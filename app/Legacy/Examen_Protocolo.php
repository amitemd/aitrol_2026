<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Protocolo extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_protocolo';
    

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function examen()
    {
        return $this->belongsTo('Sis_medico\Examen', 'id_examen');
    }
    protected $guarded = [];
}
