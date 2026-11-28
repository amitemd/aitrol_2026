<?php

namespace App\Http\Controllers;

use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class PersonaFormularioController extends Controller
{
    public function __invoke(Request $request, string $formulario): JsonResponse
    {
        $permiso = match ($formulario) {
            'clientes' => 'clientes.lista',
            'proveedores' => 'proveedores.lista',
            'empleados' => 'rrhh.empleados.index',
            'usuarios' => null,
        };
        if ($permiso === null) {
            Gate::authorize('administrar-configuracion');
        } else {
            abort_unless(Gate::allows('permiso', [$permiso, 'escribir']) || Gate::allows('permiso', [$permiso, 'actualizar']), 403);
        }

        $datos = $request->validate([
            'q' => ['required', 'string', 'regex:/\A[A-Za-z0-9][A-Za-z0-9-]{2,29}\z/'],
            'tipo' => ['required_if:exacta,true', 'nullable', Rule::enum(TipoIdentificacion::class)],
            'exacta' => ['sometimes', 'boolean'],
        ]);
        $termino = strtoupper($datos['q']);
        $tipo = isset($datos['tipo']) ? TipoIdentificacion::from($datos['tipo'])->idCatalogo() : null;
        $consulta = Persona::query()->whereIn('id_tipo_identificacion', $tipo === null ? [1, 2, 3] : [$tipo]);
        $cabeceras = ['Cache-Control' => 'private, no-store'];

        if ($datos['exacta'] ?? false) {
            $persona = $consulta->where('identificacion', $termino)->first();
            abort_if($persona === null, 404);

            return response()->json(['data' => [
                'identificacion' => $persona->identificacion,
                'tipo_identificacion' => TipoIdentificacion::desdeCatalogo((int) $persona->id_tipo_identificacion)->value,
                'nombres' => $persona->nombre,
                'apellidos' => $persona->apellido,
                'nombre' => $persona->nombreCompleto(),
                ...$persona->only(['email', 'telefono', 'direccion']),
                ...$persona->perfil(),
            ]], 200, $cabeceras);
        }

        $resultados = $consulta->whereLike('identificacion', $termino.'%')
            ->orderBy('identificacion')->orderBy('id_tipo_identificacion')->limit(8)
            ->get(['identificacion', 'id_tipo_identificacion', 'nombre', 'apellido'])
            ->map(fn (Persona $persona): array => [
                'identificacion' => $persona->identificacion,
                'tipo_identificacion' => TipoIdentificacion::desdeCatalogo((int) $persona->id_tipo_identificacion)->value,
                'nombre' => $persona->nombreCompleto(),
            ]);

        return response()->json(['data' => $resultados], 200, $cabeceras);
    }
}
