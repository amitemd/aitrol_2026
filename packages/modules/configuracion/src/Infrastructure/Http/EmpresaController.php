<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class EmpresaController
{
    public function index(Request $request): View
    {
        if ($request->boolean('crear')) {
            Gate::authorize('permiso', ['empresas', 'escribir']);
        }
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $consulta = EmpresaModel::query();
        $busqueda = $datos['q'] ?? '';
        if ($busqueda !== '') {
            $consulta->where(fn ($query) => $query->whereLike('razon_social', '%'.$busqueda.'%')
                ->orWhereLike('nombre_comercial', '%'.$busqueda.'%')->orWhereLike('ruc', '%'.$busqueda.'%'));
        }

        return view('configuracion::empresas', [
            'empresas' => $consulta->orderBy('razon_social')->orderBy('id')->paginate(20)->appends(['q' => $busqueda]),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->guardar($request, new EmpresaModel);
    }

    public function edit(EmpresaModel $empresa): View
    {
        return view('configuracion::editar-empresa', ['seleccionada' => $empresa]);
    }

    public function update(Request $request, EmpresaModel $empresa): RedirectResponse|JsonResponse
    {
        return $this->guardar($request, $empresa);
    }

    public function destroy(Request $request, EmpresaModel $empresa): RedirectResponse|JsonResponse
    {
        $empresa->delete();

        return $this->resultado($request, 'Empresa eliminada del listado.');
    }

    private function guardar(Request $request, EmpresaModel $empresa): RedirectResponse|JsonResponse
    {
        $nuevo = ! $empresa->exists;
        $datos = $request->validate([
            'ruc' => ['required', 'string', 'digits:13', Rule::unique('erp_empresas', 'ruc')->ignore($empresa)],
            'razon_social' => ['required', 'string', 'max:160'],
            'nombre_comercial' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'string', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $empresa->fill($datos)->save();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['ruc' => 'Ya existe una empresa con ese RUC.']);
        }

        return $this->resultado($request, $nuevo ? 'Empresa registrada.' : 'Empresa actualizada.', $nuevo ? 201 : 200);
    }

    private function resultado(Request $request, string $mensaje, int $estado = 200): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje], $estado)
            : to_route('configuracion.empresas.index')->with('estado', $mensaje);
    }
}
