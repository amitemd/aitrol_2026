<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumos_utilizados_banos extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumos_utilizados_banos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function dotacion()
    {
        return $this->belongsTo('Sis_medico\Mantenimientos_Dotacion', 'id_insumos_banos');
    }
   
    
}


