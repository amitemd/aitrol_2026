<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Clientes_Empresa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_clientes_empresa';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente', 'identificacion');
    }  
    
    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function usuariomodif()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function facturas()
    {
        return $this->hasMany('Sis_medico\Ct_ventas', 'id_cliente','identificacion');
    }
 
    public function anticipos()
    {
        return $this->hasMany('Sis_medico\Ct_Comprobante_Ingreso', 'id_cliente','identificacion');
    }
}
