<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class RolesUsuario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles_usuario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}