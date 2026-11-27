<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\EmpresaActiva;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class EmpresaController
{
    public function __construct(private readonly EmpresaActiva $empresaActiva) {}

    public function index(Request $request): View
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $consulta = EmpresaModel::query()->whereKey($this->empresaActiva->id());
        $busqueda = $datos['q'] ?? '';
        if ($busqueda !== '') {
            $consulta->where(fn ($query) => $query->whereLike('razon_social', '%'.$busqueda.'%')
                ->orWhereLike('nombre_comercial', '%'.$busqueda.'%')->orWhereLike('ruc', '%'.$busqueda.'%'));
        }

        return view('configuracion::empresas', [
            'empresas' => $consulta->orderBy('razon_social')->orderBy('id')->paginate(20)->appends(['q' => $busqueda]),
        ]);
    }

    public function edit(EmpresaModel $empresa): View
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

        return view('configuracion::editar-empresa', ['seleccionada' => $empresa]);
    }

    public function detalle(EmpresaModel $empresa): View
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

        return view('partials.detalle-ficha-simple', ['titulo' => 'Detalle de empresa', 'volver' => route('configuracion.empresas.index'), 'campos' => [
            'Razón social' => $empresa->razon_social, 'Nombre comercial' => $empresa->nombre_comercial, 'RUC' => $empresa->ruc,
            'Correo electrónico' => $empresa->email, 'Teléfono' => $empresa->telefono, 'Dirección' => $empresa->direccion,
        ]]);
    }

    public function update(Request $request, EmpresaModel $empresa): RedirectResponse|JsonResponse
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

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

        return $this->resultado($request, 'Empresa actualizada.');
    }

    private function resultado(Request $request, string $mensaje, int $estado = 200): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje], $estado)
            : to_route('configuracion.empresas.index')->with('estado', $mensaje);
    }
}
