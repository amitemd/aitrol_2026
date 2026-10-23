<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ap_Orden extends Model

{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_ordenes';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function plano_cabecera(){
        return $this->belongsTo('Sis_medico\Archivo_Plano_Cabecera','id_ap_cabecera');    
    }

    public function paciente(){
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');    
    }

    public function hc_procedimientos(){
        return $this->belongsTo('Sis_medico\Hc_procedimientos','id_hc_procedimientos');    
    }

    public function orden(){
        return $this->belongsTo('Sis_medico\Orden','id_orden');    
    }

    public function orden_012(){
        return $this->belongsTo('Sis_medico\Orden_012','id_orden_012');    
    }

    public function usuariocrea()   
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function usuariomod()   
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
}
