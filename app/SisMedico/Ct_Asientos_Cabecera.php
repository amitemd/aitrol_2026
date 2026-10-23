<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Asientos_Cabecera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_asientos_cabecera';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function egresos()
    {
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Egreso', 'id', 'id_asiento_cabecera');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function egresos_varios()
    {
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Egreso_Varios', 'id', 'id_asiento_cabecera');
    }

    public function compras()
    {
        return $this->belongsTo('Sis_medico\Ct_compras', 'id', 'id_asiento_cabecera');
    }
    public function cr()
    {
        return $this->belongsTo('Sis_medico\Ct_Cruce_Cuentas', 'id', 'id_asiento_cabecera');
    }
    public function ventas()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id', 'id_asiento');
    }

    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\Ct_Retenciones','id' ,'id_asiento_cabecera');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Asientos_Detalle', 'id_asiento_cabecera');
    }
    public function debito()
    {
        return $this->belongsTo('Sis_medico\Nota_Debito', 'id','id_asiento');
    }
    public function transferencia()
    {
        return $this->belongsTo('Sis_medico\Ct_Transferencia_Bancaria', 'id','id_asiento');
    }
    public function baneg()
    {
        return $this->belongsTo('Sis_medico\Ct_Debito_Bancario','id', 'id_asiento');
    }
    public function nota_credito()
    {
        return $this->belongsTo('Sis_medico\Ct_Nota_Credito','id', 'id_asiento');
    }
    public function depositos()
    {
        return $this->belongsTo('Sis_medico\Ct_Deposito_Bancario','id', 'id_asiento');
    }
    public function masivo()
    {
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Egreso_Masivo','id', 'id_asiento_cabecera');
    }
    public function anterior()
    {
        return $this->belongsTo('Sis_medico\Log_Contable','id', 'id_referencia');
    }
    public function referencia()
    {
        return $this->belongsTo('Sis_medico\Log_Contable','id', 'id_ant');
    }
    
}
