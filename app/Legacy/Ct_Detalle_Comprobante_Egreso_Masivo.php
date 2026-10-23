<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Comprobante_Egreso_Masivo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_comprobante_egreso_masivo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function compras()
    {
        return $this->belongsTo('Sis_medico\Ct_compras', 'id_compra');
    }
    public function comp_egreso()
    {
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Egreso_Masivo','id_comprobante');
    }
    
    public function proveedor(){
        
        return $this->belongsTo('Sis_medico\Proveedor', 'id_proveedor','id');
    }
}