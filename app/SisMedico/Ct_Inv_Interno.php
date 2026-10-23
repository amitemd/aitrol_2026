<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Inv_Interno extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_inv_interno';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos','id_producto','id');
    }
    public function movimiento()
    {
        return $this->belongsTo('Sis_medico\Ct_Inv_Movimiento','id_movimiento');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Inv_Kardex','id_inv')->orderBy('id_transaccion','ASC');
    }

}
