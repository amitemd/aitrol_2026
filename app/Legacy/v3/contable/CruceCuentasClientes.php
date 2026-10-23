<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\DetalleCruceCuentasClientes;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\LogAsiento;
use Sis_medico\v3\contable\LogAsiento as ContableLogAsiento;

class CruceCuentasClientes extends Model
{
    private static $modelo = 'CruceCuentasClientes';
    protected $table = 'ct_cruce_cuentas_clientes';
    protected $primaryKey  = 'id';

    public static function getCruceCuentas($id = '')
    {
        if ($id == '') {
            return CruceCuentasClientes::leftjoin('users as uc', 'ct_cruce_cuentas_clientes.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_clientes as c', 'ct_cruce_cuentas_clientes.id_cliente', 'c.identificacion')
                ->leftjoin('ct_ventas as cv', 'ct_cruce_cuentas_clientes.id_factura', 'cv.id')
                ->where('ct_cruce_cuentas_clientes.id_empresa', session('id_empresa'))->get([
                    'ct_cruce_cuentas_clientes.id',
                    'ct_cruce_cuentas_clientes.id_cliente',
                    'c.nombre',
                    'ct_cruce_cuentas_clientes.id_asiento_cabecera',
                    'ct_cruce_cuentas_clientes.detalle',
                    'ct_cruce_cuentas_clientes.total',
                    'ct_cruce_cuentas_clientes.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'cv.nro_comprobante'
                ]);
        } else {
            return CruceCuentasClientes::join('users as uc', 'ct_cruce_cuentas_clientes.id_usuariocrea', 'uc.id')
                ->join('ct_clientes as c', 'ct_cruce_cuentas_clientes.id_cliente', 'c.identificacion')
                ->where('ct_cruce_cuentas_clientes.id_empresa', session('id_empresa'))
                ->leftjoin('ct_ventas as cv', 'ct_cruce_cuentas_clientes.id_factura', 'cv.id')
                ->where('ct_cruce_cuentas_clientes.id', $id)
                ->first([
                    'ct_cruce_cuentas_clientes.id',
                    'ct_cruce_cuentas_clientes.secuencia',
                    'ct_cruce_cuentas_clientes.id_asiento_cabecera',
                    'ct_cruce_cuentas_clientes.fecha',
                    'ct_cruce_cuentas_clientes.detalle',
                    'ct_cruce_cuentas_clientes.id_factura',
                    'ct_cruce_cuentas_clientes.id_cliente',
                    'c.nombre',
                    'ct_cruce_cuentas_clientes.detalle',
                    'ct_cruce_cuentas_clientes.total',
                    'ct_cruce_cuentas_clientes.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'cv.nro_comprobante',
                    'c.nombre',
                    'ct_cruce_cuentas_clientes.total',
                    'cv.fecha as fecha_fact',
                    'cv.id as id_fact',
                    DB::raw("CONCAT(c.identificacion, ' | ', c.nombre) as cliente"),
                ]);
        }
    }

    public static function getDetalleCruceCuentas($id)
    {
        return DetalleCruceCuentasClientes::join('plan_cuentas_empresa as pce', 'pce.id_plan', 'ct_detalle_cruce_cuentas_clientes.codigo')
            ->where('pce.id_empresa', session('id_empresa'))
            ->where('ct_detalle_cruce_cuentas_clientes.id_comprobante', $id)->get();
    }

    public static function estadoCruceCuentas($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (CruceCuentasClientes::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro editado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado de Cruce de cuentas',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }

    public static function buscarVentas($id = '')
    {
        if ($id == '') {
            return Ventas::where('ct_ventas.id_empresa', session('id_empresa'))
                ->join('ct_clientes as c', 'c.identificacion', 'ct_ventas.id_cliente')
                ->where('ct_ventas.estado', '<>', '0')
                ->where('ct_ventas.valor_contable', '>', '0')
                ->get([
                    'ct_ventas.*',
                    'c.nombre'
                ]);
        } else {
            return Ventas::where('ct_ventas.id_empresa', session('id_empresa'))
                ->join('ct_clientes as c', 'c.identificacion', 'ct_ventas.id_cliente')
                ->where('ct_ventas.estado', '<>', '0')
                ->where('ct_ventas.valor_contable', '>', '0')
                ->where('ct_ventas.id', $id)
                ->first([
                    'ct_ventas.*',
                    'c.identificacion',
                    'c.nombre'
                ]);
        }
    }

    public static function saveCruceCuentas($r)
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
            $numero_factura = CruceCuentasClientes::generarNumeroCruce();
            $cabecera = [
                'sucursal'                      => $r->sucursal,
                'observacion'                   => "Fact # " . $r->comprobante . " " . $r->concepto,
                'fecha_asiento'                 => $r->fecha,
                'fact_numero'                   => $numero_factura,
                'valor'                         => $r->total,
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip,
                'ip_modificacion'               => $ip,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'cruce_cuenta', $cabecera,  $r);
            $consulta_ventas = Ventas::where('id', $r->comprobante)->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();

            if ($consulta_ventas != '') {
                $valor = $consulta_ventas->valor_contable - $r->total;

                $arrayCompra = [
                    'valor_contable'                => $valor,
                    'estado_pago'                   => ($valor >= 0) ? '2' : '3',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                Ventas::where('id', $r->comprobante)->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->update($arrayCompra);

                $input = [
                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'fecha'                         => $r->fecha,
                    'detalle'                       => $r->concepto,
                    'total'                         => $r->total,
                    'id_factura'                    => $r->comprobante,
                    'id_cliente'                    => $consulta_ventas->id_cliente,
                    'secuencia'                     => $numero_factura,
                    'estado'                        => '1',
                    'id_empresa'                    => $id_empresa,
                    'ip_creacion'                   => $ip,
                    'ip_modificacion'               => $ip,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $id_cruce = CruceCuentasClientes::insertGetId($input);
                for ($i = 0; $i < count($data["cuenta"]); $i++) {
                    $detalle_cruce = new DetalleCruceCuentasClientes;
                    $detalle_cruce->codigo = $data['cuenta'][$i];
                    $detalle_cruce->id_comprobante = $id_cruce;
                    $detalle_cruce->secuencia_factura = $consulta_ventas->numero;
                    $detalle_cruce->fecha = $r->fecha;
                    $detalle_cruce->estado = '1';
                    $detalle_cruce->total_factura = '0';
                    $detalle_cruce->total = $data['valor'][$i];
                    $detalle_cruce->observaciones = $data['detalle'][$i];
                    $detalle_cruce->id_usuariocrea = $idusuario;
                    $detalle_cruce->id_usuariomod =  $idusuario;
                    $detalle_cruce->ip_creacion = $ip;
                    $detalle_cruce->ip_modificacion = $ip;
                    $detalle_cruce->save();
                }
               
                $r->c = 'Clientes';
                $r->s = 'saveCruceCuentas';
                $r->d = $origin['d'];
                $r->m = CruceCuentasClientes::$modelo;
                $r->o = 'Se agrego el nuevo cruce de cuentas con id.: ' . $id_cruce;
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
            $r->c = 'Clientes';
            $r->s = 'saveCruceCuentas';
            $r->d =  $origin['d'];
            $r->m = CruceCuentasClientes::$modelo;
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
                'tipo' => 'crucecuentas',
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
        $num = CruceCuentasClientes::where('id_empresa', session('id_empresa'))->max('id');
        if ($num == '')
            $num = '0000000001';
        else {
            $num = $num + 1;
            $num = str_pad($num, 10, 0, STR_PAD_LEFT);
        }
        return $num;
    }

    public static function anularCruce($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $origin = $r->input();
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $comp_ingreso = CruceCuentasClientes::where('estado', '1')->where('id', $r->id)->first();
            if ($comp_ingreso != '') {
                $consulta_venta = Ventas::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->first();
                if (!is_null($consulta_venta)) {
                    $valor = $consulta_venta->valor_contable + $comp_ingreso->total;
                    $input_actualiza = [
                        'valor_contable'                => $valor,
                        'ip_modificacion'               => $ip,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    Ventas::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->update($input_actualiza);
                }
                $input = [
                    'estado'                        => '0',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                CruceCuentasClientes::where('estado', '1')->where('id', $r->id)->update($input); 
                $asiento = AsientosCabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = AsientosCabecera::insertGetId([
                    'observacion'     => strtoupper($r->text),
                    'fecha_asiento'   => date("Y-m-d"),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'sucursal'        => $asiento->sucursal,
                ]);
                ContableLogAsiento::anulacion("CL-CC", $asiento, $comp_ingreso->id_asiento_cabecera);
                foreach ($detalles as $value) {
                    AsientosDetalles::insert([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => date("Y-m-d"),
                        'ip_creacion'         => $ip,
                        'ip_modificacion'     => $ip,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
                $r->c = 'Clientes';
                $r->s = 'anularCruce';
                $r->m = CruceCuentasClientes::$modelo;
                $r->o = 'Se anulo el nuevo cruce de cuentas con id.: ' . $r->id;
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
            $r->s = 'anularCruce';
            //$r->d =  $origin['id'];
            $r->m = CruceCuentasClientes::$modelo;
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

    public static function getFactVenta($term)
    {
        return Ventas::selectRaw('ct_ventas.id as id, CONCAT("Fact# : ", ct_ventas.nro_comprobante," | ",c.nombre, " | Id: ", ct_ventas.id) as text')
            ->join('ct_clientes as c', 'c.identificacion', 'ct_ventas.id_cliente')
            ->where('ct_ventas.estado', '<>', '0')
            ->where('ct_ventas.valor_contable', '>', '0')
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($term) {
                $q->orWhere('ct_ventas.id', 'like', '%' . $term . '%');
                $q->orWhere('ct_ventas.nro_comprobante', 'like', '%' . $term . '%');
                $q->orWhere('c.nombre', 'like', '%' . $term . '%');
                $q->orWhere('c.identificacion', 'like', '%' . $term . '%');
            })->get();
    }
}
