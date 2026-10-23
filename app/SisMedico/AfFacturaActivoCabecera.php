<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfFacturaActivoCabecera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_factura_activo_cab';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }

    public function datosproveedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }


    public function tipo_comprobante()
    {
        return $this->belongsTo('Sis_medico\Ct_master_tipos', 'codigo', 'tipo_comprobante');
    }

    public function tp_comprobante()
    {
        return $this->belongsTo('Sis_medico\Ct_master_tipos', 'codigo', 'tipo_comprobante');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\AfFacturaActivoDetalle', 'fact_activo_id', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    
}