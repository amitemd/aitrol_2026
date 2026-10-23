<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Orden_Venta extends Model
{
    //
    protected $table = 'ct_orden_venta';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Orden_Venta_Detalle', 'id_orden');
    }

    public function  paquetes_detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Orden_Venta_Detalle_Paquete', 'id_orden');
    }

    public function empresa()
    {

        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function seguro()
    {

        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }
    public function cliente()
    {

        return $this->belongsTo('Sis_medico\Ct_Clientes','identificacion', 'identificacion');
    }

    public function agenda()
    {

        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }

    public function pagos()
    {
        return $this->hasMany('Sis_medico\Ct_Orden_Venta_Pago', 'id_orden');
    }

    public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function venta(){
        return $this->belongsTo('Sis_medico\Ct_ventas','id', 'orden_venta');
    }
    public function user_referido()
    {
        return $this->belongsTo('Sis_medico\User','asesor_venta');
    }
    public function logs()
    {
        return $this->hasMany('Sis_medico\LogReciboDeCobro', 'id_ct_orden_venta');
    }
    public function proforma()
    {
        return $this->belongsTo('Sis_medico\Proforma_Cabecera','id_proforma');
    }
    public function nombreDoctorComision(){
        return $this->belongsTo('Sis_medico\Comisiones_Gastro','doctor_referido');
    }
}
