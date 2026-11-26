<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Producto_Medicina extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'producto_medicina';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
