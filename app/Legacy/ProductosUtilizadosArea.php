<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ProductosUtilizadosArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos_utilizados_area';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
