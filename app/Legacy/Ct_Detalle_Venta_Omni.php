<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Venta_Omni extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_venta_omni';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Factura_Omni','id_omni');
        
    }

    
}
