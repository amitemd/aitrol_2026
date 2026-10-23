<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DeDocumentosElectronicos extends Model
{
    private static $modelo = 'DeDocumentosElectronicos';
    protected $table = 'de_documentos_electronicos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
