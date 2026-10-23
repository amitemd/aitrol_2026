<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Cliente_Retencion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cliente_retencion';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalle_retencion()
    {

        return $this->hasMany('Sis_medico\Ct_Detalle_Cliente_Retencion', 'id_cliente_retencion');
    }

    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente', 'identificacion');
    }
    public function ventas()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function usuariomodif()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function retencion()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura');
    }
}
