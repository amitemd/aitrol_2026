<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ComprobanteEgresoVarios extends Model
{

    public static $modelo = 'ComprobanteEgresoVario';
    protected $table = 'ct_comprobante_egreso_varios';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Usuarios', 'id_usuariocrea');
    }

    public function asiento()
    {
        return $this->belongsTo('Sis_medico\v3\contable\AsientosCabecera', 'id_asiento_cabecera');
    }
    
    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleComprobanteEgresoVarios', 'id_comprobante_varios');
    }
}
