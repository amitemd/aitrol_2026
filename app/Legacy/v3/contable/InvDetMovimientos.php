<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class InvDetMovimientos extends Model
{
    private static $modelo = 'Inv detalle movimientos';

    protected $table = 'inv_det_movimientos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Invcabmovimientos', 'id_inv_cab_movimientos', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Productos', 'id_producto', 'id');
    }
  

}
