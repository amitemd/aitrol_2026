<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Evoluciones_Enfermeria extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_evoluciones_enfermeria';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function dato()
    {
        return $this->belongsTo('Sis_medico\Ho_Solicitud','id_solicitud');
    }

}