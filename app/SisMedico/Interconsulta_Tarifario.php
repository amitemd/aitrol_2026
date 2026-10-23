<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Interconsulta_Tarifario extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interconsulta_tarifario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function ap_procedimento()
    {
        return $this->belongsTo('Sis_medico\ApProcedimiento','id_ap_proc');
    }


    
}