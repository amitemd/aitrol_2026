<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Protocolo extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'protocolo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function examenes()
    {
        return $this->hasMany('Sis_medico\Examen_Protocolo','id_protocolo');   
    }
}
