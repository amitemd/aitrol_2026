<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Convenios extends Model
{
    protected $table = 'convenio';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
