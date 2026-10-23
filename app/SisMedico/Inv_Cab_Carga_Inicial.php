<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Inv_Cab_Carga_Inicial extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_cab_carga_inicial';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles() {
        return $this->hasMany('Sis_medico\Inv_Det_Carga_Inicial', 'id_cab');
    }
    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    
}
