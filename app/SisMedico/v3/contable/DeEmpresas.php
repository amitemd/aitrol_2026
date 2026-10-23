<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeEmpresas extends Model
{
    private static $modelo = 'DeEmpresas';
    protected $table = 'de_empresa';
    protected $primaryKey  = 'id';

    public static function getDatosFirma($id)
    {
        $empresa = self::getEmpresas($id);
        if (isset($empresa->ruta_firma) && $empresa->ruta_firma != '') {
            $rutaFirma = base_path() . '/storage/app/facturaelectronica/p12/' . $empresa->ruta_firma;
            if (file_exists($rutaFirma)) {
                try {
                    $clave = $empresa->clave_firma;
                    $config = array('firmar' => true, 'pass' => $clave, 'file' => $rutaFirma);
                    $firmar = new FirmaElectronica($config);
                    $datos = $firmar->getData();
                    if (count($datos) > 0) {
                        $nombre = isset($datos['subject']['CN']) ? $datos['subject']['CN'] : '';
                        $email = isset($datos['subject']['emailAddress']) ? str_replace('email:', '', $datos['subject']['emailAddress']) : (isset($datos['extensions']['subjectAltName']) ? str_replace('email:', '', $datos['extensions']['subjectAltName']) : '');
                        $desde = isset($datos['validFrom_time_t']) ? date('Y-m-d H:i:s', $datos['validFrom_time_t']) : '';
                        $emision = isset($datos['validFrom_time_t']) ? date('Y-m-d H:i:s', $datos['validFrom_time_t']) : '';
                        $hasta = isset($datos['validTo_time_t']) ? date('Y-m-d H:i:s', $datos['validTo_time_t']) : '';
                        $start = new \DateTime($desde);
                        $end = new \DateTime($hasta);
                        $diff = $start->diff($end);
                        $dias = $diff->format('%a');
                        $desde = date('Y-m-d H:i:s');
                        $start = new \DateTime($desde);
                        $diff = $start->diff($end);
                        $expiracion = $diff->format('%a');
                        $when = date('Y-m-d') . ' 00:00:00';
                        $vigente = $hasta > $when;
                        $emisior = isset($datos['issuer']['CN']) ? $datos['issuer']['CN'] : '';
                        $arrayDatos = [
                            'Nombre' => $nombre,
                            'Email' => $email,
                            'Desde' => $emision,
                            'Hasta' => $hasta,
                            'Dias' => $dias,
                            'Expiracion' => $expiracion,
                            'Vigente' => $vigente,
                            'Emisor' => $emisior,
                            'message' => 'ok'
                        ];
                    } else {
                        $arrayDatos = [
                            'Nombre' => 'Sin Firma',
                            'Email' => 'Sin Firma',
                            'Desde' => 'Sin Firma',
                            'Hasta' => 'Sin Firma',
                            'Dias' => 'Sin Firma',
                            'Expiracion' => 'Sin Firma',
                            'Vigente' => 'Sin Firma',
                            'Emisor' => 'Sin Firma',
                            'message' => 'Error al leer firma electrónica'
                        ];
                    }
                } catch (Exception $e) {
                    $arrayDatos = [
                        'Nombre' => 'Sin Firma',
                        'Email' => 'Sin Firma',
                        'Desde' => 'Sin Firma',
                        'Hasta' => 'Sin Firma',
                        'Dias' => 'Sin Firma',
                        'Expiracion' => 'Sin Firma',
                        'Vigente' => 'Sin Firma',
                        'Emisor' => 'Sin Firma',
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $arrayDatos = [
                    'Nombre' => 'Sin Firma',
                    'Email' => 'Sin Firma',
                    'Desde' => 'Sin Firma',
                    'Hasta' => 'Sin Firma',
                    'Dias' => 'Sin Firma',
                    'Expiracion' => 'Sin Firma',
                    'Vigente' => 'Sin Firma',
                    'Emisor' => 'Sin Firma',
                    'message' => 'Archivo no encontrado'
                ];
            }
        } else {
            $arrayDatos = [
                'Nombre' => 'Sin Firma',
                'Email' => 'Sin Firma',
                'Desde' => 'Sin Firma',
                'Hasta' => 'Sin Firma',
                'Dias' => 'Sin Firma',
                'Expiracion' => 'Sin Firma',
                'Vigente' => 'Sin Firma',
                'Emisor' => 'Sin Firma',
                'message' => 'Archivo sin ruta'
            ];
        }
        return $arrayDatos;
    }
    public static function getEmpresas($id)
    {
        return DeEmpresas::where('id_empresa', $id)
            ->where('anio', date('Y'))
            ->where('estado', 1)
            ->first([
                'id',
                'id_empresa',
                DB::Raw('IF(tipo_contribuyente=0,"Persona natural","Persona juridica") as tipo_contribuyente'),
                DB::Raw('IF(agente_retencion=0,"No","Si") as agente_retencion'),
                DB::Raw('IF(contribuyente_especial="NO","No",contribuyente_especial) as contribuyente_especial'),
                DB::Raw('IF(ambiente=1,"Pruebas","Producción") as ambiente'),
                DB::Raw('IF(contabilidad=1,"Si","No") as contabilidad'),
                DB::Raw('IF(tipo_rimpe=0,"Rimpe popular",(IF(tipo_rimpe=1,"Rimpe Emprendedor","Regimen general"))) as tipo_rimpe'),
                'anio',
                'estado',
                'ruta_firma',
                'clave_firma'
            ]);
    }
}
