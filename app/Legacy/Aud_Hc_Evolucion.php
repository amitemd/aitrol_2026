<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Evolucion extends Model
{
    //
    protected $table = 'aud_hc_evolucion';

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
        return $this->belongsTo('Sis_medico\Historiaclinica','hcid');    
    }
    
    public function aud_procedimiento()
    {
        return $this->belongsTo('Sis_medico\Aud_Hc_Procedimientos','hc_id_procedimiento','id_procedimientos_org');
    }

    public function aud_child_pugh()
    {
        return $this->belongsTo('Sis_medico\Aud_Hc_Child_Pugh','id_evolucion_org','id_hc_evolucion');
    }
    
}
