<?php

namespace Sis_medico;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Http\Controllers\sri_electronico\FirmaElectronica;

class De_Empresa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_empresa';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getDatos($id)
    {
        return De_Empresa::where('id_empresa', $id)->first();
    }
    public static function getDatosFirma($id)
    {
        $empresa = De_Empresa::getDatos($id);
        if (isset($empresa->ruta_firma)) {
            $rutaFirma = base_path() . '/storage/app/facturaelectronica/p12/' . $empresa->ruta_firma;
            if (file_exists($rutaFirma)) {
                try {
                    $clave = $empresa->clave_firma;
                    $config = array('firmar' => true, 'pass' => $clave, 'file' => $rutaFirma);
                    $firmar = new FirmaElectronica($config);
                    $datos = $firmar->getData();
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
                    ];
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
            ];
        }
        return $arrayDatos;
    }
}
