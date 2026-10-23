<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DocumentosElectronicos extends Model
{
    protected $table = 'de_documentos_electronicos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';

    public static function updateRecibidoSri($clave, $resp)
    {
        $arrayDoc = [
            'id_de_pasos' => 4,
            'respuestaSriRecepcion' => json_encode($resp)
        ];
        DocumentosElectronicos::where('clave_acceso', $clave)
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($clave)
    {
        $arrayDoc = [
            'id_de_pasos' => 3
        ];
        DocumentosElectronicos::where('id_de_pasos', 2)
            ->where('clave_acceso', $clave)
            ->update($arrayDoc);
    }
    public static function setDocElectronico($datos, $idPaso = 0)
    {
        $id = 0;
        DB::beginTransaction();
        try {
            $existe = DocumentosElectronicos::where('clave_acceso', $datos['clave_acceso'])->first();
            if ($existe == '' || is_null($existe)) {
                $id = DocumentosElectronicos::insertGetId($datos);
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
                DocumentosElectronicos::where('id', $id)->update($array);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return 'Error: ' . $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile();
        }
        return $id;
    }
}
