<?php

namespace Sis_medico;

use DB;
use Illuminate\Database\Eloquent\Model;

class Ct_detalle_compra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_compra';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function termino()
    {

        return $this->belongsTo('Sis_medico\Ct_compras', 'id_ct_compras', 'id');
    }

    public function afActivo()
    {
        return $this->belongsTo('Sis_medico\AfActivo', 'codigo', 'codigo');
    }

    public function cabecera()
    {

        return $this->belongsTo('Sis_medico\Ct_compras', 'id_ct_compras', 'id');
    }
    public static function getDetalles($id)
    {
        return Ct_detalle_compra::leftjoin('ct_productos as p', 'ct_detalle_compra.codigo', '=', 'p.codigo')
            ->where('id_ct_compras', $id)
            ->get([
                'ct_detalle_compra.precio',
                'ct_detalle_compra.descuento',
                'ct_detalle_compra.iva',
                'ct_detalle_compra.cantidad',
                'ct_detalle_compra.porcentaje',
                'ct_detalle_compra.codigo',
                'ct_detalle_compra.extendido',
                'ct_detalle_compra.nombre',
                'ct_detalle_compra.bodega',
                'ct_detalle_compra.detalle',
                'ct_detalle_compra.codigoref',
                'p.descripcion'
            ]);
    }

    public static function getDetallesGastos($id)
    {
        return Ct_detalle_compra::join('plan_cuentas as p', 'ct_detalle_compra.codigo', '=', 'p.id')
            ->where('id_ct_compras', $id)
            ->get([
                'ct_detalle_compra.precio',
                'ct_detalle_compra.descuento',
                'ct_detalle_compra.iva',
                'ct_detalle_compra.cantidad',
                'ct_detalle_compra.porcentaje',
                'ct_detalle_compra.codigo',
                'ct_detalle_compra.extendido',
                'ct_detalle_compra.nombre',
                'ct_detalle_compra.bodega',
                'ct_detalle_compra.detalle',
                'ct_detalle_compra.codigoref',
                'p.nombre',
                'p.descripcion'
            ]);
    }
}
