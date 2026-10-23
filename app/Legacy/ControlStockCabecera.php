<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ControlStockCabecera extends Model
{

    protected $table = 'control_stock_cabecera';


    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'responsable_id');
    }

    public function detalle()
    {
        return $this->hasMany('Sis_medico\ControlStockDetalle', 'cabecera_id');
    }
    public function firma()
    {
        return $this->belongsTo('Sis_medico\Firma_Usuario', 'responsable_id', 'id_usuario');
    }
}
