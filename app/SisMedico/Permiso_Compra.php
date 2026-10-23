<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Permiso_Compra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permiso_compra';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
   

}
