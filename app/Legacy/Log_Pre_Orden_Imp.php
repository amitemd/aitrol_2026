<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_Pre_Orden_Imp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_pre_orden_imp';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function importacion(){
        return $this->belongsTo('Sis_medico\Ct_Importaciones_Cab', 'id_imp_cab');
    }

    public function af_factura(){
        return $this->belongsTo('Sis_medico\AfFacturaActivoCabecera', 'id_af_factura');
    }

}

