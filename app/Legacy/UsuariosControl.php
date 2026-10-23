<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UsuariosControl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios_control_sintoma';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
