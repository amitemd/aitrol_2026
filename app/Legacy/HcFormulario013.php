<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class HcFormulario013 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_formulario_013';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_hc_procedimiento');
    }
    public function historia_clinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id', 'hcid');
    }
    public function historiaclinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'hcid');
    }
    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function user_mod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
}