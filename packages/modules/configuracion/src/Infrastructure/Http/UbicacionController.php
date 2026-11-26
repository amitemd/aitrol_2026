<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class UbicacionController
{
    public function index(): View
    {
        return view('configuracion::ubicaciones');
    }

    public function opciones(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'pais' => ['nullable', 'integer', 'exists:paises,id'],
            'provincia' => ['nullable', 'integer', 'exists:provincias,id'],
            'ciudad' => ['nullable', 'integer', 'exists:ciudades,id'],
            'nivel' => ['nullable', 'in:pais,provincia,ciudad'],
            'q' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        if (isset($datos['ciudad'])) {
            $ciudad = DB::table('ciudades')->join('provincias', 'provincias.id', '=', 'ciudades.id_provincia')
                ->join('paises', 'paises.id', '=', 'provincias.id_pais')
                ->where('ciudades.id', $datos['ciudad'])->first([
                    'ciudades.id as id_ciudad', 'ciudades.nombre as ciudad',
                    'id_provincia', 'provincias.nombre as provincia', 'id_pais', 'paises.nombre as pais',
                ]);
            $datos['pais'] = $ciudad->id_pais;
            $datos['provincia'] = $ciudad->id_provincia;
        }

        if (isset($datos['nivel'])) {
            return $this->buscar($datos);
        }

        if ($request->boolean('seleccion')) {
            return response()->json(isset($ciudad) ? [
                'pais' => ['id' => $ciudad->id_pais, 'text' => $ciudad->pais],
                'provincia' => ['id' => $ciudad->id_provincia, 'text' => $ciudad->provincia],
                'ciudad' => ['id' => $ciudad->id_ciudad, 'text' => $ciudad->ciudad],
            ] : [
                'pais' => isset($datos['pais'])
                    ? DB::table('paises')->where('id', $datos['pais'])->first(['id', 'nombre as text'])
                    : null,
            ]);
        }

        return response()->json([
            'pais' => $datos['pais'] ?? null,
            'provincia' => $datos['provincia'] ?? null,
            'paises' => DB::table('paises')->orderBy('nombre')->get(['id', 'nombre']),
            'provincias' => isset($datos['pais'])
                ? DB::table('provincias')->where('id_pais', $datos['pais'])->orderBy('nombre')->get(['id', 'nombre'])
                : [],
            'ciudades' => isset($datos['provincia'])
                ? DB::table('ciudades')->where('id_provincia', $datos['provincia'])->orderBy('nombre')->get(['id', 'nombre'])
                : [],
        ]);
    }

    private function buscar(array $datos): JsonResponse
    {
        $consulta = match ($datos['nivel']) {
            'pais' => DB::table('paises'),
            'provincia' => DB::table('provincias')->where('id_pais', $datos['pais'] ?? 0),
            'ciudad' => DB::table('ciudades')->where('id_provincia', $datos['provincia'] ?? 0),
        };
        if (isset($datos['q'])) {
            $consulta->whereLike('nombre', '%'.$datos['q'].'%');
        }

        $pagina = $consulta->orderBy('nombre')->orderBy('id')
            ->simplePaginate(30, ['id', 'nombre as text'], 'page', $datos['page'] ?? 1);

        return response()->json(['results' => $pagina->items(), 'pagination' => ['more' => $pagina->hasMorePages()]]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tipo' => ['required', 'in:pais,provincia,ciudad'],
            'nombre' => ['required', 'string', 'max:120'],
            'codigo' => ['exclude_unless:tipo,pais', 'required', 'alpha:ascii', 'size:2'],
            'id_pais' => ['exclude_unless:tipo,provincia', 'required', 'integer', 'exists:paises,id'],
            'id_provincia' => ['exclude_unless:tipo,ciudad', 'required', 'integer', 'exists:provincias,id'],
        ]);

        try {
            match ($datos['tipo']) {
                'pais' => DB::table('paises')->insert(['codigo' => Str::upper($datos['codigo']), 'nombre' => $datos['nombre']]),
                'provincia' => DB::table('provincias')->insert(['id_pais' => $datos['id_pais'], 'nombre' => $datos['nombre']]),
                'ciudad' => DB::table('ciudades')->insert(['id_provincia' => $datos['id_provincia'], 'nombre' => $datos['nombre']]),
            };
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['nombre' => 'Ya existe una ubicación con ese nombre o código.']);
        }

        return to_route('configuracion.ubicaciones.index')->with('estado', 'Ubicación registrada.');
    }
}
