<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardexNuevo;
use Sis_medico\Planilla_Detalle_Labs;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class KardexUsoLabs extends Model
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public static function descontar_kardex_nuevo($serie, $id_bodega, $id_inv_det_mov, $id_detalle_pla)
    {
        $regresa = 0;

       
            $ip_cliente             = $_SERVER["REMOTE_ADDR"];
            $idusuario              = isset(Auth::user()->id) ? Auth::user()->id : 'FACELECTRO';
            $detalle_mov = InvDetMovimientos::find($id_inv_det_mov);
            $detalle_planilla = Planilla_Detalle_Labs::find($id_detalle_pla);
            $cabecera_movimiento = $detalle_planilla->cabecera;
            /*$id_agenda              = $cabecera_movimiento->id_agenda;
                $id_hc_procedimientos   = $cabecera_movimiento->id_hc_procedimientos;*/
            $inventario = InvInventarioSerie::where('serie', $serie)->where('id_bodega', $id_bodega)->first();
            if (!is_null($inventario)) {
                if ($inventario->producto->kardex_uso == 1) {
                    if ($inventario->cantidad_usos_kardex > 0) {
                        $regresa = 1;
                        if ($inventario->cantidad_usos_kardex == 1) {
                            $regresa = 0;
                        }
                        $primer_kardex = InvKardexNuevo::where('serie', $serie)->where('id_bodega', $id_bodega)->where('tipo', 'T')->first(); //dd($serie,$bodega,$primer_kardex);
                        /*if(is_null($primer_kardex)){
                                Log_usuario::create([
                                    'id_usuario'  => $idusuario,
                                    'ip_usuario'  => $ip_cliente,
                                    'descripcion' => "NO_TIENE_KARDEX",
                                    'dato_ant1'   => $serie,
                                    'dato1'       => $bodega,
                                    'dato_ant2'   => ' ',
                                    'dato2'       => ' ',
                                    'dato_ant3'   => $inventario->cantidad_usos_kardex,
                                    'dato3'       => ' ',
                                ]);  
                                return "ok";
                            }*/
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
                        $kardex->id_empresa             = '0993075000001';
                        $kardex->ip_creacion            = $ip_cliente;
                        $kardex->ip_modificacion        = $ip_cliente;
                        $kardex->id_usuariocrea         = $idusuario;
                        $kardex->id_usuariomod          = $idusuario;
                        $kardex->serie                  = $serie;
                        $kardex->valor_unitario         = $primer_kardex->valor_unitario;
                        $kardex->total                  = $primer_kardex->valor_unitario;
                        $kardex->id_inv_det_movimientos = $detalle_mov->id;
                        // $kardex->id_agenda              = $cabecera_movimiento->id_agenda;
                        // $kardex->id_hc_procedimientos   = $cabecera_movimiento->id_hc_procedimientos;
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

    public static function documentoEgresoPaciente($inv_serie, $id_agenda = null, $id_hc_procedimientos = null, $cantidad, $cantidad_uso, $id_movimiento_paciente = "", $id_bodega_param = 2, $id_detalle_pla, $examen_orden, $detalle_unico = "")
    {
        try{
            $id_bodega = $id_bodega_param;
            $ip_cliente = 'FACELECTRO';
            $idusuario = 'FACELECTRO';
            $id_empresa = '0993075000001';
            $documento_bodega_abr = 'EPL';
            $cab_mov_inv = DB::table('inv_cab_movimientos as c')
                ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
                ->where('d.abreviatura_documento', $documento_bodega_abr)
                ->where('c.id_bodega_origen', $id_bodega)
                ->where('c.id_empresa', $id_empresa)
                ->where('c.estado', '!=', 0)
                ->where('c.data_humanlabs', $detalle_unico)
                ->select('c.*')
                ->first();
            if (!is_null($inv_serie->inventario)) {
                $valor_precio = $inv_serie->inventario->costo_promedio;
            } else {
                $movimiento = Movimiento::where('serie', $inv_serie->serie)->orderBy('created_at', 'Desc')->first();
                $valor_precio = $movimiento->precio;
            }
            if (!isset($cab_mov_inv->id)) {
                # creo la cabecera del traslado #
                $documento = InvDocumentosBodegas::where('abreviatura_documento', $documento_bodega_abr)->first();
                $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, $documento_bodega_abr);
                if ($secuencia != 0) {
                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                        ->where('id_bodega', $id_bodega)
                        ->first();
                    $cab_mov_inv                        = new InvCabMovimientos;
                    $cab_mov_inv->id_documento_bodega   = $documento->id;
                    $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                    $cab_mov_inv->id_bodega_origen      = $id_bodega;
                    $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                    $cab_mov_inv->observacion           = "EGRESO LAB PACIENTE: {$examen_orden['id_paciente']}, Orden: {$examen_orden['id']}";
                    $cab_mov_inv->fecha                 = date('Y-m-d');
                    $cab_mov_inv->id_empresa            = $id_empresa;
                    $cab_mov_inv->ip_creacion           = $ip_cliente;
                    $cab_mov_inv->ip_modificacion       = $ip_cliente;
                    $cab_mov_inv->id_usuariocrea        = $idusuario;
                    $cab_mov_inv->id_usuariomod         = $idusuario;
                    $cab_mov_inv->data_humanlabs        = $detalle_unico;
                    $cab_mov_inv->save();
                }
            }
            # I V A
            $iva = 0;
          
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
            // $det_mov_inv->id_procedimiento       = $id_hc_procedimientos;
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
            // $det_mov_inv->id_movimiento_paciente = $id_movimiento_paciente;
            $det_mov_inv->ip_creacion            = $ip_cliente;
            $det_mov_inv->ip_modificacion        = $ip_cliente;
            $det_mov_inv->id_usuariocrea         = $idusuario;
            $det_mov_inv->id_usuariomod          = $idusuario;
            $det_mov_inv->save();
    
            $serie = $inv_serie->serie;
            $id_inv_det_mov = $det_mov_inv->id;
            $regreso = KardexUsoLabs::descontar_kardex_nuevo($serie, $id_bodega, $id_inv_det_mov, $id_detalle_pla);

            InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
            if ($regreso ==  0 ) {
                $kardex = InvKardex::setKardex2($cab_mov_inv->id);
                if(!$kardex["status"]){
                    return $kardex;
                }
            }
            return ["status"=>true, "cab_mov_inv"=> $cab_mov_inv->id];
        }catch(\Exception $e){
            // return $e->getMessage();
            return ["status"=>false, "msj"=> "Error al registrar el kardex", "exp"=>$e->getMessage(), "mod"=> "KardexUsoLabs"];
        }
        
    }
}
