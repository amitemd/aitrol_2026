<?php

namespace Sis_medico\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\v3\contable\Apis\ApiInicioController;

class VerificarApiTributariaV3 extends Command
{
    protected $signature = 'v3:api-check {--json : Devuelve el resultado en formato JSON}';

    protected $description = 'Certifica en solo lectura el contrato y aislamiento de la API tributaria v3';

    public function handle()
    {
        $resultados = [];
        $tablas = $this->documentos();

        $resultados[] = $this->verificarRuta();
        $resultados[] = $this->verificarContrato();

        $baseDisponible = true;
        try {
            DB::connection()->getPdo();
            $resultados[] = $this->resultado('mysql', true, 'Conexion disponible');
        } catch (\Exception $e) {
            $baseDisponible = false;
            $resultados[] = $this->resultado('mysql', false, 'Conexion no disponible');
        }

        if ($baseDisponible) {
            $resultadoEsquema = $this->verificarEsquema($tablas);
            $resultados[] = $resultadoEsquema;

            if ($resultadoEsquema['correcto']) {
                $resultados[] = $this->verificarMaestros($tablas);
                $resultados[] = $this->verificarEmpresas($tablas);
                $resultados[] = $this->verificarAislamiento($tablas);
            }
        }

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
            $this->comment('No genera XML, no firma, no envia correos y no contacta al SRI.');
        }

        return $aprobado ? 0 : 1;
    }

    private function verificarRuta()
    {
        try {
            $ruta = app('router')->getRoutes()->match(Request::create('/api/v1/tributario', 'GET'));
            $middlewares = $ruta->gatherMiddleware();
            $correcto = in_array('general', $middlewares) && in_array('operacion.v3', $middlewares);
            return $this->resultado('ruta_api', $correcto, $correcto ? 'GET protegido por sesion y monitoreo' : 'Faltan middleware requeridos');
        } catch (\Exception $e) {
            return $this->resultado('ruta_api', false, 'Ruta no disponible');
        }
    }

    private function verificarContrato()
    {
        $esperadas = [
            'facturas', 'link_factura', 'emitido_factura',
            'retenciones', 'link_retencion', 'emitido_retencion',
            'liquidaciones', 'link_liquidaciones',
            'nota_credito', 'link_nota_credito',
            'guia_remision', 'link_guia_remision',
        ];
        $correcto = ApiInicioController::CLAVES_CONTRATO === $esperadas;
        return $this->resultado('contrato', $correcto, $correcto ? '12 claves publicas estables' : 'Contrato modificado');
    }

    private function verificarEsquema(array $tablas)
    {
        $schema = DB::getSchemaBuilder();
        $requeridas = [
            'de_documentos_electronicos' => ['id', 'id_documento', 'id_maestro_documento', 'ruc_emisor'],
            'de_maestro_documentos' => ['id', 'codigo'],
        ];
        foreach ($tablas as $documento) {
            $requeridas[$documento['tabla']] = ['id', 'id_empresa'];
        }

        foreach ($requeridas as $tabla => $columnas) {
            if (!$schema->hasTable($tabla)) {
                return $this->resultado('esquema_api', false, 'Falta tabla ' . $tabla);
            }
            foreach ($columnas as $columna) {
                if (!$schema->hasColumn($tabla, $columna)) {
                    return $this->resultado('esquema_api', false, 'Falta columna ' . $tabla . '.' . $columna);
                }
            }
        }

        return $this->resultado('esquema_api', true, 'Tablas y columnas disponibles');
    }

    private function verificarMaestros(array $tablas)
    {
        $codigos = array_keys($tablas);
        $encontrados = DB::table('de_maestro_documentos')
            ->whereIn('codigo', $codigos)
            ->distinct()
            ->pluck('codigo')
            ->all();
        $faltantes = array_diff($codigos, $encontrados);

        return $this->resultado(
            'maestros_sri',
            count($faltantes) === 0,
            count($faltantes) === 0 ? 'Codigos 01, 03, 04, 06 y 07 disponibles' : 'Faltan codigos: ' . implode(', ', $faltantes)
        );
    }

    private function verificarEmpresas(array $tablas)
    {
        $empresas = $this->empresasMuestra($tablas);

        return $this->resultado(
            'muestra_empresas',
            count($empresas) >= 2,
            count($empresas) >= 2 ? 'Existen al menos dos empresas para certificar aislamiento' : 'Se requieren documentos de dos empresas'
        );
    }

    private function verificarAislamiento(array $tablas)
    {
        $empresas = array_slice($this->empresasMuestra($tablas), 0, 2);
        $empresasVerificadas = [];

        foreach ($tablas as $codigo => $documento) {
            $maestro = DB::table('de_maestro_documentos')->where('codigo', $codigo)->value('id');
            if ($maestro === null) {
                return $this->resultado('aislamiento_api', false, 'No se pudo resolver maestro ' . $codigo);
            }

            foreach ($empresas as $empresa) {
                $origen = DB::table($documento['tabla'] . ' as origen')
                    ->where('origen.id_empresa', $empresa)
                    ->select(['origen.id', 'origen.id_empresa'])
                    ->first();
                if ($origen === null) {
                    continue;
                }
                $empresasVerificadas[] = $origen->id_empresa;
                $incorrecto = DB::table('de_documentos_electronicos')
                    ->where('id_documento', $origen->id)
                    ->where('id_maestro_documento', $maestro)
                    ->where('ruc_emisor', '<>', $origen->id_empresa)
                    ->exists();
                if ($incorrecto) {
                    return $this->resultado('aislamiento_api', false, 'Existe una colision historica de empresa para el tipo ' . $codigo);
                }
            }
        }

        $empresasVerificadas = array_unique($empresasVerificadas);
        if (count($empresasVerificadas) < 2) {
            return $this->resultado('aislamiento_api', false, 'No fue posible tomar documentos de dos empresas');
        }

        return $this->resultado('aislamiento_api', true, 'Dos empresas sin cruces de emisor ni tipo documental');
    }

    private function empresasMuestra(array $tablas)
    {
        $empresas = [];
        foreach ($tablas as $documento) {
            $ids = DB::table($documento['tabla'])
                ->whereNotNull('id_empresa')
                ->distinct()
                ->limit(3)
                ->pluck('id_empresa')
                ->all();
            $empresas = array_merge($empresas, $ids);
        }

        return array_values(array_unique(array_filter($empresas)));
    }

    private function documentos()
    {
        return [
            '01' => ['tabla' => 'ct_ventas'],
            '03' => ['tabla' => 'ct_compras'],
            '04' => ['tabla' => 'ct_nota_credito_clientes'],
            '06' => ['tabla' => 'ct_cabecera_remision'],
            '07' => ['tabla' => 'ct_retenciones'],
        ];
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
