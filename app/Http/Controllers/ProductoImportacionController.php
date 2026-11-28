<?php

namespace App\Http\Controllers;

use App\Jobs\ImportarProductos;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

final class ProductoImportacionController extends Controller
{
    public function __construct(private readonly EmpresaActiva $empresa) {}

    public function index(): View
    {
        return view('configuracion::productos-importaciones', ['tareas' => $this->tareas()]);
    }

    public function estado(): Response
    {
        return response()->view('configuracion::importaciones-estado', ['tareas' => $this->tareas()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function plantilla(): BinaryFileResponse
    {
        return response()->download(base_path('packages/modules/inventarios/resources/plantilla-productos.xlsx'),
            'plantilla-productos.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function show(string $importacion): JsonResponse
    {
        $estado = $this->consulta()->where('id', $importacion)->first();
        abort_if($estado === null, 404);

        return response()->json(['data' => $estado])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'extensions:xlsx,csv', 'mimes:xlsx,csv,txt', 'max:4096'],
        ], [
            'archivo.required' => 'Selecciona el archivo de productos.',
            'archivo.extensions' => 'Selecciona un archivo Excel .xlsx o CSV .csv.',
            'archivo.mimes' => 'El archivo debe ser un libro Excel .xlsx o un CSV de texto.',
            'archivo.max' => 'El archivo debe pesar hasta 4 MB.',
        ]);

        $archivo = $request->file('archivo');
        $id = (string) Str::uuid();
        $formato = strtolower($archivo->getClientOriginalExtension());
        $ruta = ImportarProductos::ruta($id, $formato);
        $disk = Storage::disk('local');
        if ($disk->putFileAs(dirname($ruta), $archivo, basename($ruta)) === false) {
            throw ValidationException::withMessages(['archivo' => 'No se pudo guardar el archivo. Vuelve a intentarlo.']);
        }

        try {
            DB::transaction(function () use ($request, $archivo, $id, $formato): void {
                DB::table('inv_importaciones')->insert([
                    'id' => $id, 'id_empresa' => $this->empresa->id(), 'id_usuario' => $request->user()->id,
                    'archivo' => Str::limit($archivo->getClientOriginalName(), 255, ''),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                Bus::dispatch(new ImportarProductos($id, $formato));
            });
        } catch (Throwable $error) {
            $disk->delete($ruta);
            throw $error;
        }

        if ($request->routeIs('api.*')) {
            $estado = route('api.v1.inventarios.productos.importaciones.show', $id);

            return response()->json(['data' => ['id' => $id, 'estado' => 'pendiente', 'url_estado' => $estado]], 202)
                ->header('Location', $estado);
        }

        return to_route('configuracion.importaciones.productos.index')
            ->with('estado', 'Archivo recibido. Puedes consultar el avance en el historial.');
    }

    private function consulta(): Builder
    {
        return DB::table('inv_importaciones')->where('id_empresa', $this->empresa->id())
            ->select('id', 'archivo', 'estado', 'total', 'procesadas', 'importadas', 'existentes', 'omitidas',
                'procesadas_inicio', 'inicio_progreso', 'updated_at', 'created_at', 'error');
    }

    private function tareas(): array
    {
        $consulta = $this->consulta()->latest();

        return [
            'activas' => (clone $consulta)->whereIn('estado', ['pendiente', 'preparando', 'procesando'])->get(),
            'historial' => $consulta->whereIn('estado', ['completada', 'fallida'])->limit(20)->get(),
        ];
    }
}
