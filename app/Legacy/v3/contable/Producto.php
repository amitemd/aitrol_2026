<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    private static $modelo = 'producto';
    protected $table = 'producto';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
