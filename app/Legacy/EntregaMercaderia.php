<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class EntregaMercaderia extends Model
{
    protected $table = 'ct_entregas_mercaderia';
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany(EntregaMercaderiaDetalle::class, 'id_entrega');
    }

    public function trazabilidad()
    {
        return $this->hasMany(EntregaMercaderiaTrazabilidad::class, 'id_entrega')->orderBy('id');
    }

    public function cliente()
    {
        return $this->belongsTo(Ct_Clientes::class, 'id_cliente', 'identificacion');
    }

    public function guia()
    {
        return $this->belongsTo('Sis_medico\v3\contable\GuiaRemision', 'id_guia_remision');
    }

    public function venta()
    {
        return $this->belongsTo(Ct_ventas::class, 'id_venta');
    }
}
