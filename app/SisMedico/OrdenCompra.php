<?php

namespace Sis_medico;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_compra';
    
    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function proveedores()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }

    
}
