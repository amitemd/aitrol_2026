<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Procedimientos extends Model
{
    //
    protected $table = 'aud_hc_procedimientos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
 
    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','id_hc');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');
    }
    
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_examinador');
    }
    public function doctor_firma()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_examinador2');
    }

    public function procedimiento_completo()
    {
        return $this->belongsTo('Sis_medico\procedimiento_completo','id_procedimiento_completo');
    }

    public function evolucion_consulta()
    {
        return $this->hasOne('Sis_medico\hc_evolucion', 'hc_id_procedimiento');
    }
    public function doctor_ayudante()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_ayudante_con');
    }
    public function hc_procedimiento_final()
    {
        return $this->belongsTo('Sis_medico\Hc_Procedimiento_Final','id', 'id_hc_procedimientos');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function movimiento_paciente()
    {
        return $this->belongsTo('Sis_medico\Movimiento_Paciente','id','id_hc_procedimientos');
    }
    public function hc_procedimiento_f()
    {
        return $this->hasMany('Sis_medico\Hc_Procedimiento_Final','id_hc_procedimientos');
    }
    
    
}
 