<?php

namespace App\Http\Controllers;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use App\Services\EmpresaActiva;
use App\Services\PlanCuentasInicial;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class EmpresaInicialController
{
    public function store(Request $request, EmpresaActiva $empresaActiva, PlanCuentasInicial $planCuentas): RedirectResponse
    {
        if ($request->user()->id_empresa !== null) return to_route('dashboard');

        $datos = $request->validate([
            'ruc' => ['required', 'digits:13', Rule::unique('erp_empresas')],
            'razon_social' => ['required', 'string', 'max:160'],
            'nombre_comercial' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $usuario = DB::transaction(function () use ($request, $datos, $empresaActiva, $planCuentas): User {
                $usuario = User::query()->lockForUpdate()->findOrFail($request->user()->id);
                if ($usuario->id_empresa !== null) {
                    return $usuario;
                }

                $empresa = EmpresaModel::query()->create($datos);
                $empresaActiva->prepararPermisos($empresa->id);
                $planCuentas->copiarA($empresa->id, $usuario->id);
                $usuario->empresa()->associate($empresa);
                $usuario->is_admin = true;
                $usuario->save();

                return $usuario;
            }, 3);
        } catch (UniqueConstraintViolationException $error) {
            throw ValidationException::withMessages(['ruc' => 'Ya existe una empresa con ese RUC.']);
        }

        Auth::setUser($usuario);
        $request->session()->regenerate();

        return to_route('dashboard');
    }
}
