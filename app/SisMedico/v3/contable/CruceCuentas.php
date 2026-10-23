<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\LogAsiento;
use Sis_medico\v3\contable\DetalleCruceCuentas;

class CruceCuentas extends Model
{
    private static $modelo = 'CruceCuentas';
    protected $table = 'ct_cruce_cuentas';
    protected $primaryKey  = 'id';

    public static function getCruceCuentas($id = '')
    {
        if ($id == '') {
            return CruceCuentas::leftjoin('users as uc', 'ct_cruce_cuentas.id_usuariocrea', 'uc.id')
                ->leftjoin('proveedor as p', 'ct_cruce_cuentas.id_proveedor', 'p.id')
                ->where('ct_cruce_cuentas.id_empresa', session('id_empresa'))->get([
                    'ct_cruce_cuentas.id',
                    'ct_cruce_cuentas.secuencia',
                    'ct_cruce_cuentas.id_asiento_cabecera',
                    'ct_cruce_cuentas.fecha',
                    'ct_cruce_cuentas.id_proveedor',
                    'p.nombrecomercial',
                    'ct_cruce_cuentas.detalle',
                    'ct_cruce_cuentas.total',
                    'ct_cruce_cuentas.estado',
                    DB::raw("CONCAT(SUBSTRING(uc.nombre1, 1, 1), '. ', uc.apellido1) as usuariocrea"),
                ]);
        } else {
            return CruceCuentas::leftjoin('users as uc', 'ct_cruce_cuentas.id_usuariocrea', 'uc.id')
                ->leftjoin('proveedor as p', 'ct_cruce_cuentas.id_proveedor', 'p.id')
                ->leftjoin('ct_compras as c', 'ct_cruce_cuentas.id_factura', 'c.id')
                ->where('ct_cruce_cuentas.id', $id)
                ->where('ct_cruce_cuentas.id_empresa', session('id_empresa'))->first([
                    'ct_cruce_cuentas.id',
                    'ct_cruce_cuentas.secuencia',
                    'ct_cruce_cuentas.id_asiento_cabecera',
                    'ct_cruce_cuentas.fecha',
                    'ct_cruce_cuentas.id_proveedor',
                    'p.nombrecomercial',
                    'ct_cruce_cuentas.detalle',
                    'ct_cruce_cuentas.total',
                    'ct_cruce_cuentas.estado',
                    'c.fecha as fecha_fact',
                    DB::raw("CONCAT('Fact# : ', c.numero, ' | ', p.nombrecomercial, ' | Id: ', c.id) as comprobante"),
                    DB::raw("CONCAT(p.id, ' | ', p.razonsocial) as proveedor"),
                ]);
        }
    }

    public static function buscarCompras($id = '')
    {
        if ($id == '') {
            return Compras::where('ct_compras.id_empresa', session('id_empresa'))
                ->where('ct_compras.estado', '<>', '0')
                ->where('ct_compras.valor_contable', '>', '0')
                ->get([
                    'ct_compras.id', 'ct_compras.observacion', 'ct_compras.numero'
                ]);
        } else {
            return Compras::where('ct_compras.id_empresa', session('id_empresa'))
                ->leftjoin('proveedor as p', 'ct_compras.proveedor', 'p.id')
                ->where('ct_compras.estado', '<>', '0')
                ->where('ct_compras.valor_contable', '>', '0')
                ->where('ct_compras.id', $id)
                ->first([
                    'ct_compras.*', 'p.id as id_proveedor', 'p.razonsocial'
                ]);
        }
    }

    public static function saveCruceCuentasProveedor($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');

            $numero_factura = CruceCuentas::generarNumeroCruce();
            $cabecera = [
                'observacion'                   => "Fact # " . $r->comprobante_compra . " " . $r->conceptop,
                'fecha_asiento'                 => $r->fechacc,
                'fact_numero'                   => $numero_factura,
                'valor'                         => $r->totalp,
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip,
                'ip_modificacion'               => $ip,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            //$id_asiento_cabecera = AsientosCabecera::insertGetId($cabecera);
            //$id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'cruce_cuenta', $cabecera,  $r);
            $id_asiento_cabecera = 226;

            $consulta_compra = Compras::where('id', $r->comprobante_compra)->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();

            if ($consulta_compra != '') {
                $valor = $consulta_compra->valor_contable - $r->totalp;
                $arrayCompra = [
                    'valor_contable'                => $valor,
                    'estado'                        => '2',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                Compras::where('id', $r->comprobante_compra)->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->update($arrayCompra);

                $input = [
                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'fecha'                         => $r->fechacc,
                    'detalle'                       => $r->conceptop,
                    'total'                         => $r->totalp,
                    'id_factura'                    => $r->comprobante_compra,
                    'id_proveedor'                  => $consulta_compra->proveedor,
                    'secuencia'                     => $numero_factura,
                    'estado'                        => '1',
                    'id_empresa'                    => $id_empresa,
                    'ip_creacion'                   => $ip,
                    'ip_modificacion'               => $ip,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];

                $id_cruce = CruceCuentas::insertGetId($input);

                for ($i = 0; $i < count($data["cuentap"]); $i++) {
                    $detalle_cruce = new DetalleCruceCuentas;
                    $detalle_cruce->codigo = $data['cuentap'][$i];
                    $detalle_cruce->id_comprobante = $id_cruce;
                    $detalle_cruce->secuencia_factura = $consulta_compra->numero;
                    $detalle_cruce->fecha = $r->fechacc;
                    $detalle_cruce->estado = '1';
                    $detalle_cruce->total_factura = '0';
                    $detalle_cruce->total = $data['valorp'][$i];
                    $detalle_cruce->observaciones = $data['detallep'][$i];
                    $detalle_cruce->id_usuariocrea = $idusuario;
                    $detalle_cruce->id_usuariomod =  $idusuario;
                    $detalle_cruce->ip_creacion = $ip;
                    $detalle_cruce->ip_modificacion = $ip;
                    $detalle_cruce->save();

                    /* $plan_cuentasx = PlanCuentas::where('id_plan', $data['cuenta'][$i])->where('id_empresa', $id_empresa)->first();
                        if($plan_cuentasx != ''){
                            $asiento_debe = new AsientosDetalles;
                            $asiento_debe->id_asiento_cabecera = $id_asiento_cabecera;
                            $asiento_debe->id_plan_cuenta = $data['cuenta'][$i];
                            $asiento_debe->descripcion = $plan_cuentasx->nombre;
                            $asiento_debe->fecha = $r->fecha;
                            $asiento_debe->debe = $data['valor'][$i];
                            $asiento_debe->haber = '0';
                            $asiento_debe->estado = '1';
                            $asiento_debe->id_usuariocrea = $idusuario;
                            $asiento_debe->id_usuariomod =  $idusuario;
                            $asiento_debe->ip_creacion = $ip;
                            $asiento_debe->ip_modificacion = $ip;
                            $asiento_debe->save();
                        }*/
                }
                /* $asiento_haber = new AsientosDetalles;
                    $asiento_haber->id_asiento_cabecera = $id_asiento_cabecera;
                    $asiento_haber->id_plan_cuenta = $data['cuenta'][$i];
                    $asiento_haber->descripcion = $plan_cuentasx->nombre;
                    $asiento_haber->fecha = $r->fecha;
                    $asiento_haber->debe = $data['valor'][$i];
                    $asiento_haber->haber = '0';
                    $asiento_haber->estado = '1';
                    $asiento_haber->id_usuariocrea = $idusuario;
                    $asiento_haber->id_usuariomod =  $idusuario;
                    $asiento_haber->ip_creacion = $ip;
                    $asiento_haber->ip_modificacion = $ip;
                    $asiento_haber->save();*/

                $r->c = 'Proveedores';
                $r->s = 'saveCruceCuentasProveedor';
                $r->d = $origin['d'];
                $r->m = CruceCuentas::$modelo;
                $r->o = 'Se agrego el nuevo cruce de cuentas proveedores con id.: ' . $id_cruce;
                $cont++;
            }
            //}
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Proveedores';
            $r->s = 'saveCruceCuentasProveedor';
            $r->d =  $origin['d'];
            $r->m = CruceCuentas::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'id_asiento' => $id_asiento_cabecera,
                'id' => $id_cruce,
                'numero' => $numero_factura,
                'tipo' => 'crucecuentasproveedores',
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro guardado correctamente',
            ];
            return $arrayRespuesta;
        }
    }

    private static function generarNumeroCruce()
    {
        $num = CruceCuentas::where('id_empresa', session('id_empresa'))->max('id');
        if ($num == '')
            $num = '0000000001';
        else {
            $num = $num + 1;
            $num = str_pad($num, 10, 0, STR_PAD_LEFT);
        }
        return $num;
    }

    public static function getDetalleCruceCuentas($id)
    {
        return DetalleCruceCuentas::join('plan_cuentas_empresa as pce', 'pce.id_plan', 'ct_detalle_cruce_cuentas.codigo')
            ->where('pce.id_empresa', session('id_empresa'))
            ->where('ct_detalle_cruce_cuentas.id_comprobante', $id)->get();
    }

    public static function anularCruceProv($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $origin = $r->input();
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $comp_ingreso = CruceCuentas::where('estado', '1')->where('id', $r->id)->first();
            if ($comp_ingreso != '') {
                $consulta = Compras::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->first();
                if (!is_null($consulta)) {
                    $valor = $consulta->valor_contable + $comp_ingreso->total;
                    $input_actualiza = [
                        'valor_contable'                => $valor,
                        'estado'                        => '2',
                        'ip_modificacion'               => $ip,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    Compras::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->update($input_actualiza);
                }
                $input = [
                    'estado'                        => '0',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                CruceCuentas::where('estado', '1')->where('id', $r->id)->update($input);

                //$id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'cruce_cuenta', $cabecera,  $r);
                //aqui va reverso de asientos
                //LogAsiento::anulacion("CL-CC", $id_asiento, $comp_ingreso->id_asiento_cabecera);

                $r->c = 'Clientes';
                $r->s = 'anularCruceProv';
                //$r->d = $origin['d'];
                $r->m = CruceCuentas::$modelo;
                $r->o = 'Se anulo el nuevo cruce de cuentas proveedor con id.: ' . $r->id;
                $cont++;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'anularCruceProv';
            //$r->d =  $origin['id'];
            $r->m = CruceCuentas::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }

        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro anulado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }

    public static function datosPdf($id)
    {
        $datos =  CruceCuentas::leftjoin('ct_detalle_cruce_cuentas as cdcc', 'cdcc.id_comprobante', 'ct_cruce_cuentas.id')
            ->where('ct_cruce_cuentas.id', $id)
            ->select('ct_cruce_cuentas.*', 'cdcc.*')
            ->first();

        DB::enableQueryLog();
        $detalles  = AsientosDetalles::where('id_asiento_cabecera', $datos->id_asiento_cabecera)
            ->join('plan_cuentas_empresa as pce', function ($q) {
                $q->on('pce.id_plan', 'ct_asientos_detalle.id_plan_cuenta')
                    ->where('pce.id_empresa', session('id_empresa'));
            })
            ->groupBy('id_plan_cuenta')
            ->select(DB::raw('id_plan_cuenta, ct_asientos_detalle.descripcion, SUM(debe) as debe, SUM(haber) as haber,pce.plan,pce.nombre'))
            ->get();

        $ccuentas          = CruceCuentas::where('id', $id)->first();
        $proveedor         = Proveedor::where('id', $ccuentas->id_proveedor)->first();
        $empresa           = Empresas::where('id', $ccuentas->id_empresa)->first();
        $detalle_crucecuentasprov = DetalleCruceCuentas::where('id_comprobante', $ccuentas->id)->get();
        $compras = Compras::where('id', $ccuentas->id_factura)->first();
        return compact("ccuentas", "proveedor", "empresa", "detalle_crucecuentasprov", "compras", "datos", "detalles");
    }

    public static function getFactCompra($term)
    {
        return Compras::selectRaw('ct_compras.id as id, CONCAT("Fact# : ", ct_compras.numero," | ",p.nombrecomercial, " | Id: ", ct_compras.id) as text')
            ->leftjoin('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->where('ct_compras.estado', '<>', '0')
            ->where('ct_compras.valor_contable', '>', '0')
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($term) {
                $q->orWhere('ct_compras.numero', 'like', '%' . $term . '%');
                $q->orWhere('ct_compras.observacion', 'like', '%' . $term . '%');
                $q->orWhere('p.id', 'like', '%' . $term . '%');
                $q->orWhere('p.nombrecomercial', 'like', '%' . $term . '%');
            })->get();
    }
}
