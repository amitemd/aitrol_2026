<?php

namespace Sis_medico\Http\Controllers\Inventario;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;

class InvProcesosController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function carga_inicial()
    {
        try {
            $ip_cliente  = $_SERVER["REMOTE_ADDR"];
            $idusuario   = Auth::user()->id;
            $cab_mov_inv = null;
            $id_pedido   = null;
            $bodegas     = array(1 => 'COMPRAS', 2 => 'PENTAX');
            echo "::: INICIO CARGA :::<br>";

            foreach ($bodegas as $key => $value) {
                echo "LUGAR : $value <br>";
                $datos = DB::table('inv_carga_inventario')
                    ->where('lugar', trim($value))
                    ->whereNull('carga')
                    ->get();
                $bodega = Bodega::find($key);
                $msjok  = "";
                $msjerr = "";
                $i      = 0;
                foreach ($datos as $row) {
                    $row->serie = str_replace('-', '', $row->serie);
                    //  crear la cabecera    //
                    $cab_mov_inv = DB::table('inv_cab_movimientos as c')
                        ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
                        ->where('d.abreviatura_documento', 'INI')
                        ->where('c.id_bodega_origen', $key)
                        ->where('c.estado', '!=', 0)
                    // ->where('c.created_at','>=',date('Y-m-d'))
                        ->select('c.*')
                        ->first();
                    $pedido = Pedido::where('id_proveedor', '0992704152001')
                    // ->where('fecha', date('Y-m-d'))
                        ->where('factura', 'INI-0000' . $key)
                        ->where('pedido', 'INI-0000' . $key)
                        ->first();
                    // dd($pedido);

                    if (is_null($pedido)) {
                        $input = [
                            'id_proveedor'    => '0992704152001',
                            'pedido'          => 'INI-0000' . $key,
                            'tipo'            => 2,
                            'factura'         => 'INI-0000' . $key,
                            'fecha'           => date('Y-m-d'),
                            'id_bodega'       => $key,
                            'id_empresa'      => '0992704152001',
                            'observaciones'   => 'CARGA INICIAL',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        $id_pedido = Pedido::insertGetId($input);
                        $pedido    = Pedido::find($id_pedido);
                    }
                    if (!isset($cab_mov_inv->id)) {
                        ##    PEDIDO
                        $documento   = InvDocumentosBodegas::where('abreviatura_documento', 'INI')->first();
                        $secuencia   = InvDocumentosBodegas::getSecueciaTipoDocum($key, 'INI');
                        $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                            ->where('id_bodega', $key)
                            ->first();
                        ##      C A B E C E R A
                        $cab_mov_inv                        = new InvCabMovimientos;
                        $cab_mov_inv->id_documento_bodega   = $documento->id;
                        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                        $cab_mov_inv->id_bodega_origen      = $key;
                        $cab_mov_inv->numero_documento      = str_pad($secuencia, 10, "0", STR_PAD_LEFT);
                        $cab_mov_inv->observacion           = 'CARGA DE DATOS ' . date('d/m/Y') . " BODEGA: " . $bodega->nombre;
                        $cab_mov_inv->fecha                 = date('Y-m-d');
                        $cab_mov_inv->id_empresa            = '0992704152001';
                        $cab_mov_inv->id_pedido             = $pedido->id;
                        $cab_mov_inv->ip_creacion           = $ip_cliente;
                        $cab_mov_inv->ip_modificacion       = $ip_cliente;
                        $cab_mov_inv->id_usuariocrea        = $idusuario;
                        $cab_mov_inv->id_usuariomod         = $idusuario;
                        $cab_mov_inv->save();

                    }

                    if (trim($row->tipo) == 'CONSIGNA') {
                        $tipo = 'C';
                    } else {
                        $tipo = 'F';
                    }
                    $carga    = 'C';
                    $producto = Producto::where(trim('codigo'), trim($row->codigo))->first();
                    if (!isset($producto->id)) {
                        $movimiento = Movimiento::where('serie', $row->serie)->first();
                        if (isset($movimiento->id_producto)) {
                            $producto = Producto::find($movimiento->id_producto);
                            $carga    = 'F';
                        }
                    }
                    $cant_uso = 0;
                    $iva      = 0;
                    if (isset($producto->id)) {

                        // if ($value=='PENTAX') {dd($producto);}
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($row->cantidad * $row->precio) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }
                        $inventario = InvInventario::getInventario($producto->id, $key, $tipo);
                        if ($inventario == '[]') {
                            $inventario = InvInventario::setNeoInventario($producto->id, $key, $tipo, 0, 0);
                        }
                        if ($row->serie != "") {
                            // if ($row->fecha_exp==null||$row->fecha_exp=="") {
                            //     $row->fecha_exp = '2023-12-31';
                            // }
                            if ($row->fecha_exp == null || $row->fecha_exp == "" || $row->fecha_exp < date('Y-m-d')) {
                                $row->fecha_exp = date("Y-m-d", strtotime(date('Y-m-d') . "+ 1 year"));
                            }
                            if ($row->cantidad == null) {$row->cantidad = 1;}
                            if ($row->lote == null) {$row->lote = rand(100, 999);}
                            $det_mov_inv                         = new InvDetMovimientos;
                            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                            $det_mov_inv->id_producto            = $producto->id;
                            $det_mov_inv->id_inv_inventario      = $inventario->id;
                            $det_mov_inv->cantidad               = $row->cantidad;
                            $det_mov_inv->cant_uso               = $row->cantidad * $cant_uso;
                            $det_mov_inv->serie                  = $row->serie;
                            $det_mov_inv->lote                   = $row->lote;
                            $det_mov_inv->fecha_vence            = date('Y-m-d', strtotime($row->fecha_exp));
                            $det_mov_inv->valor_unitario         = $row->precio;
                            $det_mov_inv->subtotal               = $row->cantidad * $row->precio;
                            $det_mov_inv->descuento              = 0;
                            $det_mov_inv->iva                    = $iva;
                            $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                            $det_mov_inv->motivo                 = "CARGA DE DATOS " . date('d/m/Y') . " || BODEGA: " . $bodega->nombre . " || PEDIDO: " . $row->pedido;
                            $det_mov_inv->ip_creacion            = $ip_cliente;
                            $det_mov_inv->ip_modificacion        = $ip_cliente;
                            $det_mov_inv->id_usuariocrea         = $idusuario;
                            $det_mov_inv->id_usuariomod          = $idusuario;
                            $det_mov_inv->save();
                            // if ($id_pedido==null) {$id_pedido=1428;}

                            $input2 = [
                                'id_producto'       => $producto->id,
                                'cantidad'          => $row->cantidad,
                                'id_encargado'      => $idusuario,
                                'serie'             => $row->serie,
                                'id_bodega'         => $key,
                                'tipo'              => '1',
                                'fecha_vencimiento' => date('Y-m-d', strtotime($row->fecha_exp)),
                                'lote'              => $row->lote,
                                'usos'              => $row->cantidad * $cant_uso,
                                'descuentop'        => 0,
                                'descuento'         => 0,
                                'precio'            => $row->precio,
                                'id_pedido'         => $pedido->id,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariocrea'    => $idusuario,
                                'id_usuariomod'     => $idusuario,
                            ];
                            $id_movimiento = DB::table('movimiento')->insertGetId($input2);
                            echo "<span style='color:blue'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span>  <br>";
                            DB::table('inv_carga_inventario')
                                ->where('id', $row->id)
                                ->update(['carga' => $carga]);
                        }
                    } else {
                        DB::table('inv_carga_inventario')
                            ->where('id', $row->id)
                            ->update(['carga' => 'X']);
                        echo "<span style='color:red'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span> <br>";
                    }
                    $i++;
                }

                if (!is_null($cab_mov_inv)) {
                    $kardex = InvKardex::setKardex($cab_mov_inv->id);
                }

            }
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            echo "<br><br><span style='color:red'>ERROR AL GUARDAR ROLLBACK</span> <br>";
        }

        DB::commit();
        echo "<span style='color:blue'>::: FIN :::</span> ";

    }
    # elimina los q se crearon mal
    public function reproceso_inventario_serie()
    {
        echo "INICIO <br>";
        $sql = "select id_producto, id_bodega, serie, tipo, count(id)
                from inv_inventario_serie
                group by id_producto, id_bodega, serie, tipo
                having count(id) >1 ";
        $productos = DB::select(DB::raw($sql));
        foreach ($productos as $value) {
            echo "$value->id_producto, $value->id_bodega, $value->serie <br>";
            $series = InvInventarioSerie::where('serie', $value->serie)
                ->where('id_bodega', $value->id_bodega)
                ->get();
            $i = 0;
            foreach ($series as $row) {
                if ($i > 0) {
                    $serie = InvInventarioSerie::find($row->id);
                    if (isset($serie->id)) {
                        $serie->delete();
                        echo "delete serie: $serie->id, $serie->existencia <br>";
                    }
                }
                $i++;
            }
        }
        echo "FIN <br>";
    }
    # se eliminan los estado 0
    public function reproceso_inventario_serie_eliminados()
    {
        echo "INICIO <br>";
        $sql = "select id, serie, id_producto, id_bodega
                from inv_inventario_serie
                where estado = 0";
        $productos = DB::select(DB::raw($sql));
        foreach ($productos as $value) {
            echo "$value->id_producto, $value->id_bodega, $value->serie <br>";
            $serie = InvInventarioSerie::find($value->id);
            if (isset($serie->id)) {
                $serie->delete();
                echo "delete serie: $serie->id, $serie->existencia <br>";
            }

        }
        echo "FIN <br>";
    }

    public function reproceso_inventario_serie_bodegas_eliminados()
    {
        echo "INICIO INVENTARIO <br>";
        $sql = "select id, id_producto, id_bodega
                from inv_inventario
                where id_bodega not in (1,2) ";
        $productos = DB::select(DB::raw($sql));
        foreach ($productos as $value) {
            echo "$value->id_producto, $value->id_bodega, $value->id <br>";
            $inv = InvInventario::find($value->id);
            if (isset($inv->id)) {
                $inv->id_bodega = 2;
                $inv->estado    = 0;
                $inv->save();
                echo "delete id: $inv->id, $inv->id_bodega <br>";
            }

        }
        echo "FIN INVENTARIO <br>";

        echo "INICIO <br>";
        $sql = "select id, serie, id_producto, id_bodega
                from inv_inventario_serie
                where id_bodega not in (1,2) ";
        $productos = DB::select(DB::raw($sql));
        // dd($productos);
        foreach ($productos as $value) {
            echo "$value->id_producto, $value->id_bodega, $value->serie <br>";
            $serie = InvInventarioSerie::find($value->id);
            if (isset($serie->id)) {
                $serie->id_bodega = 2;
                $serie->estado    = 0;
                $serie->save();
                echo "delete serie: $serie->id, $serie->existencia <br>";
            }

        }

        echo "FIN <br>";
    }

    public function trasladoMasivo()
    {
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            # leer bodega 1
            $bodega1 = InvInventarioSerie::where('id_bodega', 1)
                ->where('estado', 1)
            // ->where('existencia','<','comprometido')
                ->get();
            # verificar si debe estar en bodega 1
            echo "::: INICIO CARGA :::<br>";
            $i = 0;
            foreach ($bodega1 as $row) {
                // echo "<span style='color:red'>SERIE: $row->serie || </span> ";

                $carga = DB::table('inv_carga_inventario')
                    ->where('serie', $row->serie)
                    ->first();
                if ($carga != null and $carga->lugar == 'PENTAX') {
                    # creo la cabecera del movimiento
                    $cab_mov_inv = InvCabMovimientos::where('observacion', 'TRASLADO 24112021 MOVIMIENTO A BODEGA PENTAX')->first();

                    if (!isset($cab_mov_inv->id)) {
                        $documento = invDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
                        $secuencia = InvDocumentosBodegas::getSecueciaTipo($row->id_bodega, 'T');
                        if ($secuencia != 0) {
                            $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                                ->where('id_bodega', $row->id_bodega)
                                ->first();
                            $cab_mov_inv                        = new InvCabMovimientos;
                            $cab_mov_inv->id_documento_bodega   = $documento->id;
                            $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                            $cab_mov_inv->id_bodega_origen      = $row->id_bodega;
                            $cab_mov_inv->id_bodega_destino     = 2;
                            $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                            $cab_mov_inv->observacion           = 'TRASLADO 24112021 MOVIMIENTO A BODEGA PENTAX';
                            $cab_mov_inv->fecha                 = date('Y-m-d');
                            $cab_mov_inv->id_empresa            = '0992704152001';
                            $cab_mov_inv->ip_creacion           = $ip_cliente;
                            $cab_mov_inv->ip_modificacion       = $ip_cliente;
                            $cab_mov_inv->id_usuariocrea        = $idusuario;
                            $cab_mov_inv->id_usuariomod         = $idusuario;
                            $cab_mov_inv->save();
                        }
                    }

                    if (trim($carga->tipo) == 'CONSIGNA') {
                        $tipo = 'C';
                    } else {
                        $tipo = 'F';
                    }
                    $producto = Producto::find($row->id_producto);

                    if (isset($producto->id)) {
                        $cant_uso = 0;
                        $iva      = 0;
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($row->cantidad * $row->precio) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }
                        $inventario = InvInventario::getInventario($producto->id, 2, $tipo);
                        if ($inventario == '[]') {
                            $inventario = InvInventario::setNeoInventario($producto->id, 2, $tipo, 0, 0);
                        }

                        if ($row->fecha_exp == null || $row->fecha_exp == "") {
                            $row->fecha_exp = '2023-12-31';
                        }
                        if ($row->cantidad == null) {
                            $row->cantidad = 1;
                        }
                        if ($row->lote == null) {
                            $row->lote = rand(100, 999);
                        }

                        $det_mov_inv                         = new InvDetMovimientos;
                        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                        $det_mov_inv->id_producto            = $producto->id;
                        $det_mov_inv->id_inv_inventario      = $inventario->id;
                        $det_mov_inv->cantidad               = $row->cantidad;
                        $det_mov_inv->cant_uso               = $row->cantidad * $cant_uso;
                        $det_mov_inv->serie                  = $row->serie;
                        $det_mov_inv->lote                   = $row->lote;
                        $det_mov_inv->fecha_vence            = $row->fecha_exp;
                        $det_mov_inv->valor_unitario         = $row->precio;
                        $det_mov_inv->subtotal               = $row->cantidad * $row->precio;
                        $det_mov_inv->descuento              = 0;
                        $det_mov_inv->iva                    = $iva;
                        $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                        $det_mov_inv->motivo                 = 'TRASLADO 24112021 MOVIMIENTO A BODEGA PENTAX';
                        $det_mov_inv->ip_creacion            = $ip_cliente;
                        $det_mov_inv->ip_modificacion        = $ip_cliente;
                        $det_mov_inv->id_usuariocrea         = $idusuario;
                        $det_mov_inv->id_usuariomod          = $idusuario;
                        $det_mov_inv->save();
                        echo " $i <span style='color:blue'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span>  <br>";
                        $i++;
                    }
                }
            }
            # realizar traslado
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            echo "<br><br><span style='color:red'>ERROR AL GUARDAR ROLLBACK</span> <br>";
        }

        DB::commit();
        echo "<span style='color:blue'>::: FIN :::</span> ";
    }

    public function reprocesaKardex()
    {
        $cab_mov_inv = DB::table('inv_cab_movimientos as c')
            ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
            ->where('d.abreviatura_documento', 'INI')
            ->where('c.estado', '!=', 0)
            ->where('c.created_at', '>=', date('Y-m-d'))
            ->get();
        foreach ($cab_mov_inv->detalles as $det) {

        }
    }

    public function reprocesaRoxicaina()
    {
        echo " REPROCESO ROXICAINA <br> ";
        $movimientos = DB::table('movimiento_paciente as mp')
            ->join('movimiento as m', 'mp.id_movimiento', '=', 'm.id')
            ->where('mp.created_at', '>=', '2020-11-20 00:00')
            ->where('mp.created_at', '<=', '2020-11-20 23:59')
            ->whereIn('m.id_producto', [129, 197])
            ->select('mp.*')
        // ->select('mp.id_hc_procedimientos')
            ->get();
        foreach ($movimientos as $mov) {
            echo "mp id: $mov->id || id_hc_procedimiento: $mov->id_hc_procedimientos <br>";
            # se crea el registro en movimiento paciente
            /*$mov_pac                        = new Movimiento_Paciente;
            $mov_pac->id_movimiento         = 452541;
            $mov_pac->id_hc_procedimientos  = $mov->id_hc_procedimientos;
            $mov_pac->id_usuariocrea        = '0924383631';
            $mov_pac->id_usuariomod         = '0924383631';
            $mov_pac->ip_creacion           = '192.168.75.164';
            $mov_pac->ip_modificacion       = '192.168.75.164';
            $mov_pac->save();*/

            // $mov_pac                        = Movimiento_Paciente::find($mov->id);
            // $mov_pac->id_movimiento         = 452541;
            // $mov_pac->save();
            // echo " || ok  <br> ";
            ///public_html/sis_medico/app/Http/Controllers/Inventario

        }
        echo " FIN REPROCESO <br> ";
    }

    public function trasladoMasivoBodega1()
    {
        // PARA SOLUCIONAR EL TEMA DE LAS 2 BODEGAS
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_bod_origen  = 3;
        $id_bod_destino = 1;
        echo "::: INICIO TRASLADO :::<br>";
        $i = 0;
        try {
            # TRAER EL INVENTARIO DE BODEGA PENTAX
            $inv_serie = InvInventarioSerie::where('id_bodega', $id_bod_origen)
                ->where('estado', 1)
                ->get();
            if (isset($inv_serie[0]->id)) {
                # VERIFICO SI NO EXISTE LA CABECERA #
                $cab_mov_inv = InvCabMovimientos::where('observacion', 'TRASLADO ' . date('YmdHi') . ' MOVIMIENTO A BODEGA PRINCIPAL')->first();

                if (!isset($cab_mov_inv->id)) {
                    $documento = invDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
                    $secuencia = InvDocumentosBodegas::getSecueciaTipo($id_bod_origen, 'T');
                    if ($secuencia != 0) {
                        $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                            ->where('id_bodega', $id_bod_origen)
                            ->first();
                        $cab_mov_inv                        = new InvCabMovimientos;
                        $cab_mov_inv->id_documento_bodega   = $documento->id;
                        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                        $cab_mov_inv->id_bodega_origen      = $id_bod_origen;
                        $cab_mov_inv->id_bodega_destino     = $id_bod_destino;
                        $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                        $cab_mov_inv->observacion           = 'TRASLADO ' . date('YmdHi') . ' MOVIMIENTO A BODEGA PRINCIPAL';
                        $cab_mov_inv->fecha                 = date('Y-m-d');
                        $cab_mov_inv->id_empresa            = '0992704152001';
                        $cab_mov_inv->ip_creacion           = $ip_cliente;
                        $cab_mov_inv->ip_modificacion       = $ip_cliente;
                        $cab_mov_inv->id_usuariocrea        = $idusuario;
                        $cab_mov_inv->id_usuariomod         = $idusuario;
                        $cab_mov_inv->save();
                    }
                }

                foreach ($inv_serie as $row) {
                    $producto = Producto::find($row->id_producto);
                    if (isset($producto->id)) {
                        $cant_uso = 0;
                        $iva      = 0;
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($row->cantidad * $row->precio) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }

                        $det_mov_inv                         = new InvDetMovimientos;
                        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                        $det_mov_inv->id_producto            = $producto->id;
                        $det_mov_inv->id_inv_inventario      = $row->id_inv_inventario;
                        $det_mov_inv->cantidad               = $row->existencia;
                        $det_mov_inv->cant_uso               = $row->existencia_uso;
                        $det_mov_inv->serie                  = $row->serie;
                        $det_mov_inv->lote                   = $row->lote;
                        $det_mov_inv->fecha_vence            = $row->fecha_vence;
                        $det_mov_inv->valor_unitario         = $row->invcosto->costo;
                        $det_mov_inv->subtotal               = $row->existencia * $row->invcosto->costo;
                        $det_mov_inv->descuento              = 0;
                        $det_mov_inv->iva                    = $iva;
                        $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                        $det_mov_inv->motivo                 = 'TRASLADO ' . date('YmdHi') . ' MOVIMIENTO A BODEGA PENTAX';
                        $det_mov_inv->ip_creacion            = $ip_cliente;
                        $det_mov_inv->ip_modificacion        = $ip_cliente;
                        $det_mov_inv->id_usuariocrea         = $idusuario;
                        $det_mov_inv->id_usuariomod          = $idusuario;

                        $det_mov_inv->save();
                        echo " $i <span style='color:blue'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span>  <br>";
                        $i++;

                    }
                }
                if (isset($cab_mov_inv->id)) {
                    $kardex = InvKardex::setKardex($cab_mov_inv->id);
                    InvCabMovimientos::ingresoTraslado($cab_mov_inv->id);
                }

            }

            # REALIZAR EL TRASLADO
            echo " $i <span style='color:blue'>INICIO DEL INGRESO DEL TRASLADO</span>  <br>";

            # REALIZAR EL INGRESO DE TRASLADO

        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            echo "<br><br><span style='color:red'>ERROR AL GUARDAR ROLLBACK</span> <br>";
        }

        DB::commit();
        echo "<span style='color:blue'>::: FIN :::</span> ";
    }

    public static function actualizaPrecio($data)
    {
        # REPROCESO DE PRECIO DE KARDEX Y MOVIMIENTOS #
        if (isset($data->producto->id)) {
            $kardex = InvKardex::where('id_producto', $data->producto->id)->where('estado', 1)->get();
            foreach ($kardex as $row) {
                $mov                 = InvKardex::find($row->id);
                $mov->valor_unitario = $data->precio;
                $mov->total          = ($mov->cantidad * $data->precio) + $mov->iva;
                $mov->save();
            }
            $int_costo = InvCosto::where('id_producto', $data->producto->id)
                ->where('estado', 1)
                ->first();
            if (isset($int_costo->id)) {
                $int_costo->costo_promedio = $data->precio;
                $int_costo->costo_anterior = $data->precio;
                $int_costo->save();
            } else {
                $int_costo                 = new InvCosto;
                $int_costo->id_producto    = $data->producto->id;
                $int_costo->costo_promedio = $data->precio;
                $int_costo->costo_anterior = $data->precio;
                $int_costo->id_empresa     = '0992704152001';
                $int_costo->estado         = 1;
                $int_costo->save();
            }
        }
    }

    public function reingreso($id_cab_movimientos)
    {
        // MOVIMIENTO EN KARDEX //
        $kardex = InvKardex::setKardex($id_cab_movimientos);
        echo "Api::ReIngreso::OK";

    }

}

/*
0968868588
0990086929
 */
