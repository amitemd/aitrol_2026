<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UsuarioEmpresa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuario_empresa';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
