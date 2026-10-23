<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_ventas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_ventas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }

    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');
    }
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\Ct_Cliente_Retencion','id','id_factura');
    }
    public function comp_ingreso()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Ingreso','id_factura');
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce_Clientes','id_factura');
    }
    public function chequepost()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cheque_Post','id_factura');
    }
    
    public function cab_retenciones()
    {
        return $this->hasOne(Ct_Cliente_Retencion::class,'id_factura');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuariomod(){
        
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function cruce_cuentas()
    {
        return $this->hasMany('Sis_medico\Ct_Cruce_Cuentas_Clientes','id_factura');
    }
    public function credito()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Credito_Clientes','id_factura');
    }
   
}
