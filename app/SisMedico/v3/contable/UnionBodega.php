<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class UnionBodega extends Model
{
    private static $modelo = 'unionBodega';
    protected $table = 'union_bodega';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function bodega_compras(){
        return $this->belongsTo('Sis_medico\v3\contable\Bodega', 'id_bodega_compra');
    }
}
