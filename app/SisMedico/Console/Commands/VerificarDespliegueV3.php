<?php

namespace Sis_medico\Console\Commands;

use Illuminate\Console\Command;
use Sis_medico\v3\contable\PoliticaDespliegue;

class VerificarDespliegueV3 extends Command
{
    protected $signature = 'v3:deploy-check {--json : Devuelve el resultado en formato JSON}';

    protected $description = 'Verifica requisitos de despliegue v3 sin instalar ni modificar archivos';

    public function handle()
    {
        $resultados = [];
        $versionLaravel = app()->version();
        $versiones = PoliticaDespliegue::evaluarVersiones(PHP_VERSION, $versionLaravel);

        $resultados[] = $this->resultado(
            'PHP',
            $versiones['php'],
            PHP_VERSION . ' / esperado ' . PoliticaDespliegue::versionPhpEsperada()
        );
        $resultados[] = $this->resultado(
            'Laravel',
            $versiones['laravel'],
            $versionLaravel . ' / esperado 5.4.x'
        );

        foreach (PoliticaDespliegue::componentesServidor() as $componente) {
            $ruta = base_path($componente['ruta']);
            $existe = $componente['tipo'] === 'archivo' ? is_file($ruta) : is_dir($ruta);
            $resultados[] = $this->resultado($componente['nombre'], $existe, $componente['ruta']);
        }

        $almacenamiento = storage_path();
        $escribible = is_dir($almacenamiento) && is_writable($almacenamiento);
        $resultados[] = $this->resultado('Escritura storage', $escribible, $escribible ? 'Disponible' : 'No disponible');

        $debugSeguro = !config('app.debug');
        $resultados[] = $this->resultado('APP_DEBUG', $debugSeguro, $debugSeguro ? 'Desactivado' : 'Activo');

        $correctas = 0;
        foreach ($resultados as $resultado) {
            if ($resultado['correcto']) {
                $correctas++;
            }
        }

        $aprobado = $correctas === count($resultados);
        $salida = [
            'estado' => $aprobado ? 'aprobado' : 'no_aprobado',
            'solo_lectura' => true,
            'fecha' => date('c'),
            'correctas' => $correctas,
            'total' => count($resultados),
            'verificaciones' => $resultados,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($salida));
        } else {
            $filas = [];
            foreach ($resultados as $resultado) {
                $filas[] = [
                    $resultado['componente'],
                    $resultado['correcto'] ? 'OK' : 'ATENCION',
                    $resultado['detalle'],
                ];
            }
            $this->table(['Componente', 'Estado', 'Detalle'], $filas);
            $this->line('Resultado: ' . strtoupper($salida['estado']) . ' (' . $correctas . '/' . count($resultados) . ')');
            $this->comment('Este comando no instala, copia, elimina ni modifica archivos.');
        }

        return $aprobado ? 0 : 1;
    }

    private function resultado($componente, $correcto, $detalle)
    {
        return [
            'componente' => $componente,
            'correcto' => (bool) $correcto,
            'detalle' => $detalle,
        ];
    }
}
