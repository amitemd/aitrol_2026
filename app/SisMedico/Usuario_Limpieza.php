<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Usuario_Limpieza extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuario_limpieza';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
