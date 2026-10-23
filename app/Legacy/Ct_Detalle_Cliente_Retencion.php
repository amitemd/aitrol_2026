<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Cliente_Retencion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_cliente_retencion';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function porcentajer(){
        
        return $this->belongsTo('Sis_medico\Ct_Porcentaje_Retenciones', 'id_porcentaje','id');
    }
    public function cabecera(){
        return $this->belongsTo('Sis_medico\Ct_Cliente_Retencion', 'id_cliente_retencion');
    }


}