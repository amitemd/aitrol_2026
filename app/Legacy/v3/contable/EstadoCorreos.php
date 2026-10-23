<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class EstadoCorreos extends Model
{
    private static $modelo = 'EstadoCorreos';
    protected $table = 'new_db_estado_correos';
    protected $primaryKey  = 'id_estado_correo';
}
