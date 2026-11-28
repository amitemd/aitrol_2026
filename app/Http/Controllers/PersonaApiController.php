<?php

namespace App\Http\Controllers;

use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Http\Resources\PersonaResource;
use App\Models\Persona;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PersonaApiController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $datos = $request->validate([
            'identificacion' => ['bail', 'required', 'string', 'regex:/\A[0-9]{10}\z/'],
        ], [
            'identificacion.required' => 'Ingrese la cédula que desea consultar.',
            'identificacion.string' => 'Envíe la cédula como texto para conservar los ceros iniciales.',
            'identificacion.regex' => 'La cédula debe tener exactamente 10 dígitos.',
        ]);

        $persona = Persona::query()->where('identificacion', $datos['identificacion'])
            ->where('id_tipo_identificacion', TipoIdentificacion::Cedula->idCatalogo())
            ->first();

        if ($persona === null) {
            return response()->json(['message' => 'No se encontró una persona con esa cédula.'], 404);
        }

        return (new PersonaResource($persona))->response()->header('Cache-Control', 'private, no-store');
    }
}
