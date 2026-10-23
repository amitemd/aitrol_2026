<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
class DetalleChequePost extends Model
{
    private static $modelo = 'ct_detalle_cheques_post';
    protected $table = 'ct_detalle_cheques_post';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';
    
 
    public function cabecera(){
        
        return $this->belongsTo('Sis_medico\Ct_Cheques_Post','id_comprobante', 'id');
    }

}
