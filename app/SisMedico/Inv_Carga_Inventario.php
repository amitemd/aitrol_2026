<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Inv_Carga_Inventario extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_carga_inventario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public $timestamps= false;
}