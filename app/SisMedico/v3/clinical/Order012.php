<?php

namespace Sis_medico\v3\clinical;

use Illuminate\Database\Eloquent\Model;

class Order012 extends Model
{
    protected $table = 'orden_012';
    protected $guarded = array();

    public function patient()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function signingDoctor()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor_firma');
    }

    public function requestingDoctor()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor_solicita');
    }

    public function company()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function evolution()
    {
        return $this->belongsTo('Sis_medico\Hc_Evolucion', 'id_hc_evolucion');
    }

    // Alias conservado para los exportadores clínicos compartidos.
    public function evolucion()
    {
        return $this->evolution();
    }

    public function diagnoses()
    {
        return $this->hasMany('Sis_medico\v3\clinical\Order012Diagnosis', 'id_orden_012');
    }
}
