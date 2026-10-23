<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CodigoContrasena extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'codigo_contrasena';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
