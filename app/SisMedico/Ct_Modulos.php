<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Session;

class Ct_Modulos extends Model
{
    //
    protected $table = 'ct_modulos';
    
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

    public function globales()
    {
        $idempresa = Session::get('id_empresa');
        return $this->hasMany('Sis_medico\Ct_Globales','id_modulo','id')->where('id_empresa', $idempresa);
    }

}
