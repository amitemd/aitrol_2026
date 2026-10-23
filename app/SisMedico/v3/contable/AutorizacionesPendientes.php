<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutorizacionesPendientes extends Model
{
    private static $modelo = 'AutorizacionesPendientes';
    protected $table = 'ct_autorizaciones_pendientes';
    protected $primaryKey  = 'id';
}
