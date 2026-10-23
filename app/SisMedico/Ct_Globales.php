<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Globales extends Model
{
    //
    protected $table = 'ct_globales';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa')->with('empresa');
        
    }*/

    public function modulos()
    {
        return $this->belongsTo('Sis_medico\Ct_Modulos','id_modulo');
        
    }
    public function debec(){
        return $this->belongsTo('Sis_medico\Plan_Cuentas','debe');
    }
    public function haberc(){
        return $this->belongsTo('Sis_medico\Plan_Cuentas','haber');
    }

}
