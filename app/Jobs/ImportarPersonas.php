<?php

namespace App\Jobs;

use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use App\Services\LectorPersonasExcel;
use DateTimeImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class ImportarPersonas implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 900;

    public array $backoff = [10, 30];

    public function __construct(public readonly string $importacion)
    {
        $this->onConnection('personas');
    }

    public function handle(LectorPersonasExcel $lector): void
    {
        $tarea = DB::table('personas_importaciones')->find($this->importacion);
        if ($tarea === null || in_array($tarea->estado, ['completada', 'fallida'], true)) {
            return;
        }

        $disk = Storage::disk('local');
        $archivo = self::ruta($this->importacion);
        $this->actualizar(['estado' => 'preparando']);
        try {
            ['total' => $total, 'filas' => $filas, 'fechasDesde1904' => $fechasDesde1904] = $lector->leer($disk->path($archivo));
            if ($total === 0) {
                throw new RuntimeException('La hoja tiene encabezados, pero no contiene filas de personas.');
            }
        } catch (RuntimeException $error) {
            $this->terminar('fallida', $error->getMessage());

            return;
        }

        $procesadas = (int) $tarea->procesadas;
        $this->actualizar([
            'estado' => 'procesando', 'total' => $total,
            'inicio_progreso' => now(), 'procesadas_inicio' => $procesadas,
        ]);
        $lote = [];
        $cantidad = 0;
        foreach ($filas as $indice => $fila) {
            if ($indice < $procesadas) {
                continue;
            }
            $persona = $this->persona($fila, $tarea->id_usuario, $fechasDesde1904);
            if ($persona !== null) {
                $lote[] = $persona;
            }
            $cantidad++;
            if ($cantidad === 100) {
                $this->guardarLote($lote, $cantidad);
                $lote = [];
                $cantidad = 0;
            }
        }
        if ($cantidad > 0) {
            $this->guardarLote($lote, $cantidad);
        }

        $this->terminar('completada');
    }

    public function failed(?Throwable $error): void
    {
        $this->terminar('fallida', 'La importación se interrumpió. Puede volver a cargar el archivo para procesar las filas pendientes.');
    }

    public static function ruta(string $importacion): string
    {
        return 'importaciones-personas/'.$importacion.'.xlsx';
    }

    private function actualizar(array $datos): void
    {
        DB::table('personas_importaciones')->where('id', $this->importacion)->update($datos + ['updated_at' => now()]);
    }

    private function terminar(string $estado, ?string $error = null): void
    {
        DB::table('personas_importaciones')->where('id', $this->importacion)->whereNotIn('estado', ['completada', 'fallida'])
            ->update(['estado' => $estado, 'error' => $error, 'updated_at' => now()]);
        Storage::disk('local')->delete(self::ruta($this->importacion));
    }

    private function guardarLote(array $personas, int $cantidad): void
    {
        DB::transaction(function () use ($personas, $cantidad): void {
            $insertadas = $personas === [] ? 0 : DB::table('personas')->insertOrIgnore($personas);
            DB::table('personas_importaciones')->where('id', $this->importacion)->incrementEach([
                'procesadas' => $cantidad,
                'importadas' => $insertadas,
                'existentes' => count($personas) - $insertadas,
                'omitidas' => $cantidad - count($personas),
            ], ['updated_at' => now()]);
        }, attempts: 3);
    }

    private function persona(array $fila, ?int $usuario, bool $fechasDesde1904): ?array
    {
        $identificacion = Str::upper($fila['identificacion'] ?? '');
        $tipo = $this->tipoDocumento($fila['tipo'] ?? '', $identificacion);
        if ($tipo === null) {
            return null;
        }
        $longitud = match ($tipo) {
            TipoIdentificacion::Cedula => 10,
            TipoIdentificacion::Ruc => 13,
            TipoIdentificacion::Pasaporte => null,
        };
        if ($longitud !== null && ctype_digit($identificacion) && strlen($identificacion) === $longitud - 1) {
            $identificacion = '0'.$identificacion;
        }
        if ($longitud !== null
            ? ! ctype_digit($identificacion) || strlen($identificacion) !== $longitud || trim($identificacion, '0') === ''
            : ! preg_match('/\A[A-Z0-9][A-Z0-9.\/-]{0,29}\z/', $identificacion)) {
            return null;
        }

        $nombres = $this->campo($fila, 'nombre', 255);
        $apellidos = $this->campo($fila, 'apellido', 100);
        $partes = [];
        foreach (Persona::COMPONENTES_NOMBRE as $campo => [, $maximo]) {
            $partes[$campo] = $this->campo($fila, $campo, $maximo);
        }
        $nombres = $this->nombreAgrupado($nombres, $partes['primer_nombre'], $partes['segundo_nombre']);
        $apellidos = $this->nombreAgrupado($apellidos, $partes['primer_apellido'], $partes['segundo_apellido']);
        if ($nombres === null) {
            return null;
        }

        $ahora = now();

        return [
            'id_tipo_identificacion' => $tipo->idCatalogo(),
            'identificacion' => $identificacion,
            'nombre' => $nombres,
            'apellido' => $apellidos,
            ...$partes,
            'telefono' => $this->campo($fila, 'telefono', 100),
            'celular' => $this->campo($fila, 'celular', 100),
            'direccion' => $this->campo($fila, 'direccion', 255),
            'email' => $this->campo($fila, 'email', 500),
            'fecha_nacimiento' => $this->fecha($fila['fecha_nacimiento'] ?? '', $fechasDesde1904),
            'urbanizacion' => $this->campo($fila, 'urbanizacion', 100),
            'etapa' => $this->campo($fila, 'etapa', 100),
            'manzana' => $this->campo($fila, 'manzana', 100),
            'villa' => $this->campo($fila, 'villa', 100),
            'referencia' => $this->campo($fila, 'referencia', 1000),
            'id_usuario_creacion' => $usuario,
            'id_usuario_modificacion' => $usuario,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ];
    }

    private function tipoDocumento(string $tipo, string $identificacion): ?TipoIdentificacion
    {
        return match (Str::lower(Str::ascii($tipo))) {
            'cedula', 'c', '05', '1' => TipoIdentificacion::Cedula,
            'ruc', 'r', '04', '2' => TipoIdentificacion::Ruc,
            'pasaporte', 'p', '06', '3' => TipoIdentificacion::Pasaporte,
            '' => match (true) {
                preg_match('/\A[0-9]{9,10}\z/', $identificacion) === 1 => TipoIdentificacion::Cedula,
                preg_match('/\A[0-9]{12,13}\z/', $identificacion) === 1 => TipoIdentificacion::Ruc,
                preg_match('/\A(?=.*[A-Z])(?=.*[0-9])[A-Z0-9][A-Z0-9.\/-]{0,29}\z/', $identificacion) === 1 => TipoIdentificacion::Pasaporte,
                default => null,
            },
            default => null,
        };
    }

    private function campo(array $fila, string $campo, int $maximo): ?string
    {
        $valor = $fila[$campo] ?? '';

        return $valor === '' ? null : mb_substr($valor, 0, $maximo);
    }

    private function nombreAgrupado(?string $agrupado, ?string $primero, ?string $segundo): ?string
    {
        $especifico = trim($primero.' '.$segundo);

        return $especifico === '' ? $agrupado : $especifico;
    }

    private function fecha(string $valor, bool $fechasDesde1904): ?string
    {
        if (preg_match('/\A[0-9]{1,5}(?:\.[0-9]+)?\z/', $valor)) {
            $dias = (int) $valor;
            $base = $fechasDesde1904 ? '1904-01-01' : ($dias < 60 ? '1899-12-31' : '1899-12-30');
            $fecha = (new DateTimeImmutable($base))->modify(sprintf('+%d days', $dias))->format('Y-m-d');

            return $fecha <= today()->toDateString() ? $fecha : null;
        }
        foreach (['Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd-m-Y'] as $formato) {
            $fecha = DateTimeImmutable::createFromFormat('!'.$formato, $valor);
            if ($fecha !== false && $fecha->format($formato) === $valor && $fecha->format('Y-m-d') <= today()->toDateString()) {
                return $fecha->format('Y-m-d');
            }
        }

        return null;
    }
}
