<?php

namespace App\Jobs;

use Aitrol\Inventarios\Application\RepositorioInventario;
use App\Models\User;
use Generator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SplFileObject;
use stdClass;
use Throwable;

final class ImportarProductos implements ShouldQueue
{
    use Queueable;

    public const COLUMNAS = ['codigo', 'nombre', 'unidad', 'tipo_producto', 'precio_venta', 'codigo_barras', 'descripcion'];

    public int $tries = 3;

    public int $timeout = 900;

    public array $backoff = [10, 30];

    public function __construct(public readonly string $importacion)
    {
        $this->onConnection('personas');
    }

    public static function ruta(string $id): string
    {
        return 'importaciones-productos/'.$id.'.csv';
    }

    public function handle(RepositorioInventario $inventario): void
    {
        $tarea = DB::table('inv_importaciones')->find($this->importacion);
        if ($tarea === null || in_array($tarea->estado, ['completada', 'fallida'], true)) {
            return;
        }

        $usuario = User::query()->find($tarea->id_usuario);
        if (! $usuario?->activo || ! $usuario->is_admin || (int) $usuario->id_empresa !== (int) $tarea->id_empresa
            || ! $usuario->empresa()->where('activo', true)->exists()) {
            $this->terminar('fallida', 'El usuario ya no está autorizado para importar en esta empresa.');

            return;
        }

        $this->actualizar(['estado' => 'preparando']);
        $ruta = Storage::disk('local')->path(self::ruta($this->importacion));
        try {
            $total = 0;
            foreach ($this->filas($ruta) as $fila) {
                if (++$total > 20000) {
                    throw new RuntimeException('El CSV admite hasta 20.000 filas.');
                }
            }
            if ($total === 0) {
                throw new RuntimeException('El CSV no contiene filas de productos.');
            }
        } catch (RuntimeException $error) {
            $this->terminar('fallida', $error->getMessage());

            return;
        }

        $procesadas = (int) $tarea->procesadas;
        $this->actualizar([
            'estado' => 'procesando', 'total' => $total,
            'procesadas_inicio' => $procesadas, 'inicio_progreso' => now(),
        ]);
        $guardia = Auth::guard('web');
        $guardiaAnterior = Auth::getDefaultDriver();
        $usuarioAnterior = $guardia->user();
        Auth::shouldUse('web');
        $guardia->setUser($usuario);
        try {
            $lote = [];
            $inicioLote = $procesadas;
            foreach ($this->filas($ruta) as $indice => $fila) {
                if ($indice < $procesadas) {
                    continue;
                }
                $lote[] = $fila;
                if (count($lote) === 25) {
                    $this->guardarLote($tarea, $lote, $inicioLote, $inventario);
                    $inicioLote += count($lote);
                    $lote = [];
                }
            }
            if ($lote !== []) {
                $this->guardarLote($tarea, $lote, $inicioLote, $inventario);
            }
            $this->terminar('completada');
        } finally {
            if ($usuarioAnterior === null) {
                $guardia->forgetUser();
            } else {
                $guardia->setUser($usuarioAnterior);
            }
            Auth::shouldUse($guardiaAnterior);
        }
    }

    public function failed(?Throwable $error): void
    {
        $this->terminar('fallida', 'La importación se interrumpió. Puedes volver a cargar el CSV para importar las filas pendientes.');
    }

    /** @return Generator<int, list<string|null>> */
    private function filas(string $ruta): Generator
    {
        if (! is_file($ruta)) {
            throw new RuntimeException('El archivo de importación ya no está disponible.');
        }
        $archivo = new SplFileObject($ruta);
        $archivo->setCsvControl(';', '"', '');
        $encabezado = $archivo->fgetcsv();
        if (is_array($encabezado)) {
            $encabezado[0] = Str::chopStart($encabezado[0] ?? '', "\xEF\xBB\xBF");
            $encabezado = array_map(fn (?string $columna): string => Str::lower(trim($columna ?? '')), $encabezado);
        }
        if ($encabezado !== self::COLUMNAS) {
            throw new RuntimeException('La cabecera del CSV debe coincidir con la plantilla de productos y usar punto y coma.');
        }
        while (! $archivo->eof()) {
            $fila = $archivo->fgetcsv();
            if (is_array($fila) && array_filter($fila, fn (?string $valor): bool => trim($valor ?? '') !== '') !== []) {
                yield $fila;
            }
        }
    }

    /** @param list<list<string|null>> $lote */
    private function guardarLote(stdClass $tarea, array $lote, int $inicioLote, RepositorioInventario $inventario): void
    {
        DB::transaction(function () use ($tarea, $lote, $inicioLote, $inventario): void {
            DB::table('erp_empresas')->where('id', $tarea->id_empresa)->lockForUpdate()->first(['id']);
            $consulta = DB::table('inv_importaciones')->where('id', $this->importacion);
            $avance = (clone $consulta)->lockForUpdate()->first(['estado', 'procesadas']);
            if ($avance === null || $avance->estado !== 'procesando' || (int) $avance->procesadas !== $inicioLote) {
                return;
            }
            $conteo = ['importadas' => 0, 'existentes' => 0, 'omitidas' => 0];
            foreach ($lote as $fila) {
                $conteo[$this->guardarFila((int) $tarea->id_empresa, $fila, $inventario)]++;
            }
            $consulta->incrementEach(
                $conteo + ['procesadas' => count($lote)], ['updated_at' => now()],
            );
        }, attempts: 3);
    }

    /** @param list<string|null> $fila */
    private function guardarFila(int $empresa, array $fila, RepositorioInventario $inventario): string
    {
        if (count($fila) > count(self::COLUMNAS) || ! mb_check_encoding($fila, 'UTF-8')) {
            return 'omitidas';
        }
        $datos = array_combine(self::COLUMNAS, array_pad(array_map(
            fn (?string $valor): ?string => trim($valor ?? '') === '' ? null : trim($valor), $fila,
        ), count(self::COLUMNAS), null));
        $datos['codigo'] = Str::upper($datos['codigo'] ?? '');
        $datos['tipo_producto'] ??= 'producto';
        if ($datos['precio_venta'] !== null) {
            $datos['precio_venta'] = str_replace(',', '.', $datos['precio_venta']);
        }
        $validacion = Validator::make($datos, [
            'codigo' => ['required', 'max:60'],
            'nombre' => ['required', 'max:160'],
            'unidad' => ['required', 'max:30'],
            'tipo_producto' => ['required', Rule::in(array_keys(config('inventarios.tipos_producto')))],
            'precio_venta' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000'],
            'codigo_barras' => ['nullable', 'max:80'],
            'descripcion' => ['nullable', 'max:500'],
        ]);
        if ($validacion->fails()) {
            return 'omitidas';
        }
        $productos = DB::table('inv_productos')->where('id_empresa', $empresa);
        if ((clone $productos)->where('codigo', $datos['codigo'])->exists()) {
            return 'existentes';
        }
        if ($datos['codigo_barras'] !== null && $productos->where('codigo_barras', $datos['codigo_barras'])->exists()) {
            return 'omitidas';
        }
        try {
            $inventario->guardarCatalogo('productos', null, $validacion->validated() + ['activo' => true]);
        } catch (ValidationException) {
            return 'omitidas';
        }

        return 'importadas';
    }

    private function actualizar(array $datos): void
    {
        DB::table('inv_importaciones')->where('id', $this->importacion)->update($datos + ['updated_at' => now()]);
    }

    private function terminar(string $estado, ?string $error = null): void
    {
        DB::table('inv_importaciones')->where('id', $this->importacion)->whereNotIn('estado', ['completada', 'fallida'])
            ->update(['estado' => $estado, 'error' => $error, 'updated_at' => now()]);
        Storage::disk('local')->delete(self::ruta($this->importacion));
    }
}
