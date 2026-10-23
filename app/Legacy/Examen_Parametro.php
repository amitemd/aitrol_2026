<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Parametro extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_parametro';
    

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function examen()
    {
        return $this->belongsTo('Sis_medico\Examen','id_examen');
    }
}
 