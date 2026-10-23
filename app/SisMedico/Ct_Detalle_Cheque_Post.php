<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class Ct_Detalle_Cheque_Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_cheques_post';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function cabecera(){
        
        return $this->belongsTo('Sis_medico\Ct_Cheques_Post','id_comprobante', 'id');
    }
    
}