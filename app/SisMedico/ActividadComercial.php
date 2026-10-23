<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ActividadComercial extends Model
{
    /**
     * The table aaaaassociated with the model.
     *
     * @var string
     */
    protected $table = 'actividad_comercial';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function cliente(){
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente');
    }

    public function actividadestado(){
        return $this->belongsTo('Sis_medico\Actividad_Estados_Comercial', 'id_actividad');
    }
    
    public function vendedor(){
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
}