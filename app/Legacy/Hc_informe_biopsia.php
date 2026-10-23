<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_informe_biopsia extends Model
{
    //
    protected $table = 'hc_informe_biopsia';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
 
    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','hcid');
    }
    public function doctor_macro()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor_macroscopica','id');  
    }
    public function doctor_micro()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor_microscopica','id');  
    }
    public function responsable()
    {
        return $this->belongsTo('Sis_medico\User', 'id_responsable','id');  
    }
    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','id_hc_procedimiento','id');
    }
    
}
 