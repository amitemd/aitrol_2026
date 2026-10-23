<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_productos_paquete extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_productos_paquete';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function tarifarios()
    {
        return $this->hasMany('Sis_medico\Ct_Producto_Tarifario_Paquete', 'id_producto_paquete');
    }

}