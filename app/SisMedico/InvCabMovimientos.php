<?php

namespace Sis_medico;

use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Configuraciones;
//use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;

class InvCabMovimientos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_cab_movimientos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function pedido()
    {
        return $this->belongsTo('Sis_medico\Pedido', 'id_pedido', 'id');
    }

    public function bodega_origen()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega_origen', 'id');
    }

    public function bodega_destino()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega_destino', 'id');
    }

    public function documento_bodega()
    {
        return $this->belongsTo('Sis_medico\InvDocumentosBodegas', 'id_documento_bodega', 'id');
    }

    public function transaccion_bodega()
    {
        return $this->belongsTo('Sis_medico\InvTransaccionesBodegas', 'id_transaccion_bodega', 'id');
    }

    public function estado()
    {
        return $this->belongsTo('Sis_medico\InvEstadoMovimientos', 'id_movimiento_estado', 'id');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\InvDetMovimientos', 'id_inv_cab_movimientos', 'id')->where('estado', 1);
    }

    public function detalles_todos()
    {
        return $this->hasMany('Sis_medico\InvDetMovimientos', 'id_inv_cab_movimientos', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function transito_proforma()
    {
        return $this->belongsTo('Sis_medico\Transito_Proforma', 'id', 'id_transito');
    }

    public static function traslados($fecha_desde = "", $fecha_hasta = "")
    {
        $mes_actual = date("m");
        $fechas = getdate();
        $dia_actual = $fechas["mday"];
        $anio_actual = $fechas["year"];
        //dd($fechas);

        //dd("fecha desde: {$fecha_desde}-- fecha hasta: {$fecha_hasta}");
        $traslados = InvCabMovimientos::where('estado', 1)
            ->join('inv_documentos_bodegas', 'inv_documentos_bodegas.id', '=', 'inv_cab_movimientos.id_documento_bodega')
            ->where('abreviatura_documento', 'TRA')
            ->select('inv_cab_movimientos.*')
            ->orderBy('inv_cab_movimientos.id', 'desc');
        //dd($traslados);
        if ($fecha_desde == "" and $fecha_hasta == "") {

            $traslados = $traslados->where('inv_cab_movimientos.fecha', '>=', ["{$anio_actual}/{$mes_actual}/{$dia_actual} 00:00:00"])->get();
        } else {
            if ($fecha_desde != "" and $fecha_hasta == "") {

                $traslados = $traslados->where('inv_cab_movimientos.fecha', '>=', ["{$fecha_desde} 00:00:00"])->get();
            } else if ($fecha_desde == "" and $fecha_hasta != "") {

                $traslados = $traslados->where('inv_cab_movimientos.fecha', '<=', ["{$fecha_hasta} 00:00:00"])->get();
            } else if ($fecha_desde != "" and $fecha_hasta != "") {
                $traslados = $traslados->whereBetween('fecha', [$fecha_desde . " 00:00:00", $fecha_hasta . " 00:00:00"])->get();
            }
        }

        return $traslados;
    }

    public static function ingresosEgresosVarios($fecha_desde = "", $fecha_hasta = "")
    {
        $mes_actual = date("m");

        //dd("fecha desde: {$fecha_desde}-- fecha hasta: {$fecha_hasta}");
        $movimientos = DB::table('inv_cab_movimientos')->where('estado', 1)
            ->join('inv_documentos_bodegas', 'inv_documentos_bodegas.id', '=', 'inv_cab_movimientos.id_documento_bodega')
            ->leftjoin('pedido as p', 'p.id', '=', 'inv_cab_movimientos.id_pedido')
            // ->where('abreviatura_documento','TRA')
            ->where('inv_cab_movimientos.id_empresa', Session::get('id_empresa'))
            ->whereIn('abreviatura_documento', array('INV', 'EGV', 'IVR', 'EVC', 'ENR',  'IER', 'EER', 'EMC', 'EPR', 'EPI', 'ECC'))
            ->whereNull('p.deleted_at')
            ->select('inv_cab_movimientos.*')
            ->orderBy('inv_cab_movimientos.id', 'desc');
        //dd($movimientos);
        if ($fecha_desde == "" and $fecha_hasta == "") {

            $movimientos = $movimientos->where('inv_cab_movimientos.fecha', '>=', ["2021/{$mes_actual}/01 00:00:00"])->get();
        } else {
            if ($fecha_desde != "" and $fecha_hasta == "") {

                $movimientos = $movimientos->where('inv_cab_movimientos.fecha', '>=', ["{$fecha_desde} 00:00:00"])->get();
            } else if ($fecha_desde == "" and $fecha_hasta != "") {

                $movimientos = $movimientos->where('inv_cab_movimientos.fecha', '<=', ["{$fecha_hasta} 23:59:59"])->get();
            } else if ($fecha_desde != "" and $fecha_hasta != "") {
                $movimientos = $movimientos->whereBetween('inv_cab_movimientos.fecha', [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])->get();
            }
        }

        return $movimientos;
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    public static function calcularTotalCabMovimiento($id)
    {
        $cab_mov_inv = InvCabMovimientos::find($id);
        if (isset($cab_mov_inv)) {
            $subt  = $cab_mov_inv->detalles->sum('subtotal');
            $desc  = $cab_mov_inv->detalles->sum('descuento');
            $iva   = $cab_mov_inv->detalles->sum('iva');
            $total = $cab_mov_inv->detalles->sum('total');

            $cab_mov_inv->descuento  = $desc;
            $cab_mov_inv->subtotal   = $subt;
            $cab_mov_inv->subtotal_0 = 0;
            $cab_mov_inv->iva        = $iva;
            $cab_mov_inv->total      = $total;
            $cab_mov_inv->save();
            return "ok";
        } else {
            return "error";
        }
    }

    public static function crearTrasladoPedido($id_pedido, $id_documento_origen)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        # movimiento origen
        $cab_movimiento = InvCabMovimientos::find($id_documento_origen);
        # creacion de traslados de documento de pedido
        # traigo las bodegas a las que se realizaron los movimientos
        $pedido     = Pedido::find($id_pedido);
        $movbodegas = Movimiento::where('id_pedido', $id_pedido)
            ->where('id_bodega', '!=', $pedido->id_bodega)
            ->select(DB::raw('distinct(id_bodega)'))
            ->get();
        foreach ($movbodegas as $value) {
            $movimientos = Movimiento::where('id_pedido', $id_pedido)
                ->where('id_bodega', $value->id_bodega)
                ->get();
            # creo la cabecera del traslado #
            $documento = invDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipo($value->id_bodega, 'T');
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $value->id_bodega)
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $pedido->id_bodega;
                $cab_mov_inv->id_bodega_destino     = $value->id_bodega;
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $pedido->observaciones;
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->num_doc_ext           = $pedido->pedido;
                $cab_mov_inv->num_doc_cont          = $pedido->factura;
                $cab_mov_inv->observacion           = 'TRASLADO ' . str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->id_docum_origen       = $id_documento_origen;
                $cab_mov_inv->id_pedido             = $pedido->id;
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();
                $acum_subt  = 0;
                $acum_desc  = 0;
                $acum_iva   = 0;
                $acum_total = 0;
                foreach ($movimientos as $det) {
                    # consulto el detalle
                    $mov_ant = InvDetMovimientos::where('id_detalle_pedido', $det->id)->first();
                    if (isset($cab_movimiento->documento_bodega->id) and $cab_movimiento->documento_bodega->tipo != '') {
                        $tipo = $cab_movimiento->documento_bodega->tipo;
                    } else {
                        $tipo = 'C';
                    }
                    $inventario = InvInventario::getInventario($mov_ant->id_producto, $pedido->id_bodega, $tipo);
                    if (!isset($inventario->id)) {
                        $inventario = InvInventario::setNeoInventario($mov_ant->id_producto, $pedido->id_bodega, $tipo, 0, 0);
                    }

                    # creo los detalles del traslado
                    $det_mov_inv                         = new InvDetMovimientos;
                    $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                    $det_mov_inv->id_producto            = $mov_ant->id_producto;
                    $det_mov_inv->serie                  = $mov_ant->serie;
                    $det_mov_inv->lote                   = $mov_ant->lote;
                    $det_mov_inv->fecha_vence            = $mov_ant->fecha_vence;
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->cantidad               = $mov_ant->cantidad;
                    $det_mov_inv->cant_uso               = $mov_ant->cant_uso;
                    $det_mov_inv->valor_unitario         = $mov_ant->valor_unitario;
                    $det_mov_inv->subtotal               = $mov_ant->subtotal;
                    $det_mov_inv->descuento              = $mov_ant->descuento;
                    $det_mov_inv->iva                    = $mov_ant->iva;
                    $det_mov_inv->total                  = $mov_ant->total;
                    $det_mov_inv->motivo                 = 'TRASLADO PEDIDO ' . $cab_mov_inv->numero_documento;
                    $det_mov_inv->id_detalle_origen      = $det->id;
                    $det_mov_inv->ip_creacion            = $ip_cliente;
                    $det_mov_inv->ip_modificacion        = $ip_cliente;
                    $det_mov_inv->id_usuariocrea         = $idusuario;
                    $det_mov_inv->id_usuariomod          = $idusuario;
                    $det_mov_inv->save();
                    # acumular valores
                    $acum_subt += $mov_ant->subtotal;
                    $acum_desc += $mov_ant->descuento;
                    $acum_iva += $mov_ant->iva;
                    $acum_total += $mov_ant->total;
                }
                $cab_mov_inv->descuento  = $acum_desc;
                $cab_mov_inv->subtotal   = $acum_subt;
                $cab_mov_inv->subtotal_0 = 0;
                $cab_mov_inv->iva        = $acum_iva;
                $cab_mov_inv->total      = $acum_total;
                $cab_mov_inv->save();
                $kardex = InvKardex::setKardex($cab_mov_inv->id);
            }
            # crear los ingresos de traslados
            InvCabMovimientos::ingresoTrasladoPedido($cab_mov_inv->id, $id_documento_origen);
        }
    }

    public static function ingresoTrasladoPedido($id_cab_movimiento/* cab traslado */, $id_documento_origen)
    {
        # movimiento origen
        $cab_movimiento = InvCabMovimientos::find($id_documento_origen);
        # creo la cabecera del traslado #
        $cabcera       = InvCabMovimientos::find($id_cab_movimiento);
        $num_documento = $cabcera->numero_documento;
        $documento     = invDocumentosBodegas::where('abreviatura_documento', 'INT')->first();
        $secuencia     = InvDocumentosBodegas::getSecueciaTipoDocum($cabcera->id_bodega_destino, 'INT');
        $transaccion   = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
            ->where('id_bodega', $cabcera->id_bodega_destino)
            ->first();
        $cabcera->id_documento_bodega   = $documento->id;
        $cabcera->id_transaccion_bodega = $transaccion->id;
        $cabcera->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cabcera->observacion           = 'INGRESO POR TRASLADO';
        $cabcera->fecha                 = date('Y-m-d');
        $cabcera->id_docum_origen       = $id_cab_movimiento;
        // $cab_mov_inv                        = InvCabMovimientos::create($cabcera);

        ##      CABECERA
        $cab_mov_inv                        = new InvCabMovimientos;
        $cab_mov_inv->id_documento_bodega   = $documento->id;
        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
        $cab_mov_inv->id_bodega_origen      = $cabcera->id_bodega_origen;
        $cab_mov_inv->id_bodega_destino     = $cabcera->id_bodega_destino;
        $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cab_mov_inv->observacion           = $cabcera->observacion;
        $cab_mov_inv->fecha                 = date('Y-m-d');
        $cab_mov_inv->num_doc_ext           = $cabcera->num_doc_ext;
        $cab_mov_inv->num_doc_cont          = $cabcera->num_doc_cont;
        $cab_mov_inv->descuento             = $cabcera->descuento;
        $cab_mov_inv->subtotal              = $cabcera->subtotal;
        $cab_mov_inv->subtotal_0            = $cabcera->subtotal_0;
        $cab_mov_inv->iva                   = $cabcera->iva;
        $cab_mov_inv->total                 = $cabcera->total;
        $cab_mov_inv->id_docum_origen       = $cabcera->id;
        $cab_mov_inv->id_empresa            = Session::get('id_empresa');
        $cab_mov_inv->id_pedido             = $cabcera->id_pedido;
        $cab_mov_inv->ip_creacion           = $cabcera->ip_creacion;
        $cab_mov_inv->ip_modificacion       = $cabcera->ip_modificacion;
        $cab_mov_inv->id_usuariocrea        = $cabcera->id_usuariocrea;
        $cab_mov_inv->id_usuariomod         = $cabcera->id_usuariomod;
        $cab_mov_inv->save();
        ##      D E T A L L E S
        foreach ($cabcera->detalles as $detalle) {
            // if ($detalle->kardex == 0) {
            if (isset($cab_movimiento->documento_bodega->id) and $cab_movimiento->documento_bodega->tipo != '') {
                $tipo = $cab_movimiento->documento_bodega->tipo;
            } else {
                $tipo = 'C';
            }
            $inventario = InvInventario::getInventario($detalle->id_producto, $cabcera->id_bodega_destino, $tipo);
            if (!isset($inventario->id)) {
                $inventario = InvInventario::setNeoInventario($detalle->id_producto, $cabcera->id_bodega_destino, $tipo, 0, 0);
            }
            ##       creo los detalles del traslado
            $det_mov_inv                         = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto            = $detalle->id_producto;
            $det_mov_inv->serie                  = $detalle->serie;
            $det_mov_inv->lote                   = $detalle->lote;
            $det_mov_inv->fecha_vence            = $detalle->fecha_vence;
            $det_mov_inv->serie                  = $detalle->serie;
            $det_mov_inv->id_inv_inventario      = $inventario->id;
            $det_mov_inv->cantidad               = $detalle->cantidad;
            $det_mov_inv->cant_uso               = $detalle->cant_uso;
            $det_mov_inv->valor_unitario         = $detalle->valor_unitario;
            $det_mov_inv->subtotal               = $detalle->subtotal;
            $det_mov_inv->descuento              = $detalle->descuento;
            $det_mov_inv->iva                    = $detalle->iva;
            $det_mov_inv->total                  = $detalle->total;
            $det_mov_inv->motivo                 = 'INGRESO TRASLADO PEDIDO ' . $num_documento;
            $det_mov_inv->id_detalle_origen      = $detalle->id;
            $det_mov_inv->cantidad_usos_kardex   = $detalle->cantidad_usos_kardex;
            $det_mov_inv->ip_creacion            = $detalle->ip_creacion;
            $det_mov_inv->ip_modificacion        = $detalle->ip_modificacion;
            $det_mov_inv->id_usuariocrea         = $detalle->id_usuariocrea;
            $det_mov_inv->id_usuariomod          = $detalle->id_usuariomod;
            $det_mov_inv->save();
            // }
        }
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        InvCabMovimientos::registrarTrasladoPedido($id_documento_origen/* padre */, $id_cab_movimiento/* traslado */, $cab_mov_inv->id/* ingreso tralado */);
    }

    public static function actualizarIngresoTrasladoPedido($id_cab_movimiento/* documento de origen */)
    {
        $cab_mov_inv = InvCabMovimientos::find($id_cab_movimiento);
        #   anulo los traslados y los ingresos
        InvCabMovimientos::anulacionIngresoTrasladoPedido($id_cab_movimiento/* documento de origen */);
        #   se crean los traslados de los pedidos
        InvCabMovimientos::crearTrasladoPedido($cab_mov_inv->id_pedido, $cab_mov_inv->id);
    }

    public static function anulacionIngresoTrasladoPedido($id_cab_movimiento/* documento de origen */)
    {
        # anulo los movimientos
        $traslados = InvTrasladosBodegas::where('id_inv_cab_mov_origen', $id_cab_movimiento)->get();
        foreach ($traslados as $row) {
            #   T   R   A   S   L   A   D   O   S   #
            $cab_mov_inv         = InvCabMovimientos::find($row->id_inv_cab_traslado);
            $cab_mov_inv->estado = 0;
            $cab_mov_inv->save();
            #   K   A   R   D   E   X
            foreach ($cab_mov_inv->detalles as $det) {
                $kardex = InvKardex::where('id_inv_det_movimientos', $det->id)->where('estado', 1)->first();
                if (isset($kardex->id)) {
                    $kardex->estado = 0;
                    $kardex->save();
                }
                #   I   N   V   E   N   T   A   R   I   O
                $inventario = InvInventario::where('id', $det->id_inv_inventario)->where('estado', 1)->first();
                if (isset($inventario->id)) {
                    $inventario->existencia += $det->cantidad;
                    $inventario->existencia_uso += $det->cant_uso;
                    $inventario->save();
                }
                #   S   E   R   I   E
                $serie = InvInventarioSerie::where('id_inv_inventario', $det->id_inv_inventario)->where('serie', $det->serie)->where('estado', 1)->first();
                if (isset($serie->id)) {
                    $serie->existencia += $det->cantidad;
                    $serie->existencia_uso += $det->cant_uso;
                    $serie->save();
                }
            }
            #   D   E   T   A   L   L   E   S   #
            #   anulo los detalles del traslado
            InvDetMovimientos::where('estado', 1)
                ->where('id_inv_cab_movimientos', $row->id_inv_cab_traslado)
                ->update(['estado' => 0]);

            #   I   N   G   R   E   S   O
            $cab_mov_inv         = InvCabMovimientos::find($row->id_inv_cab_ingreso);
            $cab_mov_inv->estado = 0;
            $cab_mov_inv->save();
            #   K   A   R   D   E   X
            foreach ($cab_mov_inv->detalles as $det) {
                $kardex = InvKardex::where('id_inv_det_movimientos', $det->id)->where('estado', 1)->first();
                if (isset($kardex->id)) {
                    $kardex->estado = 0;
                    $kardex->save();
                }
                #   I   N   V   E   N   T   A   R   I   O
                $inventario = InvInventario::where('id', $det->id_inv_inventario)->where('estado', 1)->first();
                if (isset($inventario->id)) {
                    $inventario->existencia -= $det->cantidad;
                    if ($inventario->existencia < 0) {
                        $inventario->existencia = 0;
                    }
                    $inventario->existencia_uso -= $det->cant_uso;
                    if ($inventario->existencia_uso < 0) {
                        $inventario->existencia_uso = 0;
                    }
                    $inventario->save();
                }
                #   S   E   R   I   E
                $serie = InvInventarioSerie::where('id_inv_inventario', $det->id_inv_inventario)->where('serie', $det->serie)->where('estado', 1)->first();
                if (isset($serie->id)) {
                    $serie->existencia -= $det->cantidad;
                    if ($serie->existencia < 0) {
                        $serie->existencia = 0;
                    }
                    $serie->existencia_uso -= $det->cant_uso;
                    if ($serie->existencia_uso < 0) {
                        $serie->existencia_uso = 0;
                    }
                    $serie->save();
                }
            }
            #   D   E   T   A   L   L   E   S   #
            #   anulo los detalles del traslado
            InvDetMovimientos::where('estado', 1)
                ->where('id_inv_cab_movimientos', $row->id_inv_cab_ingreso)
                ->update(['estado' => 0]);
        }
    }

    public static function registrarTrasladoPedido($id_cab_mov_origen = null, $id_traslado = null, $id_ingreso = null)
    {
        # se registra el traslado
        if ($id_cab_mov_origen != null and $id_traslado != null and $id_ingreso != null) {
            // $origen                             = InvCabMovimientos::find($id_cab_mov_origen);
            // $destino                            = InvCabMovimientos::find($id_traslado);
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;

            $traslado                        = new InvTrasladosBodegas;
            $traslado->id_inv_cab_mov_origen = $id_cab_mov_origen;
            $traslado->id_inv_cab_traslado   = $id_traslado;
            $traslado->id_inv_cab_ingreso    = $id_ingreso;
            $traslado->id_empresa            = Session::get('id_empresa');
            $traslado->ip_creacion           = $ip_cliente;
            $traslado->ip_modificacion       = $ip_cliente;
            $traslado->id_usuariocrea        = $idusuario;
            $traslado->id_usuariomod         = $idusuario;
            $traslado->save();
        }
    }

    # 1. creo un documento de bodega (cab/det) [I] id: x (Doc ING)
    # 2. creo los traslados de bodega (que son documentos de egreso desde la bodega de entrega a las otras bodegas) [E] id_documento_origen: x (Doc EGR)
    # 3. creo los ingresos de bodega (que son documentos de ingreso desde la bodega de entrega a las otras bodegas) [I] id_documento_origen: id_traslado (Doc ING)
    # fin
    # ANULACION
    # 1. Anulo paso 3 crear egresos (Doc ING)[i] >> (Doc EGR)[e]
    # 2. Anulo paso 2 crear ingresos (Doc EGR)[e] >> (Doc ING)[i]
    # 3. Anulo paso 1 crear egreso (Doc ING)[i] >> (Doc EGR)[e]

    public static function anularTransaccionBodega($id_cab_movimiento = null)
    {
        #   ...
        $cabcera       = InvCabMovimientos::find($id_cab_movimiento);
        $num_documento = $cabcera->numero_documento;
        $tipo_mov      = "";
        $id_bodega     = "";
        if ($cabcera->transaccion_bodega->documentoBodega->tipo_movimiento->tipo == 'I') {
            $tipo_mov  = "EGR";
            $id_bodega = $cabcera->id_bodega_destino;
        } else {
            $tipo_mov  = "ING";
            $id_bodega = $cabcera->id_bodega_origen;
        }

        if (Auth::user()->id == '0941096372') {
           // dd($cabcera . ' -- ' . $id_cab_movimiento);
        }

        // dd($cabcera->transaccion_bodega->documentoBodega->tipo_movimiento->tipo." ".$id_cab_movimiento);
        $documento   = invDocumentosBodegas::where('abreviatura_documento', $tipo_mov)->first();
        $secuencia   = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, $tipo_mov);
        $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
            ->where('id_bodega', $id_bodega)
            ->first();
        ##      CABECERA
        $cab_mov_inv                        = new InvCabMovimientos;
        $cab_mov_inv->id_documento_bodega   = $documento->id;
        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
        $cab_mov_inv->id_bodega_origen      = $id_bodega;
        // $cab_mov_inv->id_bodega_destino     = $cabcera->id_bodega_origen;
        $cab_mov_inv->numero_documento = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cab_mov_inv->observacion      = 'ANULACION DOC: ' . strtoupper($cabcera->documento_bodega->abreviatura_documento) . "-" . $cabcera->numero_documento;
        $cab_mov_inv->fecha            = $cabcera->fecha;
        $cab_mov_inv->num_doc_ext      = $cabcera->pedido;
        $cab_mov_inv->id_hc_procedimientos      = $cabcera->id_hc_procedimientos;
        $cab_mov_inv->num_doc_cont     = $cabcera->factura;
        $cab_mov_inv->descuento        = $cabcera->descuento;
        $cab_mov_inv->subtotal         = $cabcera->subtotal_12;
        $cab_mov_inv->subtotal_0       = $cabcera->subtotal_0;
        $cab_mov_inv->iva              = $cabcera->iva;
        $cab_mov_inv->total            = $cabcera->total;
        $cab_mov_inv->id_docum_origen  = $id_cab_movimiento;
        $cab_mov_inv->id_empresa       = $cabcera->id_empresa;
        $cab_mov_inv->ip_creacion      = $cabcera->ip_creacion;
        $cab_mov_inv->ip_modificacion  = $cabcera->ip_modificacion;
        $cab_mov_inv->id_usuariocrea   = $cabcera->id_usuariocrea;
        $cab_mov_inv->id_usuariomod    = $cabcera->id_usuariomod;
        $cab_mov_inv->save();

        foreach ($cabcera->detalles as $detalle) {
            ##       creo los detalles del traslado
            $det_mov_inv                         = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto            = $detalle->id_producto;
            $det_mov_inv->id_inv_inventario      = $detalle->id_inv_inventario;
            $det_mov_inv->serie                  = $detalle->serie;
            $det_mov_inv->lote                   = $detalle->lote;
            $det_mov_inv->fecha_vence            = $detalle->fecha_vence;
            $det_mov_inv->cantidad               = $detalle->cantidad;
            $det_mov_inv->cant_uso               = $detalle->cant_uso;
            $det_mov_inv->valor_unitario         = $detalle->valor_unitario;
            $det_mov_inv->subtotal               = $detalle->subtotal;
            $det_mov_inv->descuento              = $detalle->descuento;
            $det_mov_inv->iva                    = $detalle->iva;
            $det_mov_inv->total                  = $detalle->total;
            $det_mov_inv->motivo                 = 'ANULACION ' . $num_documento;
            $det_mov_inv->id_detalle_origen      = $detalle->id;
            $det_mov_inv->ip_creacion            = $detalle->ip_creacion;
            $det_mov_inv->ip_modificacion        = $detalle->ip_modificacion;
            $det_mov_inv->id_usuariocrea         = $detalle->id_usuariocrea;
            $det_mov_inv->id_usuariomod          = $detalle->id_usuariomod;
            $det_mov_inv->save();
        }
        #   actualizacion de kardex
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        InvCabMovimientos::logAnulacion($cabcera->id, $cab_mov_inv->id);
    }

    public static function logAnulacion($id_ant, $id_new)
    {
        $user = Auth::user();
        $ip_usuario = $_SERVER["REMOTE_ADDR"];
        $data = [
            "id_cab_anterior" => $id_ant,
            "id_cab_new" => $id_new,
            "id_usuariomod" => $user->id,
            "id_usuariocrea" => $user->id,
            "ip_creacion" => $ip_usuario,
            "ip_modificacion" => $ip_usuario,
        ];
        AnulacionCabMov::create($data);
    }

    public static function actualizarIngresoPedido($id_pedido)
    {
        ## actualizacion del documento de bodega padre que se genero en el ingreso del pedido
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $acum_subt  = 0;
        $acum_desc  = 0;
        $acum_iva   = 0;
        $acum_total = 0;
        # consulto el pedido
        $pedido = Pedido::find($id_pedido);
        # busco el documento q genero el pedido
        $cab_mov_inv = InvCabMovimientos::where('id_pedido', $id_pedido)->first();
        if (isset($cab_mov_inv->id)) {
            $cab_mov_inv->observacion     = $pedido->observaciones;
            $cab_mov_inv->num_doc_ext     = $pedido->pedido;
            $cab_mov_inv->num_doc_cont    = $pedido->factura;
            $cab_mov_inv->descuento       = $pedido->descuento;
            $cab_mov_inv->subtotal        = $pedido->subtotal_12;
            $cab_mov_inv->subtotal_0      = $pedido->subtotal_0;
            $cab_mov_inv->iva             = $pedido->iva;
            $cab_mov_inv->total           = $pedido->total;
            $cab_mov_inv->ip_modificacion = $ip_cliente;
            $cab_mov_inv->id_usuariomod   = $idusuario;
            $cab_mov_inv->save();

            # actualizo el estado a los detalles a 0 para eliminar logicamente todos los detalles
            InvDetMovimientos::where('estado', 1)
                ->where('id_inv_cab_movimientos', $cab_mov_inv->id)
                ->update(['estado' => 0]);
            # recorro los detalles si no existen los creo #
            foreach ($pedido->detalle as $movimiento) {
                $iva      = 0;
                $cant_uso = 0;
                $producto = Producto::find($movimiento->id_producto);
                if (isset($producto->iva) && $producto->iva == 1) {
                    $conf = Ct_Configuraciones::find(3);
                    $iva  = ($movimiento->cantidad * $movimiento->precio) * $conf->iva;
                }
                if (isset($producto->usos) and $producto->usos != null) {
                    $cant_uso = $producto->usos;
                }
                if ($cant_uso == null or $cant_uso < 0) {
                    $cant_uso = 0;
                }
                $det_mov_inv = InvDetMovimientos::where('id_detalle_pedido', $movimiento->id)
                    ->first();
                if (isset($det_mov_inv->id)) {
                    # si el movimiento tiene cambios en bodega  serie
                    if ($det_mov_inv->inventario->id_bodega != $movimiento->id_bodega) {
                        $inventario = InvInventario::getInventario($movimiento->id_producto, $movimiento->id_bodega);
                        if (!isset($inventario->id)) {
                            $inventario = InvInventario::setNeoInventario($movimiento->id_producto, $movimiento->id_bodega, 0, 0);
                        }
                        $det_mov_inv->id_inv_inventario = $inventario->id;
                    }
                    $det_mov_inv->cantidad        = $movimiento->cantidad;
                    $det_mov_inv->cant_uso        = $movimiento->cantidad * $cant_uso;
                    $det_mov_inv->serie           = $movimiento->serie;
                    $det_mov_inv->lote            = $movimiento->lote;
                    $det_mov_inv->fecha_vence     = $movimiento->fecha_vencimiento;
                    $det_mov_inv->valor_unitario  = $movimiento->precio;
                    $det_mov_inv->subtotal        = $movimiento->cantidad * $movimiento->precio;
                    $det_mov_inv->descuento       = $movimiento->descuento;
                    $det_mov_inv->iva             = $iva;
                    $det_mov_inv->total           = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->estado          = 1;
                    $det_mov_inv->ip_modificacion = $ip_cliente;
                    $det_mov_inv->id_usuariomod   = $idusuario;
                    $det_mov_inv->save();
                    #   ACTUALIZAR LOS CAMBIOS EN LOTE Y SERIE
                    InvDetMovimientos::where('serie', $movimiento->serie)
                        ->update([
                            'lote' => $movimiento->lote,
                            'fecha_vence'    => $movimiento->fecha_vencimiento
                        ]);
                    InvInventarioSerie::where('serie', $movimiento->serie)
                        ->update([
                            'lote' => $movimiento->lote,
                            'fecha_vence'    => $movimiento->fecha_vencimiento
                        ]);
                } else {
                    # si el movimiento tiene cambios en bodega
                    $inventario = InvInventario::getInventario($movimiento->id_producto, $movimiento->id_bodega);
                    if (!isset($inventario->id)) {
                        $inventario = InvInventario::setNeoInventario($movimiento->id_producto, $movimiento->id_bodega, 0, 0);
                    }
                    $det_mov_inv                         = new InvDetMovimientos;
                    $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->id_producto            = $movimiento->id_producto;
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->cantidad               = $movimiento->cantidad;
                    $det_mov_inv->cant_uso               = $movimiento->cantidad * $cant_uso;
                    $det_mov_inv->serie                  = $movimiento->serie;
                    $det_mov_inv->lote                   = $movimiento->lote;
                    $det_mov_inv->fecha_vence            = $movimiento->fecha_vencimiento;
                    $det_mov_inv->valor_unitario         = $movimiento->precio;
                    $det_mov_inv->subtotal               = $movimiento->cantidad * $movimiento->precio;
                    $det_mov_inv->descuento              = $movimiento->descuento;
                    $det_mov_inv->iva                    = $iva;
                    $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->motivo                 = 'INGRESO PEDIDO';
                    $det_mov_inv->id_detalle_pedido      = $movimiento->id;
                    $det_mov_inv->ip_creacion            = $ip_cliente;
                    $det_mov_inv->ip_modificacion        = $ip_cliente;
                    $det_mov_inv->id_usuariocrea         = $idusuario;
                    $det_mov_inv->id_usuariomod          = $idusuario;
                    $det_mov_inv->save();
                }
            }
            # actualizo los traslados si tienen algun cambio #
            InvCabMovimientos::actualizarIngresoTrasladoPedido($cab_mov_inv->id);
        }
    }

    public static function ingresoTrasladoPedidoEgresoPaciente($id_cab_movimiento/* cab traslado */, $detalle, $id_movimiento_paciente)
    {

        # creo la cabecera del traslado #
        $cabcera       = InvCabMovimientos::find($id_cab_movimiento);
        $num_documento = $cabcera->numero_documento;
        $documento     = invDocumentosBodegas::where('abreviatura_documento', 'INT')->first();
        $secuencia     = InvDocumentosBodegas::getSecueciaTipoDocum($cabcera->id_bodega_destino, 'INT');
        $transaccion   = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
            ->where('id_bodega', $cabcera->id_bodega_destino)
            ->first();
        $cabcera->id_documento_bodega   = $documento->id;
        $cabcera->id_transaccion_bodega = $transaccion->id;
        $cabcera->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cabcera->observacion           = 'INGRESO POR TRASLADO';
        $cabcera->fecha                 = date('Y-m-d');
        $cabcera->id_docum_origen       = $id_cab_movimiento;
        // $cab_mov_inv                        = InvCabMovimientos::create($cabcera);

        $cab_mov_inv = DB::table('inv_cab_movimientos as c')
            ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
            ->where('d.abreviatura_documento', 'INT')
            ->where('c.id_agenda', $cabcera->id_agenda)
            ->where('c.id_hc_procedimientos', $cabcera->id_hc_procedimientos)
            ->where('c.estado', '!=', 0)
            ->select('c.*')
            ->first();
        if (!isset($cab_mov_inv->id)) {
            ##      C A B E C E R A
            $cab_mov_inv                        = new InvCabMovimientos;
            $cab_mov_inv->id_documento_bodega   = $documento->id;
            $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
            $cab_mov_inv->id_bodega_origen      = $cabcera->id_bodega_origen;
            $cab_mov_inv->id_bodega_destino     = $cabcera->id_bodega_destino;
            $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
            $cab_mov_inv->observacion           = $cabcera->observacion;
            $cab_mov_inv->fecha                 = date('Y-m-d');
            $cab_mov_inv->num_doc_ext           = $cabcera->num_doc_ext;
            $cab_mov_inv->num_doc_cont          = $cabcera->num_doc_cont;
            $cab_mov_inv->descuento             = $cabcera->descuento;
            $cab_mov_inv->subtotal              = $cabcera->subtotal;
            $cab_mov_inv->subtotal_0            = $cabcera->subtotal_0;
            $cab_mov_inv->iva                   = $cabcera->iva;
            $cab_mov_inv->total                 = $cabcera->total;
            $cab_mov_inv->id_docum_origen       = $cabcera->id;
            $cab_mov_inv->id_empresa            = Session::get('id_empresa');
            $cab_mov_inv->id_pedido             = $cabcera->id_pedido;
            $cab_mov_inv->id_agenda             = $cabcera->id_agenda;
            $cab_mov_inv->id_hc_procedimientos  = $cabcera->id_hc_procedimientos;
            $cab_mov_inv->ip_creacion           = $cabcera->ip_creacion;
            $cab_mov_inv->ip_modificacion       = $cabcera->ip_modificacion;
            $cab_mov_inv->id_usuariocrea        = $cabcera->id_usuariocrea;
            $cab_mov_inv->id_usuariomod         = $cabcera->id_usuariomod;
            $cab_mov_inv->save();
            InvCabMovimientos::registrarTrasladoPedido($cab_mov_inv->id/* padre */, $cab_mov_inv->id/* traslado */, $cab_mov_inv->id/* ingreso tralado */);
        }
        ##      D E T A L L E
        if (isset($cab_movimiento->documento_bodega->id) and $cab_movimiento->documento_bodega->tipo != '') {
            $tipo = $cab_movimiento->documento_bodega->tipo;
        } else {
            $tipo = 'C';
        }
        $inventario = InvInventario::getInventario($detalle->id_producto, $cabcera->id_bodega_destino, $tipo);
        if (!isset($inventario->id)) {
            $inventario = InvInventario::setNeoInventario($detalle->id_producto, $cabcera->id_bodega_destino, $tipo, 0, 0);
        }
        ##       creo los detalles del traslado
        $det_mov_inv                         = new InvDetMovimientos;
        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
        $det_mov_inv->id_producto            = $detalle->id_producto;
        $det_mov_inv->serie                  = $detalle->serie;
        $det_mov_inv->lote                   = $detalle->lote;
        $det_mov_inv->fecha_vence            = $detalle->fecha_vence;
        $det_mov_inv->serie                  = $detalle->serie;
        $det_mov_inv->id_inv_inventario      = $inventario->id;
        $det_mov_inv->cantidad               = $detalle->cantidad;
        $det_mov_inv->cant_uso               = $detalle->cant_uso;
        $det_mov_inv->valor_unitario         = $detalle->valor_unitario;
        $det_mov_inv->subtotal               = $detalle->subtotal;
        $det_mov_inv->descuento              = $detalle->descuento;
        $det_mov_inv->iva                    = $detalle->iva;
        $det_mov_inv->total                  = $detalle->total;
        $det_mov_inv->motivo                 = 'INGRESO TRASLADO ' . $num_documento;
        $det_mov_inv->id_detalle_origen      = $detalle->id;
        $det_mov_inv->id_movimiento_paciente = $id_movimiento_paciente;
        $det_mov_inv->ip_creacion            = $detalle->ip_creacion;
        $det_mov_inv->ip_modificacion        = $detalle->ip_modificacion;
        $det_mov_inv->id_usuariocrea         = $detalle->id_usuariocrea;
        $det_mov_inv->id_usuariomod          = $detalle->id_usuariomod;
        $det_mov_inv->save();
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
    }

    public static function documentoTrasladoCompra($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente = "")
    {
        //  TRASLADO PARA EGRESO DE PROCEDIMIENTOS EN PACIENTES
        $id_bodega_origen  = env('BODEGA_EGR_PACI1', 2); // bodega de compras
        $id_bodega_destino = env('BODEGA_COMPRA', 3); // bodega de compras
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $iva               = 0;
        $cab_mov_inv       = DB::table('inv_cab_movimientos as c')
            ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
            ->where('d.abreviatura_documento', 'TRA')
            ->where('c.id_agenda', $id_agenda)
            ->where('c.id_hc_procedimientos', $id_hc_procedimientos)
            ->where('c.id_bodega_origen', $id_bodega_origen)
            ->where('c.id_bodega_destino', $id_bodega_destino)
            ->where('c.estado', '!=', 0)
            ->select('c.*')
            ->first();
        // dd($cab_mov_inv);
        if (!isset($cab_mov_inv->id)) {
            $documento = InvDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipo($id_bodega_origen, 'T');
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $id_bodega_origen)
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $id_bodega_origen;
                $cab_mov_inv->id_bodega_destino     = $id_bodega_destino;
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $documento->abreviatura_documento . " " . strtoupper($documento->documento) . " AGENDA: " . $id_agenda;
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->id_hc_procedimientos  = $id_hc_procedimientos;
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->id_agenda             = $id_agenda;
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();
            }
        }
        # I N V E N T A R I O
        if (isset($inv_serie->inventario->producto->iva) && $inv_serie->inventario->producto->iva == 1) {
            $conf = Ct_Configuraciones::find(3);
            $iva  = ($inv_serie->inventario->costo_promedio) * $conf->iva;
        }

        # TRAIGO EL DETALLE DEL INGRESO DEL ITEM
        $inicial = InvDetMovimientos::where('serie', $inv_serie->serie)
            ->where('estado', 1)
            ->orderBy('id', 'asc')
            ->first();
        # DETALLES
        $det_mov_inv                         = new InvDetMovimientos;
        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
        $det_mov_inv->id_producto            = $inv_serie->inventario->producto->id;
        $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
        $det_mov_inv->id_procedimiento       = $id_hc_procedimientos;
        $det_mov_inv->cantidad               = $cantidad;
        $det_mov_inv->cant_uso               = $cantidad_uso;
        $det_mov_inv->serie                  = $inv_serie->serie;
        $det_mov_inv->lote                   = $inv_serie->lote;
        $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
        $det_mov_inv->valor_unitario         = $inv_serie->inventario->costo_promedio;
        $det_mov_inv->subtotal               = $inv_serie->inventario->costo_promedio;
        $det_mov_inv->descuento              = 0;
        $det_mov_inv->iva                    = $iva;
        $det_mov_inv->total                  = $inv_serie->inventario->costo_promedio + $iva;
        $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
        if (isset($inicial->cabecera)) {
            $det_mov_inv->id_pedido = $inicial->cabecera->id_pedido;
        }
        $det_mov_inv->id_movimiento_paciente = $id_movimiento_paciente;
        $det_mov_inv->ip_creacion            = $ip_cliente;
        $det_mov_inv->ip_modificacion        = $ip_cliente;
        $det_mov_inv->id_usuariocrea         = $idusuario;
        $det_mov_inv->id_usuariomod          = $idusuario;
        $det_mov_inv->save();
        // CALCULAR TOTALES
        InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
        // MOVIMIENTO EN KARDEX
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        # crear los ingresos de traslados
        InvCabMovimientos::ingresoTrasladoPedidoEgresoPaciente($cab_mov_inv->id, $det_mov_inv, $id_movimiento_paciente); # REVISAR

        return $cab_mov_inv->id;
    }

    // public static function documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente = "", $id_docum_origen = "")
    public static function documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente = "", $id_bodega_param = 2, $id_detalle_pla)
    {
        // PARA EGRESO DE PROCEDIMIENTOS EN PACIENTES
        // $id_bodega   = env('BODEGA_COMPRA', 2); // Se cambio a bodega de pentax
        $id_bodega = $id_bodega_param;
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $cab_mov_inv = DB::table('inv_cab_movimientos as c')
            ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
            ->where('d.abreviatura_documento', 'EGP')
            ->where('c.id_agenda', $id_agenda)
            ->where('c.id_hc_procedimientos', $id_hc_procedimientos)
            ->where('c.id_bodega_origen', $id_bodega)
            // ->where('c.id_bodega_destino', $id_bodega_destino)
            ->where('c.id_empresa', Session::get('id_empresa'))
            ->where('c.estado', '!=', 0)
            ->select('c.*')
            ->first();
        $cab_mov_inv = null;

        if (!is_null($inv_serie->inventario)) {
            $valor_precio = $inv_serie->inventario->costo_promedio;
        } else {
            $movimiento = Movimiento::where('serie', $inv_serie->serie)->orderBy('created_at', 'Desc')->first();
            $valor_precio = $movimiento->precio;
        }
        if (!isset($cab_mov_inv->id)) {
            # creo la cabecera del traslado #
            $documento = InvDocumentosBodegas::where('abreviatura_documento', 'EGP')->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, 'EGP');
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $id_bodega)
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $id_bodega;
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $documento->abreviatura_documento . " " . strtoupper($documento->documento) . " AGENDA: " . $id_agenda;
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->id_hc_procedimientos  = $id_hc_procedimientos;
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->id_agenda             = $id_agenda;
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();
            }
        }
        # I V A
        $iva = 0;
        /*if (isset($inv_serie->inventario->producto->iva) && $inv_serie->inventario->producto->iva == 1) {
        $conf = Ct_Configuraciones::find(3);
        $iva  = ($inv_serie->inventario->costo_promedio) * $conf->iva;
        }*/
        # TRAIGO EL DETALLE DEL INGRESO DEL ITEM
        $inicial = InvDetMovimientos::where('serie', $inv_serie->serie)
            ->where('estado', 1)
            ->orderBy('id', 'asc')
            ->first();
        # DETALLES
        $det_mov_inv                         = new InvDetMovimientos;
        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
        // $det_mov_inv->id_producto            = $inv_serie->inventario->producto->id;
        $det_mov_inv->id_producto            = $inv_serie->id_producto;
        $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
        $det_mov_inv->id_procedimiento       = $id_hc_procedimientos;
        $det_mov_inv->cantidad               = $cantidad;
        $det_mov_inv->cant_uso               = $cantidad_uso;
        $det_mov_inv->serie                  = $inv_serie->serie;
        $det_mov_inv->lote                   = $inv_serie->lote;
        $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
        $det_mov_inv->valor_unitario         = $valor_precio;
        // $det_mov_inv->subtotal               = $valor_precio;
        $det_mov_inv->subtotal               = ($valor_precio * $cantidad);
        $det_mov_inv->descuento              = 0;
        $det_mov_inv->iva                    = $iva;
        // $det_mov_inv->total                  = $inv_serie->inventario->costo_promedio + $iva;
        $det_mov_inv->total                  = ($valor_precio * $cantidad) + $iva;
        $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
        if (isset($inicial->cabecera)) {
            $det_mov_inv->id_pedido = $inicial->cabecera->id_pedido;
        }
        $det_mov_inv->id_movimiento_paciente = $id_movimiento_paciente;
        $det_mov_inv->ip_creacion            = $ip_cliente;
        $det_mov_inv->ip_modificacion        = $ip_cliente;
        $det_mov_inv->id_usuariocrea         = $idusuario;
        $det_mov_inv->id_usuariomod          = $idusuario;
        $det_mov_inv->save();

        $serie = $inv_serie->serie;
        $id_inv_det_mov = $det_mov_inv->id;
        //KARDEX NUEVO
        $regreso = InvCabMovimientos::descontar_kardex_nuevo($serie, $id_bodega, $id_inv_det_mov, $id_detalle_pla);
        // CALCULAR TOTALES
        InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
        if ($regreso == 0) {
            // MOVIMIENTO EN KARDEX
            $kardex = InvKardex::setKardex($cab_mov_inv->id);
        }
        // MOVIMIENTO EN KARDEX
        //$kardex = InvKardex::setKardex($cab_mov_inv->id);
        return $cab_mov_inv->id;
    }
    public static function descontar_kardex_nuevo($serie, $id_bodega, $id_inv_det_mov, $id_detalle_pla)
    {
        $ip_cliente             = $_SERVER["REMOTE_ADDR"];
        $idusuario              = Auth::user()->id;


        $detalle_mov = InvDetMovimientos::find($id_inv_det_mov);
        $detalle_planilla = Planilla_Detalle::find($id_detalle_pla);
        $cabecera_movimiento = $detalle_planilla->cabecera;
        $id_agenda              = $cabecera_movimiento->id_agenda;
        $id_hc_procedimientos   = $cabecera_movimiento->id_hc_procedimientos;
        $regresa = 0;
        $inventario = InvInventarioSerie::where('serie', $serie)->where('id_bodega', $id_bodega)->first();
        if (!is_null($inventario)) {
            if ($inventario->producto->kardex_uso == 1) {
                if ($inventario->cantidad_usos_kardex > 0) {
                    $regresa = 1;
                    if ($inventario->cantidad_usos_kardex == 1) {
                        $regresa = 0;
                    }
                    $primer_kardex = InvKardexNuevo::where('serie', $serie)->where('id_bodega', $id_bodega)->where('tipo', 'T')->first(); //dd($serie,$bodega,$primer_kardex);

                    $cantidad_serie = $inventario->cantidad_usos_kardex;
                    $kardex                  = new InvKardexNuevo;
                    $kardex->id_inv_inventario = $inventario->id_inv_inventario;
                    $kardex->id_bodega         = $id_bodega;
                    $kardex->descripcion       = 'DESCUENTO DE NUEVO KARDEX'; //$cabecera_movimiento->obsevacion;
                    $kardex->id_producto       = $inventario->id_producto;
                    $kardex->tipo              = 'E';
                    $kardex->fecha             = date('Y-m-d H:i:s'); //$cabecera_movimiento->fecha;
                    $kardex->cantidad          = 1;
                    $kardex->estado                 = 1;
                    $kardex->id_empresa             = Session::get('id_empresa');
                    $kardex->ip_creacion            = $ip_cliente;
                    $kardex->ip_modificacion        = $ip_cliente;
                    $kardex->id_usuariocrea         = $idusuario;
                    $kardex->id_usuariomod          = $idusuario;
                    $kardex->serie                  = $serie;
                    $kardex->valor_unitario         = $primer_kardex->valor_unitario;
                    $kardex->total                  = $primer_kardex->valor_unitario;
                    $kardex->id_inv_det_movimientos = $detalle_mov->id;
                    $kardex->id_agenda              = $cabecera_movimiento->id_agenda;
                    $kardex->id_hc_procedimientos   = $cabecera_movimiento->id_hc_procedimientos;
                    //$kardex->referencia             = $detalle_mov->id;
                    $kardex->save();
                    $cantidad_serie -= 1;
                    $inventario->update(['cantidad_usos_kardex' => $cantidad_serie]);
                    $detalle_mov->update(['cantidad_usos_kardex' => 1]);
                    $detalle_planilla->update(['precio' => $primer_kardex->valor_unitario]);
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "DESCUENTO_NUEVO_KARDEX",
                        'dato_ant1'   => $serie,
                        'dato1'       => $id_bodega,
                        'dato_ant2'   => 'OK',
                        'dato2'       => '-1',
                        'dato_ant3'   => $inventario->cantidad_usos_kardex,
                        'dato3'       => $regresa,
                    ]);
                } else {
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "DESCUENTO_NUEVO_KARDEX",
                        'dato_ant1'   => $serie,
                        'dato1'       => $id_bodega,
                        'dato_ant2'   => 'CANTIDAD DE USOS YA ES MENOR A CERO',
                        'dato2'       => ' ',
                        'dato_ant3'   => $inventario->cantidad_usos_kardex,
                        'dato3'       => $regresa,
                    ]);
                }
            } else {
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "DESCUENTO_NUEVO_KARDEX",
                    'dato_ant1'   => $serie,
                    'dato1'       => $id_bodega,
                    'dato_ant2'   => 'PRODUCTO NO MARCADO COMO KARDEX POR USO',
                    'dato2'       => ' ',
                    'dato_ant3'   => $inventario->cantidad_usos_kardex,
                    'dato3'       => $regresa,
                ]);
            }
        } else {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "DESCUENTO_NUEVO_KARDEX",
                'dato_ant1'   => $serie,
                'dato1'       => $id_bodega,
                'dato_ant2'   => 'NO SE ENCONTRO EN INVENTARIO',
                'dato2'       => ' ',
                'dato_ant3'   => ' ',
                'dato3'       => $regresa,
            ]);
        }

        return $regresa;
    }

    public static function ingresoTraslado($id_cab_movimiento/* cab traslado */)
    {
        # movimiento origen
        //$cab_movimiento = InvCabMovimientos::find($id_documento_origen);
        # creo la cabecera del traslado #
        $cabcera       = InvCabMovimientos::find($id_cab_movimiento);
        $num_documento = $cabcera->numero_documento;
        $documento     = invDocumentosBodegas::where('abreviatura_documento', 'INT')->first();
        $secuencia     = InvDocumentosBodegas::getSecueciaTipoDocum($cabcera->id_bodega_destino, 'INT');
        $transaccion   = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
            ->where('id_bodega', $cabcera->id_bodega_destino)
            ->first();
        $cabcera->id_documento_bodega   = $documento->id;
        $cabcera->id_transaccion_bodega = $transaccion->id;
        $cabcera->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cabcera->observacion           = 'INGRESO POR TRASLADO';
        $cabcera->fecha                 = date('Y-m-d');
        $cabcera->id_docum_origen       = $id_cab_movimiento;
        // $cab_mov_inv                        = InvCabMovimientos::create($cabcera);

        ##      CABECERA
        $cab_mov_inv                        = new InvCabMovimientos;
        $cab_mov_inv->id_documento_bodega   = $documento->id;
        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
        $cab_mov_inv->id_bodega_origen      = $cabcera->id_bodega_origen;
        $cab_mov_inv->id_bodega_destino     = $cabcera->id_bodega_destino;
        $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cab_mov_inv->observacion           = $cabcera->observacion;
        $cab_mov_inv->fecha                 = date('Y-m-d');
        $cab_mov_inv->num_doc_ext           = $cabcera->num_doc_ext;
        $cab_mov_inv->num_doc_cont          = $cabcera->num_doc_cont;
        $cab_mov_inv->descuento             = $cabcera->descuento;
        $cab_mov_inv->subtotal              = $cabcera->subtotal;
        $cab_mov_inv->subtotal_0            = $cabcera->subtotal_0;
        $cab_mov_inv->iva                   = $cabcera->iva;
        $cab_mov_inv->total                 = $cabcera->total;
        $cab_mov_inv->id_docum_origen       = $cabcera->id;
        $cab_mov_inv->id_empresa            = Session::get('id_empresa');
        $cab_mov_inv->id_pedido             = $cabcera->id_pedido;
        $cab_mov_inv->ip_creacion           = $cabcera->ip_creacion;
        $cab_mov_inv->ip_modificacion       = $cabcera->ip_modificacion;
        $cab_mov_inv->id_usuariocrea        = $cabcera->id_usuariocrea;
        $cab_mov_inv->id_usuariomod         = $cabcera->id_usuariomod;
        $cab_mov_inv->save();
        ##      D E T A L L E S
        foreach ($cabcera->detalles as $detalle) {
            // if ($detalle->kardex == 0) {
            $inventario = InvInventario::getInventario($detalle->id_producto, $cabcera->id_bodega_destino);
            if (!isset($inventario->id)) {
                $inventario = InvInventario::setNeoInventario($detalle->id_producto, $cabcera->id_bodega_destino);
            }
            ##       creo los detalles del traslado
            $det_mov_inv                         = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto            = $detalle->id_producto;
            $det_mov_inv->serie                  = $detalle->serie;
            $det_mov_inv->lote                   = $detalle->lote;
            $det_mov_inv->fecha_vence            = $detalle->fecha_vence;
            $det_mov_inv->serie                  = $detalle->serie;
            $det_mov_inv->id_inv_inventario      = $inventario->id;
            $det_mov_inv->cantidad               = $detalle->cantidad;
            $det_mov_inv->cant_uso               = $detalle->cant_uso;
            $det_mov_inv->valor_unitario         = $detalle->valor_unitario;
            $det_mov_inv->subtotal               = $detalle->subtotal;
            $det_mov_inv->descuento              = $detalle->descuento;
            $det_mov_inv->iva                    = $detalle->iva;
            $det_mov_inv->total                  = $detalle->total;
            $det_mov_inv->motivo                 = 'INGRESO TRASLADO PEDIDO ' . $num_documento;
            $det_mov_inv->id_detalle_origen      = $detalle->id;
            $det_mov_inv->ip_creacion            = $detalle->ip_creacion;
            $det_mov_inv->ip_modificacion        = $detalle->ip_modificacion;
            $det_mov_inv->id_usuariocrea         = $detalle->id_usuariocrea;
            $det_mov_inv->id_usuariomod          = $detalle->id_usuariomod;
            $det_mov_inv->save();
            // }
        }
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        //InvCabMovimientos::registrarTrasladoPedido($id_documento_origen/* padre */, $id_cab_movimiento/* traslado */, $cab_mov_inv->id/* ingreso tralado */);

    }
}
