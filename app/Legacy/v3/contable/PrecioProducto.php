<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class PrecioProducto extends Model
{
    //
    protected $table = 'precio_producto';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo('Sis_medico\v3\Productos', 'id_producto', 'codigo');
    }
}
