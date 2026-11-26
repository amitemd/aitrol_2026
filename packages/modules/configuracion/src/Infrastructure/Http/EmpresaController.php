<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class EmpresaController
{
    public function index(): View
    {
        return view('configuracion::empresas', [
            'empresas' => EmpresaModel::query()->orderBy('razon_social')->orderBy('id')->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'ruc' => ['required', 'string', 'digits:13', 'unique:erp_empresas,ruc'],
            'razon_social' => ['required', 'string', 'max:160'],
            'nombre_comercial' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'string', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            EmpresaModel::query()->create($datos);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['ruc' => 'Ya existe una empresa con ese RUC.']);
        }

        return to_route('configuracion.empresas.index')->with('estado', 'Empresa registrada.');
    }
}