<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_Evolucion_Anestesiologo extends Model
{
    //
    protected $table = 'hc_evolucion_anestesiologo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function indicaciones()
    {
        return $this->hasMany('Sis_medico\Hc_Evolucion_Indicacion','id_evolucion');
    }
    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','hc_id_procedimiento');
    }

    public function child_pug()
    {
        return $this->hasOne('Sis_medico\hc_child_pugh', 'id_hc_evolucion');
    }

    public function historiaclinica(){
        return $this->belongsTo('Sis_medico\Historiaclinica','id_hc');    
    }

    public function hc_anestesiologia(){
        return $this->belongsTo('Sis_medico\Hc_Anestesiologia','id_hc_anestesiologia');    
    }

}
