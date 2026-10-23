<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Inv_Det_Carga_Inicial extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_det_carga_inicial';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function bodega(){
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega');
    }

    public function cabecera(){
        return $this->belongsTo('Sis_medico\Inv_Cab_Carga_Inicial', 'id_cab');
    }
    
}
