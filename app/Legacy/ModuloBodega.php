<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class ModuloBodega extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modulo_bodega';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega','id_bodega','id');
    }
    
}
