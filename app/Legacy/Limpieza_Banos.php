<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Limpieza_Banos extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'limpieza_banos';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


   /* public function user()
    {
        return $this->belongsTo('Sis_medico\User', 'nombre');
    }
    */
     public function piso()
    {
        return $this->belongsTo('Sis_medico\Mantenimientos_Generales', 'nombre_piso');
    }
     public function encargado()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

    public function productos()
    {
        return $this->hasMany('Sis_medico\InsumosUtilizadosArea', 'id_limpieza_area');
    }

    public function insumos()
    {
        return $this->hasMany('Sis_medico\Insumos_utilizados_banos', 'id_limpieza_banos');
    }
    public function piso_banos()
    {
        return $this->belongsTo('Sis_medico\Mantenimientos_Banos', 'id_piso');
    }
    
   
  
}