<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Hc_Receta_Evolucion extends Model
{
    protected $table = 'ho_hc_receta_evolucion';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function receta(){
        return $this->belongsTo('Sis_medico\hc_receta','id_receta');    
    }

    public function evolucion(){
        return $this->belongsTo('Sis_medico\Hc_Evolucion','id_evolucion');    
    }

}