<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Exception;

class InvDocumentosBodegas extends Model
{
    private static $modelo = 'Inv documentos bodega';

    protected $table = 'inv_documentos_bodegas';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public function tipo_movimiento()
    {
        return $this->belongsTo('Sis_medico\v3\contable\InvTipoMovimiento', 'id_inv_tipo_movimiento', 'id');
    }

    public static function getSecueciaTipoDocum($id_bodega = null, $vtipo = null)
    {
        try {
            $documento = self::where('abreviatura_documento', $vtipo)->first();
            if ($documento != null) {
                if (isset($documento->id)) {
                    $tras_bodega = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)->where('id_bodega', $id_bodega)->first();
                    if (isset($tras_bodega->id)) {
                        $suecuencia = $tras_bodega->secuencia;
                        $tras_bodega->secuencia =  $tras_bodega->secuencia + 1;
                        $tras_bodega->save();
                    } else {
                        $tras_bodega = new InvTransaccionesBodegas;
                        $tras_bodega->id_documento_bodega = $documento->id;
                        $tras_bodega->id_bodega = $id_bodega;
                        $tras_bodega->secuencia = 1;
                        $tras_bodega->save();
                    }
                    return ["status" => true, "msj" => $tras_bodega->secuencia, "code" => 200];
                }
            }
            throw new Exception("No existe un documento bodega");
        } catch (Exception $e) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
            ];
        }
    }
}
