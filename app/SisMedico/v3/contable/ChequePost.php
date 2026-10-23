<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
class ChequePost extends Model
{
    private static $modelo = 'ct_cheques_post';
    protected $table = 'ct_cheques_post';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';
    
}
