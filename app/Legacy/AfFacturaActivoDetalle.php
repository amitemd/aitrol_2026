<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfFacturaActivoDetalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_factura_activo_det';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function activo()
    {
        return $this->belongsTo('Sis_medico\AfActivo', 'activo_id', 'id');
    }

}