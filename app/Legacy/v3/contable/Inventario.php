<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $primaryKey  = 'id_info';
    const CREATED_AT = 'created_at_info';
    //public $timestamps = false;
   
}
