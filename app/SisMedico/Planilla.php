<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planilla_cabecera';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Planilla_Detalle', 'id_planilla_cabecera');
    }
    public function detalles_validos()
    {
        return $this->hasMany('Sis_medico\Planilla_Detalle', 'id_planilla_cabecera')->where('check',1)->where('estado',1);
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea','id');
    }
    public function planilla()
    {
        return $this->belongsTo('Sis_medico\Insumo_Plantilla_Control','id_planilla','id');
    }

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda','id');
    }
    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','id_hc_procedimiento','id');
    }
    public function usuariomod(){
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod','id');
    }
}
 