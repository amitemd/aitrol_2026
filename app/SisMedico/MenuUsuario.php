<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class MenuUsuario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_usuario';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}