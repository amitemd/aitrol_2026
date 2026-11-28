<?php

namespace App\Http\Controllers;

use App\Jobs\ImportarPersonas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

final class PersonaImportacionController extends Controller
{
    public function index(): View
    {
        return view('configuracion::personas-importaciones', ['tareas' => $this->tareas()]);
    }

    public function estado(): Response
    {
        return response()->view('configuracion::personas-importaciones-estado', ['tareas' => $this->tareas()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'extensions:xlsx', 'mimes:xlsx', 'max:16384'],
        ], [
            'archivo.required' => 'Seleccione el archivo Excel que desea importar.',
            'archivo.uploaded' => 'El archivo no pudo cargarse. Compruebe el límite de carga del servidor.',
            'archivo.extensions' => 'Seleccione un archivo Excel en formato .xlsx.',
            'archivo.mimes' => 'Seleccione un archivo Excel en formato .xlsx.',
            'archivo.max' => 'El archivo Excel debe pesar hasta 16 MB.',
        ]);

        $archivo = $request->file('archivo');
        $id = (string) Str::uuid();
        $ruta = ImportarPersonas::ruta($id);
        $disk = Storage::disk('local');
        if ($disk->putFileAs(dirname($ruta), $archivo, basename($ruta)) === false) {
            throw ValidationException::withMessages(['archivo' => 'No se pudo guardar el archivo. Vuelva a intentarlo.']);
        }

        try {
            DB::transaction(function () use ($request, $archivo, $id): void {
                DB::table('personas_importaciones')->insert([
                    'id' => $id,
                    'id_usuario' => $request->user()->id,
                    'archivo' => Str::limit($archivo->getClientOriginalName(), 255, ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Bus::dispatch(new ImportarPersonas($id));
            });
        } catch (Throwable $error) {
            $disk->delete($ruta);
            throw $error;
        }

        return to_route('configuracion.personas.importaciones.index')
            ->with('estado', 'Archivo recibido. Puede salir de esta pantalla y consultar el avance cuando vuelva.');
    }

    private function tareas(): array
    {
        $columnas = ['archivo', 'estado', 'total', 'procesadas', 'importadas', 'existentes', 'omitidas',
            'procesadas_inicio', 'inicio_progreso', 'updated_at', 'created_at', 'error'];
        $consulta = DB::table('personas_importaciones')->select($columnas)->latest();

        return [
            'activas' => (clone $consulta)->whereIn('estado', ['pendiente', 'preparando', 'procesando'])->get(),
            'historial' => $consulta->whereIn('estado', ['completada', 'fallida'])->limit(20)->get(),
        ];
    }
}
