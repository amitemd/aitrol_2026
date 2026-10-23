<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvCabMovimientos;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Movimiento;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pedido';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proveedor()
    {
        return $this->belongsto('Sis_medico\Proveedor', 'id_proveedor');
    }

    public function empresa()
    {
        return $this->belongsto('Sis_medico\Empresa', 'id_empresa');
    }

    public function detalle()
    {
        return $this->hasMany('Sis_medico\Movimiento', 'id_pedido')->orderBy('id_bodega');
    }

    public function movimientos()
    {
        return $this->hasMany('Sis_medico\Movimiento', 'id_pedido');
    }

    public function movimiento_inv()
    {
        return $this->belongsto('Sis_medico\InvCabMovimientos', 'id', 'id_pedido');
    }

    public function bodega()
    {
        return $this->belongsto('Sis_medico\Bodega', 'id_bodega');
    }

    public static function existencia($serie, $id_bodega)
    {
        # existencia
        $existencia = 0;
        $invserie = InvInventarioSerie::where('serie',$serie)
                                        ->where('id_bodega',$id_bodega)
                                        ->where('estado',1)->first();
        if (isset($invserie->id)){
            $existencia = $invserie->existencia;
        }
        return $existencia;
    }

    public static function cant_traslado($serie)
    {
        # traslado
        $traslado   = 0;
        $traslados  = InvCabMovimientos::where('inv_cab_movimientos.estado','!=',0)
                                        ->where('det.serie',$serie)
                                        ->where('det.estado','!=',0)
                                        ->where('doc.abreviatura_documento','TRA')
                                        ->join('inv_documentos_bodegas as doc', 'inv_cab_movimientos.id_documento_bodega', 'doc.id')
                                        ->join('inv_det_movimientos as det', 'inv_cab_movimientos.id', 'det.id_inv_cab_movimientos')
                                        ->get();

        if (!$traslados->isEmpty()) {
            $traslado = $traslados->sum('cantidad');
        }
        return $traslado;
    }

    public static function facturado($serie)
    {
        $fact   = 0;
        $facturado  = Detalle_Pedido::where('serie', $serie)
                                    ->where('estado','!=',0)
                                    ->get();
        if (!$facturado->isEmpty()) {
            $fact = $facturado->sum('cantidad');
        }
        return $fact;
    }

    public static function egreso_paciente($serie)
    {
        $egr    = false;
        $egreso = Movimiento::where('movimiento.serie', $serie)
                                    ->join('movimiento_paciente as mp', 'movimiento.id', 'mp.id_movimiento')
                                    ->where('movimiento.estado','!=',0)
                                    ->get();
        if (!$egreso->isEmpty()) {
            $egr = true;
        }
        return $egr;
    }

    public function tipo_movimiento(){
        return $this->belongsTo("Sis_medico\invTipoMovimiento", "tipo");
    }

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function det_movimientos()
    {
        return $this->belongsTo('Sis_medico\InvDetMovimientos', 'id_pedido');
    }

    public function det_movi()
    {
        return $this->hasMany('Sis_medico\InvDetMovimientos', 'id_pedido');
    }
}
