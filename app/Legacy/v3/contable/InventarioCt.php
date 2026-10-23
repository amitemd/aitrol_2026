<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class InventarioCt extends Model
{
    protected $table = 'ct_inventario';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at_info';
    //public $timestamps = false;
   
}
