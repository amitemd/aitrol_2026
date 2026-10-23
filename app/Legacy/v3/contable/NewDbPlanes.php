<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;


class NewDbPlanes extends Model
{
    private static $modelo = 'NewDbPlanes';
    protected $table = 'new_db_planes';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    
}
