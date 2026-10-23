<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    private static $modelo = 'Nota Credito Clientes';
    protected $table = 'ct_nota_credito_cabecera';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

   
}
