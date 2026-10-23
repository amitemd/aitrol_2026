<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class LogUsuario extends Model
{
    private static $modelo = 'LogUsuario';
    protected $table = 'log_usuario';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at'; 
    const UPDATED_AT = 'updated_at';

}
