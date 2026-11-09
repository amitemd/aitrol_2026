<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;

class Agenda extends Model
{
    private static $modelo = 'Agenda';
    protected $table = 'agenda';
    protected $keyType = 'string';
    protected $primaryKey  = 'id';

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Pacientes', 'id_paciente');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Seguros', 'id_seguro');
    }

   
}
