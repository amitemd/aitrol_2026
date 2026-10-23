<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class InvCosto extends Model
{
    private static $modelo = 'invcosto';
    protected $table = 'inv_costo';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
