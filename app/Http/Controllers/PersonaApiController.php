<?php

namespace App\Http\Controllers;

use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Http\Resources\PersonaResource;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

final class PersonaApiController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $datos = $request->validate([
            'identificacion' => ['required', 'string', 'max:30'],
            'tipo_identificacion' => ['nullable', Rule::enum(TipoIdentificacion::class)],
        ], [
            'identificacion.required' => 'Ingrese la identificación que desea consultar.',
            'identificacion.string' => 'Envíe la identificación como texto para conservar los ceros iniciales.',
            'identificacion.max' => 'La identificación admite hasta 30 caracteres.',
        ]);

        $consulta = Persona::query()->where('identificacion', mb_strtoupper($datos['identificacion']))
            ->whereNotNull('id_tipo_identificacion');
        if (isset($datos['tipo_identificacion'])) {
            $consulta->where('id_tipo_identificacion', TipoIdentificacion::from($datos['tipo_identificacion'])->idCatalogo());
        }

        $personas = $consulta->limit(2)->get();
        if ($personas->isEmpty()) {
            return response()->json(['message' => 'No se encontró una persona con esa identificación.'], 404);
        }
        if ($personas->count() > 1) {
            return response()->json(['message' => 'La identificación coincide con más de un tipo de documento. Envíe tipo_identificacion.'], 409);
        }

        return (new PersonaResource($personas->first()))->response()->header('Cache-Control', 'private, no-store');
    }
}
