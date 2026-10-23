<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Inv_Kardex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_inv_kardex';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function inventario()
    {
        return $this->belongsTo('Sis_medico\Ct_Inv_Interno','id_inv','id');
    }
    public function movimiento()
    {
        return $this->belongsTo('Sis_medico\Ct_Inv_Movimiento','id_movimiento','id');
    }

}
