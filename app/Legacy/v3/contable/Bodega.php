<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    private static $modelo = 'bodega';
    protected $table = 'bodega';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function empresa()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Empresa', 'id_empresa');
    }
    public function cliente_nombre()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Clientes', 'id_cliente');
    }
}
