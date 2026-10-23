<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Historiaclinica extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'historiaclinica';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    protected $primaryKey = 'hcid';

    public function historiaclinica()
    {
        return $this->belongsTo('Sis_medico\Archivo_historico');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');
    }

    public function doctor_1()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor1');
    }
     
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

    public function doctor_2()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor2');
    }

    public function doctor_3()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor3');
    }

     public function pentax()
    {
        return $this->belongsTo('Sis_medico\Pentax', 'hcid', 'hcid');
    }

    public function hc_procedimientos()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos', 'hcid', 'id_hc');
    }

     public function hc_protocolo()
    {
        return $this->belongsTo('Sis_medico\hc_protocolo', 'hcid', 'hcid');
    }

    public function procedimiento()
    {
        return $this->hasMany('Sis_medico\hc_procedimientos','id_hc', 'hcid');
    }

    //09/05/2019 formato cardiologia
    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }

    public function cardio()
    {
        return $this->belongsTo('Sis_medico\Cardiologia', 'hcid','hcid');
    }
    public function hc_procedimientof()
    {
        return $this->hasMany('Sis_medico\hc_procedimientos','id_hc','hcid');
    }

    public function evoluciones()
    {
        return $this->hasMany('Sis_medico\Hc_Evolucion','hcid', 'hcid');
    }

    public function recetas()
    {
        return $this->hasMany('Sis_medico\hc_receta','id_hc','hcid');
    }

    public function diagnosticos()
    {
        return $this->hasMany('Sis_medico\Hc_Cie10','hcid','hcid');
    }

    
}
