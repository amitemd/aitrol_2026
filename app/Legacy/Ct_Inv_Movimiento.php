<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Inv_Movimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_inv_movimiento';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function compras()
    {
        return $this->belongsTo('Sis_medico\Ct_compras','id_referencia','id');
    }
    public function ventas()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas','id_referencia','id');
    }

}
