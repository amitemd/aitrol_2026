<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class Archivos extends Model
{
    private static $modelo = 'Archivos';
    protected $table = 'new_db_archivos';
    protected $primaryKey  = 'id_archivo';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

}
