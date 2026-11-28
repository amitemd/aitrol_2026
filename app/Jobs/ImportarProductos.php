<?php

namespace App\Jobs;

use Aitrol\Inventarios\Application\RepositorioInventario;
use App\Models\User;
use App\Services\LectorExcel;
use DateTimeImmutable;
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

    private const COLUMNAS = ['codigo', 'nombre', 'unidad', 'tipo_producto', 'precio_venta', 'codigo_barras', 'descripcion', 'marca', 'categoria'];

    private const RELACIONES = ['unidad' => 'unidades', 'marca' => 'marcas', 'categoria' => 'categorias'];

    public int $tries = 3;

    public int $timeout = 900;

    public array $backoff = [10, 30];

    public string $formato = 'csv';

    public function __construct(public readonly string $importacion, string $formato = 'csv')
    {
        $this->formato = $formato;
        $this->onConnection('personas');
    }

    public static function ruta(string $id, string $formato = 'csv'): string
    {
        return 'importaciones-productos/'.$id.($formato === 'xlsx' ? '.xlsx' : '.csv');
    }

    public function handle(RepositorioInventario $inventario, LectorExcel $excel): void
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
        $ruta = Storage::disk('local')->path(self::ruta($this->importacion, $this->formato));
        try {
            ['total' => $total, 'filas' => $filas] = $this->prepararArchivo($ruta, $excel);
            if ($total > 20000) {
                throw new RuntimeException('La importación admite hasta 20.000 filas.');
            }
            if ($total === 0) {
                throw new RuntimeException('El archivo no contiene filas de productos.');
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
            foreach ($filas as $indice => $fila) {
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
        $this->terminar('fallida', 'La importación se interrumpió. Puedes volver a cargar el archivo para importar las filas pendientes.');
    }

    /** @return array{total: int, filas: Generator<int, list<string|null>>} */
    private function prepararArchivo(string $ruta, LectorExcel $excel): array
    {
        if ($this->formato === 'xlsx') {
            ['total' => $total, 'filas' => $filas, 'fechasDesde1904' => $fechasDesde1904] = $excel->leer($ruta, static function (array $encabezados): array {
                $columnas = LectorExcel::mapearColumnas($encabezados, [
                    'codigo' => ['codigo'],
                    'nombre' => ['nombre'],
                    'unidad' => ['unidad'],
                    'tipo_producto' => ['tipo_de_producto', 'tipo_producto'],
                    'precio_venta' => ['precio_de_venta', 'precio_venta'],
                    'codigo_barras' => ['codigo_de_barras', 'codigo_barras'],
                    'descripcion' => ['descripcion'],
                    'marca' => ['marca'],
                    'categoria' => ['categoria'],
                    'fecha_vencimiento' => ['fecha_de_vencimiento', 'fecha_vencimiento', 'fecha_expiracion', 'fecha_de_expiracion', 'vencimiento', 'expiracion'],
                    'registro_sanitario' => ['registro_sanitario'],
                    'fabricante' => ['fabricante'],
                    'codigo_externo' => ['codigo_externo'],
                    'id_producto_externo' => ['id_de_producto_externo', 'id_producto_externo'],
                    'sistema_externo' => ['sistema_externo', 'origen_externo'],
                    'codigo_qr' => ['codigo_qr', 'qr'],
                ]);

                return array_diff(['codigo', 'nombre', 'unidad'], $columnas) === [] ? $columnas : [];
            }, 'No se encontró una hoja con Código, Nombre y Unidad. Usa la plantilla Excel de productos.', omitirVacias: true);

            return ['total' => $total, 'filas' => $this->ordenarFilasExcel($filas, $fechasDesde1904)];
        }

        $total = 0;
        foreach ($this->filasCsv($ruta) as $fila) {
            if (++$total > 20000) {
                break;
            }
        }

        return ['total' => $total, 'filas' => $this->filasCsv($ruta)];
    }

    /** @return Generator<int, list<string|null>> */
    private function ordenarFilasExcel(Generator $filas, bool $fechasDesde1904): Generator
    {
        $columnas = $this->columnas();
        foreach ($filas as $fila) {
            $vencimiento = $fila['fecha_vencimiento'] ?? '';
            if (preg_match('/\A[0-9]{1,7}(?:\.0+)?\z/', $vencimiento)) {
                $dias = (int) $vencimiento;
                if ($dias <= 2_958_465 && ($fechasDesde1904 || $dias !== 60)) {
                    $base = $fechasDesde1904 ? '1904-01-01' : ($dias < 60 ? '1899-12-31' : '1899-12-30');
                    $fila['fecha_vencimiento'] = (new DateTimeImmutable($base))->modify(sprintf('+%d days', $dias))->format('Y-m-d');
                }
            }
            yield array_map(fn (string $campo): ?string => $fila[$campo] ?? null, $columnas);
        }
    }

    /** @return Generator<int, list<string|null>> */
    private function filasCsv(string $ruta): Generator
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
        if (! in_array($encabezado, [$this->columnas(), self::COLUMNAS, array_slice(self::COLUMNAS, 0, 7)], true)) {
            throw new RuntimeException('La cabecera del CSV debe coincidir con la plantilla de productos y usar punto y coma.');
        }
        while (! $archivo->eof()) {
            $fila = $archivo->fgetcsv();
            if (is_array($fila) && array_filter($fila, fn (?string $valor): bool => trim($valor ?? '') !== '') !== []) {
                yield $fila;
            }
        }
    }

    /** @return list<string> */
    private function columnas(): array
    {
        return [...self::COLUMNAS, ...array_keys(config('inventarios.trazabilidad_producto'))];
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
            $catalogos = [];
            foreach (self::RELACIONES as $campo => $seccion) {
                $catalogos[$campo] = DB::table('inv_'.$seccion)->where('id_empresa', $tarea->id_empresa)
                    ->orderBy('activo')->orderByDesc('id')->get(['id', 'nombre', 'activo'])
                    ->keyBy(fn (stdClass $registro): string => $this->claveNombre($registro->nombre))->all();
            }
            $conteo = ['importadas' => 0, 'existentes' => 0, 'omitidas' => 0];
            foreach ($lote as $fila) {
                $conteo[$this->guardarFila((int) $tarea->id_empresa, $fila, $inventario, $catalogos)]++;
            }
            $consulta->incrementEach(
                $conteo + ['procesadas' => count($lote)], ['updated_at' => now()],
            );
        }, attempts: 3);
    }

    /** @param list<string|null> $fila */
    private function guardarFila(int $empresa, array $fila, RepositorioInventario $inventario, array &$catalogos): string
    {
        $columnas = $this->columnas();
        if (count($fila) > count($columnas) || ! mb_check_encoding($fila, 'UTF-8')) {
            return 'omitidas';
        }
        $datos = array_combine($columnas, array_pad(array_map(
            fn (?string $valor): ?string => trim($valor ?? '') === '' ? null : trim($valor), $fila,
        ), count($columnas), null));
        $datos['codigo'] = Str::upper($datos['codigo'] ?? '');
        $datos['tipo_producto'] ??= 'producto';
        foreach (array_keys(self::RELACIONES) as $campo) {
            $datos[$campo] = $datos[$campo] === null ? null : Str::squish($datos[$campo]);
        }
        if ($datos['precio_venta'] !== null) {
            $datos['precio_venta'] = str_replace(',', '.', $datos['precio_venta']);
        }
        if (preg_match('/\A[0-9]{2}\/[0-9]{2}\/[0-9]{4}\z/', $datos['fecha_vencimiento'] ?? '')) {
            $fecha = DateTimeImmutable::createFromFormat('!d/m/Y', $datos['fecha_vencimiento']);
            if ($fecha && $fecha->format('d/m/Y') === $datos['fecha_vencimiento']) {
                $datos['fecha_vencimiento'] = $fecha->format('Y-m-d');
            }
        }
        $validacion = Validator::make($datos, [
            'codigo' => ['required', 'max:60'],
            'nombre' => ['required', 'max:160'],
            'unidad' => ['required', 'max:30'],
            'marca' => ['nullable', 'max:100'],
            'categoria' => ['nullable', 'max:100'],
            'tipo_producto' => ['required', Rule::in(array_keys(config('inventarios.tipos_producto')))],
            'precio_venta' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000'],
            'codigo_barras' => ['nullable', 'max:80'],
            'descripcion' => ['nullable', 'max:500'],
        ] + config('inventarios.reglas_trazabilidad_producto'));
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
            $relaciones = DB::transaction(function () use ($empresa, $inventario, $catalogos, $validacion): array {
                $campos = $validacion->validated();
                $resueltos = [];
                foreach (self::RELACIONES as $campo => $seccion) {
                    $nombre = $campos[$campo];
                    if ($nombre === null) {
                        continue;
                    }
                    $clave = $this->claveNombre($nombre);
                    $registro = $catalogos[$campo][$clave] ?? $this->encontrarCatalogo($empresa, $seccion, $nombre, $inventario);
                    if (! $registro?->activo) {
                        throw ValidationException::withMessages([$campo => 'Selecciona un registro activo de tu empresa.']);
                    }
                    $campos['id_'.$campo] = $registro->id;
                    $resueltos[$campo] = [$clave, $registro];
                }
                $inventario->guardarCatalogo('productos', null, $campos + ['activo' => true]);

                return $resueltos;
            });
        } catch (ValidationException) {
            return 'omitidas';
        }

        foreach ($relaciones as $campo => [$clave, $registro]) {
            $catalogos[$campo][$clave] = $registro;
        }

        return 'importadas';
    }

    private function encontrarCatalogo(int $empresa, string $seccion, string $nombre, RepositorioInventario $inventario): ?stdClass
    {
        $consulta = DB::table('inv_'.$seccion)->where('id_empresa', $empresa);
        $registro = (clone $consulta)->where('nombre', $nombre)->orderByDesc('activo')->orderBy('id')->first(['id', 'nombre', 'activo']);
        if ($registro !== null || $seccion === 'categorias') {
            return $registro;
        }
        $base = Str::upper(Str::limit(Str::slug($nombre), 45, '')) ?: 'IMP-'.substr(hash('sha256', $nombre), 0, 16);
        $codigo = $base;
        for ($sufijo = 2; (clone $consulta)->where('codigo', $codigo)->exists(); $sufijo++) {
            $codigo = $base.'-'.$sufijo;
        }

        return $inventario->guardarCatalogo($seccion, null, compact('codigo', 'nombre') + ['activo' => true]);
    }

    private function claveNombre(string $nombre): string
    {
        return Str::lower(Str::squish($nombre));
    }

    private function actualizar(array $datos): void
    {
        DB::table('inv_importaciones')->where('id', $this->importacion)->update($datos + ['updated_at' => now()]);
    }

    private function terminar(string $estado, ?string $error = null): void
    {
        DB::table('inv_importaciones')->where('id', $this->importacion)->whereNotIn('estado', ['completada', 'fallida'])
            ->update(['estado' => $estado, 'error' => $error, 'updated_at' => now()]);
        Storage::disk('local')->delete(self::ruta($this->importacion, $this->formato));
    }
}
