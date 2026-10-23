<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class InvTransaccionesBodegas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_transacciones_bodegas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = []; 

    public function documentoBodega()
    {
        return $this->belongsTo('Sis_medico\InvDocumentosBodegas', 'id_documento_bodega', 'id');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    // public static function getTransaccionBodega($id_documento, $id_bodega)
    // {
    //     $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $id_documento)
    //                                             ->where('id_bodega', $id_bodega)
    //                                             ->first();
    //     if (isset($transaccion->id)) {
    //         return $transaccion;
    //     } else {
    //         $transaccion = new InvTransaccionesBodegas;
    //         $transaccion->id_documento_bodega = $id_documento;
    //     }
    // }
    
    
}