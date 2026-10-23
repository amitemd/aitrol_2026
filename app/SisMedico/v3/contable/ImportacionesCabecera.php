<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportacionesCabecera extends Model
{
    protected $table = 'ct_importaciones_cab';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';

    public static function saveFactura($r)
    {
        $id_usuario = Auth::user()->id;
        $id_empresa = $r->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $objeto_validar = new Utilidades();
        $id_estado = $r['id_estado'];
        DB::beginTransaction();
        try {
            $errores = "";
            $fechahoy = $r['fecha'];
            $nueva_fecha = null;
            $modfecha = null;
            $consulta_fecha = Termino::where('id', $r['termino'])->first();
            if ($consulta_fecha != null) {
                $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($fechahoy));
                $modfecha = date("Y-m-d", $nueva_fecha);
            } else {
                $errores .= " la fecha del termino no funciona ";
            }
            $subtotalf = $r['total'];
            $cod_sucurs = Sucursales::where('estado', '1')->where('id', $r['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = Caja::where('estado', '1')->where('id', $r['punto_emision'])->first();
            $c_caja = $cod_caj->codigo_caja;
            $sucursal = $c_sucursal;
            $punto_emision = $c_caja;
            $contador_ctv = Compras::where('id_empresa', $id_empresa)
                ->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)
                ->get()
                ->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_importaciones_compras
                $max_id = Compras::where('id_empresa', $id_empresa)
                    ->where('sucursal', $sucursal)
                    ->where('punto_emision', $punto_emision)
                    ->latest()
                    ->first();
                $max_id = intval($max_id->secuencia_f);
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado = "{$r['serie']}-{$r['secuencia_factura']}";
            $total_final = $objeto_validar->set_round($r['total']);
            $cab_asiento = [
                'sucursal'        => $c_sucursal,
                'punto_emision'   => $c_caja,
                'observacion'     => $r['observacion'],
                'fecha_asiento'   => $r['fecha'],
                'fact_numero'     => $r['secuencia_factura'],
                'valor'           => $total_final,
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'aparece_sri'     => $r['archivosri'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ];
            $id_asiento_cabecera = AsientosCabecera::insertGetId($cab_asiento);
            $proveedor = Proveedor::where('id', $r->proveedor)->first();
            $arr_cabecera = [
                'archivo_sri'         => $r->archivosri,
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'f_autorizacion'      => $r->f_autorizacion,
                'fecha'               => $r->fecha,
                'proveedor'           => $r->proveedor,
                'autorizacion'        => $r->autorizacion,
                'serie'               => $r['serie'],
                'secuencia_factura'   => $r->secuencia_factura,
                'tipo'                => 1, //factura gasto
                'estado'              => 1,
                'tipo_comprobante'    => $r->tipo_comprobante,
                'termino'             => $r->termino,
                'f_caducidad'         => $r->f_caducidad,
                'proveedor'           => $r->proveedor,
                'direccion_proveedor' => $proveedor->direccion,
                'credito_tributario'  => $r->credito_tributario,
                'tipo_comprobante'    => $r->tipo_comprobante,
                'observacion'         => $r->observacion,
                'subtotal_0'          => $r->subtotal_0,
                'subtotal_12'         => $r->subtotal_12,
                'subtotal'            => $subtotalf,
                'descuento'           => $r->descuento,
                'iva_total'           => $r->iva,
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'fecha_termino'       => $modfecha,
                'numero'              => $numeroconcadenado,
                'secuencia_f'         => $numero_factura,
                'valor_contable'      => $total_final,
                'total_final'         => $total_final,
                'id_empresa'          => $id_empresa,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_cabecera = ImportacionesCompras::insertGetId($arr_cabecera);
            $cab_compras = Compras::insertGetId($arr_cabecera);
            for ($i = 0; $i < count($r->producto); $i++) {
                $gasto = ImpGastos::find($r->producto[$i]);
                $arr_det = [
                    'id_ct_compras'        => $id_cabecera,
                    'id_gasto'             => $r->producto[$i],
                    'detalle'              => $r->detalle[$i],
                    'cantidad'             => $r->cantidad[$i],
                    'estado'               => 1,
                    'codigo'               => $gasto->codigo,
                    'nombre'               => $gasto->nombre,
                    'precio'               => $r->precio[$i],
                    'descuento_porcentaje' => $r->pdescuento[$i],
                    'descuento'            => $r->descuento[$i],
                    'iva'                  => $r->check_iva[$i],
                    'total'                => $r->total[$i],
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];
                echo '<pre>';
                print_r($arr_det);
                DB::rollBack();
                exit;
                Ct_Importaciones_Detalle_Compra::insertGetId($arr_det);

                $arr_detcompras = [
                    'id_ct_compras'        => $cab_compras,
                    'detalle'              => $r->descrip_prod[$i], //faltan codigo y nombre de productos 
                    'codigo'               => $gasto->codigo,
                    'nombre'               => $gasto->nombre,
                    'cantidad'             => $r->cantidad[$i],
                    'estado'               => 1,
                    'precio'               => $r->precio[$i],
                    'descuento_porcentaje' => $r->descpor[$i],
                    'descuento'            => $r->desc[$i],
                    'extendido'            => ($r->precio[$i] * $r->cantidad[$i]),
                    'iva'                  => $r->check_iva[$i],
                    'total'                => $r->precioneto[$i],
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];

                Ct_detalle_compra::insertGetid($arr_detcompras);
            }

            Ct_Importaciones_Gasto_Cab::create([
                'id_import_cabl'          => $r->id_cab_imp,
                'id_import_compra'        => $id_cabecera,
                'id_ct_compra'            => $cab_compras,
                'tipo'                    => 1, //factura
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
            //f15
            if ($r->id_estado != null) {
                $act_estado = Ct_Importaciones_Gasto_Cab::find($id_estado);
                $act_estado->tipo = 2;
                $act_estado->save();
            }

            ImportacionesController::recalcularImportaciones($r->id_cab_imp);
            $asientos     = GastosImportacionController::asiento_factura($r, $id_asiento_cabecera, $id_usuario, $ip_cliente, $fechahoy, $subtotalf, $r->total1, $r->base1);

            if ($asientos["respuesta" == "error"]) {
                DB::rollBack();
                return $asientos;
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_asiento' => $id_asiento_cabecera];
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error', "e" => $e->getLine()];
        }
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\v3\contable\ImportacionesGastosCab', 'id_import_cabl')->where('tipo', 3);
    }
    public static function getImportaciones($id = '')
    {
        if ($id == '') {
            return ImportacionesCabecera::join('proveedor as p', 'ct_importaciones_cab.id_proveedor', 'p.id')
                ->join('users as uc', 'ct_importaciones_cab.id_usuariocrea', '=', 'uc.id')
                ->leftJoin('users as um', 'ct_importaciones_cab.id_usuariomod', '=', 'um.id')
                ->where('ct_importaciones_cab.id_empresa', session('id_empresa'))
                ->get([
                    'ct_importaciones_cab.id',
                    'secuencia_factura',
                    'p.razonsocial',
                    'ct_importaciones_cab.observacion',
                    'ct_importaciones_cab.fecha',
                    'ct_importaciones_cab.secuencia_importacion',
                    'ct_importaciones_cab.serie',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_importaciones_cab.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                    'ct_importaciones_cab.inventario'
                ]);
        } else {
        }
    }
}
