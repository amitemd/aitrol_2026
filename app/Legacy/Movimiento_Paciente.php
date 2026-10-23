<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Movimiento_Paciente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimiento_paciente';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function movimiento()
    {
        return $this->belongsTo('Sis_medico\Movimiento','id_movimiento');
    }
    public function hc_procedimientos()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','id_hc_procedimientos');
    }
    public function usuario_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega');
    }
}
