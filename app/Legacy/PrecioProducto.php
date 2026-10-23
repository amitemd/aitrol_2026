<?php

namespace Sis_medico;

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
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto', 'codigo');
    }

}
