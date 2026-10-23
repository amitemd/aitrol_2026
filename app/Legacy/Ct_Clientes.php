<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Clientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table     = 'ct_clientes';
    protected $keyType   = 'string';
    protected $primaryKey   = 'identificacion';
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    
    public function facturas()
    {
        return $this->hasMany('Sis_medico\Ct_ventas', 'id_cliente','identificacion');
    }
 
    public function anticipos()
    {
        return $this->hasMany('Sis_medico\Ct_Comprobante_Ingreso', 'id_cliente','identificacion');
    }

    public static function getCliente($id)
    {
        return Ct_Clientes::where('identificacion', $id)->first();
    }
}
