<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Vacaciones extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacaciones';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina','id_user');
    }
    
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }
    public function nombre_usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuario');
    }
    public function nombre_contingente()
    {
        return $this->belongsTo('Sis_medico\User','id_contingente');
    }

    
    
}