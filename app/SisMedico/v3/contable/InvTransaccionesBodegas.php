<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class InvTransaccionesBodegas extends Model
{
    private static $modelo = 'Inv transaccion bodegas';
    protected $table = 'inv_transacciones_bodegas';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public function documentoBodega()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Invdocumentosbodegas', 'id_documento_bodega', 'id');
    }

    public static function getSecueciaTipoDocum($id_bodega = null, $vtipo = null)
    {
        $documento = InvDocumentosBodegas::where('abreviatura_documento', $vtipo)->first();
        if ($documento != null) {
            if (isset($documento->id)) {
                $tras_bodega = self::where('id_documento_bodega', $documento->id)->where('id_bodega', $id_bodega)->first();
                if (isset($tras_bodega->id)) {
                    $suecuencia = $tras_bodega->secuencia;
                    $tras_bodega->secuencia =  $tras_bodega->secuencia + 1;
                    $tras_bodega->save();
                } else {
                    $tras_bodega = new self;
                    $tras_bodega->id_documento_bodega = $documento->id;
                    $tras_bodega->id_bodega = $id_bodega;
                    $tras_bodega->secuencia = 1;
                    $tras_bodega->save();
                }
                return $tras_bodega->secuencia;
            }
        }
        return 0;
    }
}
