<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Globales extends Model
{
    protected $table = 'ct_globales';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}