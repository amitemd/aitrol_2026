<?php

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

final class CatalogoGeoNames
{
    public function importar(?string $directorio, bool $completo): array
    {
        $base = $directorio ?? storage_path('app/private/geonames');
        $ciudades = $completo ? 'allCountries' : 'cities500';
        foreach (['countryInfo.txt', 'admin1CodesASCII.txt', $ciudades.'.zip'] as $archivo) {
            if ($directorio === null) {
                $this->descargar($base, $archivo);
            }
            if (! is_readable($base.'/'.$archivo)) {
                throw new RuntimeException('Falta el archivo '.$archivo.'.');
            }
        }

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Activa la extensión ZIP de PHP.');
        }
        $zip = new ZipArchive;
        if ($zip->open($base.'/'.$ciudades.'.zip') !== true) {
            throw new RuntimeException('El ZIP de ciudades no es válido.');
        }

        try {
            $paises = $this->paises($base.'/countryInfo.txt');
            $provincias = $this->provincias($base.'/admin1CodesASCII.txt', $paises);
            $lote = [];
            foreach ($this->filas($zip->getStream($ciudades.'.txt')) as $fila) {
                $codigo = $fila[8] ?? '';
                $pais = $paises[$codigo] ?? null;
                if ($pais === null || ($fila[6] ?? null) !== 'P' || empty($fila[1])) {
                    continue;
                }

                $provincia = $provincias[$codigo.'.'.($fila[10] ?? '')] ?? ($provincias[$codigo.'.??'] ??= $this->provincia($pais, 'Sin división administrativa'));
                $lote[] = ['id_provincia' => $provincia, 'nombre' => mb_substr($fila[1], 0, 120)];
                if (count($lote) === 500) {
                    DB::table('ciudades')->insertOrIgnore($lote);
                    $lote = [];
                }
            }
            DB::table('ciudades')->insertOrIgnore($lote);
        } finally {
            $zip->close();
        }

        return array_map(fn ($tabla) => DB::table($tabla)->count(), ['paises', 'provincias', 'ciudades']);
    }

    private function paises(string $ruta): array
    {
        $paises = DB::table('paises')->pluck('id', 'codigo')->all();
        foreach ($this->filas(fopen($ruta, 'rb')) as $fila) {
            [$codigo, , , , $nombre] = array_pad($fila, 5, null);
            if (strlen($codigo) !== 2 || ! $nombre || isset($paises[$codigo])) {
                continue;
            }
            if (DB::table('paises')->where('nombre', $nombre)->exists()) {
                $nombre .= ' ('.$codigo.')';
            }
            DB::table('paises')->insertOrIgnore(['codigo' => $codigo, 'nombre' => mb_substr($nombre, 0, 120)]);
            $paises[$codigo] = DB::table('paises')->where('codigo', $codigo)->value('id');
        }

        return $paises;
    }

    private function provincias(string $ruta, array $paises): array
    {
        $existentes = [];
        foreach (DB::table('provincias')->get() as $estado) {
            $existentes[$estado->id_pais][$this->clave($estado->nombre)] = $estado->id;
        }

        $codigos = [];
        foreach ($this->filas(fopen($ruta, 'rb')) as $fila) {
            [$codigo, $nombre] = array_pad($fila, 2, null);
            $pais = $paises[substr($codigo, 0, 2)] ?? null;
            if ($pais !== null && $nombre) {
                $codigos[$codigo] = $existentes[$pais][$this->clave($nombre)] ??= $this->provincia($pais, $nombre);
            }
        }

        return $codigos;
    }

    private function provincia(int $pais, string $nombre): int
    {
        $datos = ['id_pais' => $pais, 'nombre' => mb_substr($nombre, 0, 120)];
        DB::table('provincias')->insertOrIgnore($datos);

        return DB::table('provincias')->where($datos)->value('id');
    }

    private function descargar(string $base, string $archivo): void
    {
        File::ensureDirectoryExists($base);
        $ruta = $base.'/'.$archivo;
        if (is_file($ruta) && filesize($ruta)) {
            return;
        }

        $temporal = $ruta.'.'.Str::uuid().'.tmp';
        try {
            $respuesta = Http::connectTimeout(20)->timeout(1200)->sink($temporal)
                ->get('https://download.geonames.org/export/dump/'.$archivo);
            if (! $respuesta->successful() || ! filesize($temporal) || ! rename($temporal, $ruta)) {
                throw new RuntimeException('No se pudo descargar '.$archivo.' de GeoNames.');
            }
        } finally {
            File::delete($temporal);
        }
    }

    private function filas($archivo): Generator
    {
        if ($archivo === false) {
            throw new RuntimeException('No se pudo leer un archivo del catálogo.');
        }

        try {
            while (($linea = fgets($archivo)) !== false) {
                $linea = rtrim($linea, "\r\n");
                if ($linea !== '' && ! str_starts_with($linea, '#')) {
                    yield explode("\t", $linea);
                }
            }
        } finally {
            fclose($archivo);
        }
    }

    private function clave(string $nombre): string
    {
        return trim(preg_replace('/^provincia (de |del )?| province$/', '', Str::lower(Str::ascii($nombre))));
    }
}