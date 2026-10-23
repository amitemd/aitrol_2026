<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_Anestesiologia extends Model
{
    //
    protected $table = 'hc_anestesiologia';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function sala()
    {
        return $this->belongsTo('Sis_medico\Sala', 'id_sala');
    }
    public function anestesiologo()
    {
        return $this->belongsTo('Sis_medico\User', 'id_anestesiologo');
    }
    public function ayudante()
    {
        return $this->belongsTo('Sis_medico\User', 'id_ayudante');
    }
    public function guia()
    {
        return $this->belongsTo('Sis_medico\User', 'id_guiado');
    }
    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id_hc');
    }
    public function instrumentista()
    {
        return $this->belongsTo('Sis_medico\User', 'id_instrumentista');
    }
    public function tipo_anestesiologia()
    {
        return $this->belongsTo('Sis_medico\Tipo_Anesteciologia', 'id_tipoanestesiologia');
    }
    public function usuario_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuario_modifica()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }

    public function preanestesiologia()
    {
        return $this->belongsTo('Sis_medico\Rc_pre_anestesica', 'id');
    }
    public function hc_procedimientos()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos', 'id_hc_procedimientos');
    }

}
