<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Producto_Comision extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'producto_comision';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function doctor_nombre()
    {
        return $this->belongsTo('Sis_medico\Comisiones_Gastro', 'id_comision_gatro', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_ct_producto', 'id');
    }
}
