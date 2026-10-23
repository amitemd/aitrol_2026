<?php

namespace Sis_medico;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class De_Documentos_Electronicos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_documentos_electronicos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public static function getInfoAdicional($id, $clave)
    {
        return De_Documentos_Electronicos::where('clave_acceso', $clave)
            ->where('id_documento', $id)
            ->first();
    }
    public static function getInfo()
    {
        return De_Documentos_Electronicos::join('de_pasos as p', 'de_documentos_electronicos.id_de_pasos', '=', 'p.id')
            ->join('de_maestro_documentos as m', 'de_documentos_electronicos.id_maestro_documento', '=', 'm.id')
            ->leftjoin('ct_cabecera_remision as g', 'de_documentos_electronicos.id_documento', '=', 'g.id')
            ->leftjoin('users as u', 'g.id_usuariocrea', '=', 'u.id')
            ->get([
                'de_documentos_electronicos.id',
                'p.nombre as nombre_paso',
                'm.nombre as nombre_documento',
                'de_documentos_electronicos.ruc_receptor',
                'de_documentos_electronicos.ruc_emisor',
                'de_documentos_electronicos.clave_acceso',
                'de_documentos_electronicos.establecimiento',
                'de_documentos_electronicos.emision',
                'de_documentos_electronicos.secuencial',
                'de_documentos_electronicos.respuestaSRIRecepcion',
                'de_documentos_electronicos.respuestaSRIAutorizacion',
                'u.nombre1',
                'u.nombre2',
                'u.apellido1',
                'u.apellido2',
            ]);
    }
    public static function getInfoRetencion()
    {
        return De_Documentos_Electronicos::join('de_pasos as p', 'de_documentos_electronicos.id_de_pasos', '=', 'p.id')
            ->join('de_maestro_documentos as m', 'de_documentos_electronicos.id_maestro_documento', '=', 'm.id')
            ->leftjoin('ct_retenciones as g', 'de_documentos_electronicos.id_documento', '=', 'g.id')
            ->leftjoin('users as u', 'g.id_usuariocrea', '=', 'u.id')
            ->get([
                'de_documentos_electronicos.id',
                'p.nombre as nombre_paso',
                'm.nombre as nombre_documento',
                'de_documentos_electronicos.ruc_receptor',
                'de_documentos_electronicos.ruc_emisor',
                'de_documentos_electronicos.clave_acceso',
                'de_documentos_electronicos.establecimiento',
                'de_documentos_electronicos.emision',
                'de_documentos_electronicos.secuencial',
                'de_documentos_electronicos.respuestaSRIRecepcion',
                'de_documentos_electronicos.respuestaSRIAutorizacion',
                'u.nombre1',
                'u.nombre2',
                'u.apellido1',
                'u.apellido2',
            ]);
    }
    public static function getDocumento($clave)
    {
        return De_Documentos_Electronicos::where('clave_acceso', $clave)->first();
    }
    public static function updateDocElectronico($datos)
    {
        $arrayDocElec = [
            'id_de_pasos' => 6,
        ];
        De_Documentos_Electronicos::where('estado', 2)->update($arrayDocElec);
    }
    public static function updateRecibidoSri($comp, $resp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 4,
            'respuestaSriRecepcion' => json_encode($resp)
        ];
        De_Documentos_Electronicos::where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($comp, $resp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 5,
            'respuestaSriAutorizacion' => json_encode($resp)
        ];
        De_Documentos_Electronicos::where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($comp, $resp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 9,
            'respuestaSriRecepcion' => json_encode($resp)
        ];
        De_Documentos_Electronicos::where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    public static function updateXmlNoAutorizacion($comp, $resp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 10,
            'respuestaSriAutorizacion' => json_encode($resp)
        ];
        De_Documentos_Electronicos::where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    private static function to_xml(SimpleXMLElement $object, array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                De_Documentos_Electronicos::to_xml($new_object, $value);
            } else {
                // if the key is an integer, it needs text with it to actually work.
                if ($key != 0 && $key == (int) $key) {
                    $key = "key_$key";
                }

                $object->addChild($key, $value);
            }
        }
    }
    public static function updateXmlFirmado($comp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 3
        ];
        De_Documentos_Electronicos::where('id_de_pasos', 2)
            ->where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($comp)
    {
        $docAnte = simplexml_load_string($comp);
        $arrayDoc = [
            'id_de_pasos' => 2
        ];
        De_Documentos_Electronicos::where('id_de_pasos', 1)
            ->where('clave_acceso', $docAnte->infoTributaria->claveAcceso)
            ->update($arrayDoc);
    }
    public static function revisionEstadosDocumentosAutorizados($claves)
    {
        $claves = substr($claves, 0, -1);
        $claves = explode(',', $claves);
        $claves = json_encode($claves);
        $claves = json_decode($claves);

        return De_Documentos_Electronicos::join('de_maestro_documentos as m', 'de_documentos_electronicos.id_maestro_documento', '=', 'm.id')
            ->whereIn('clave_acceso', $claves)
            ->get([
                'establecimiento',
                'emision',
                'secuencial',
                'de_documentos_electronicos.clave_acceso',
                'm.nombre as tipoDoc',
            ]);
    }
    public static function revisionEstadosDocumentosErrores($ids)
    {
        $ids = substr($ids, 0, -1);
        $ids = explode(',', $ids);
        $ids = json_encode($ids);
        $ids = json_decode($ids);

        return De_Log_Error::join('de_documentos_electronicos as d', function ($q) use ($ids) {
            $q->on('de_log_error.id_de_documentos_electronicos', '=', 'd.id')
                ->whereIn('d.id', $ids);
        })
            ->join('de_maestro_documentos as m', 'd.id_maestro_documento', '=', 'm.id')
            ->orderBy('de_log_error.id', 'DESC')->first([
                'establecimiento',
                'emision',
                'secuencial',
                'descripcion_error as errores',
                'm.nombre as tipoDoc'
            ]);
    }
    public static function setDocElectronico($datos, $idPaso = 0)
    {
        //echo '<pre>';print_r($datos);exit;
        $id = 0;
        $estado = '';
        DB::beginTransaction();
        try {
            $existe = De_Documentos_Electronicos::where('clave_acceso', $datos['clave_acceso'])->first();
            if ($existe == '' || is_null($existe)) {
                $id = De_Documentos_Electronicos::insertGetId($datos);
            } else {
                $id = $existe->id;
                $array = [
                    'id_de_pasos' => $idPaso,
                    'id_usuariomod' => $datos['id_usuariomod'],
                    'ip_modificacion' =>  $datos['ip_modificacion'],
                    'infoTributaria' => $datos['infoTributaria'],
                    'infoFactura' => $datos['infoFactura'],
                    'detalles' => $datos['detalles'],
                    'infoAdicional' => $datos['infoAdicional'],
                ];
                De_Documentos_Electronicos::where('id', $id)->update($array);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $estado = 'Error: ' . $e->getMessage();
        }
        return $id;
    }
}
