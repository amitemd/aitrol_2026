<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Anticipos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_anticipos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_empl');
    }

    public function usuario_crea(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

    public function asiento_cabecera(){
        
        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabecera','id_asiento_cabecera');
    
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa')->with('empresa');
        
    }

   
}
