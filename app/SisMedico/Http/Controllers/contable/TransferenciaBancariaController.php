<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Sis_medico\Ct_Debito_Bancario;
use Session;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Transferencia_Bancaria;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\LogConfig;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Autorizaciones_Pendientes;

class TransferenciaBancariaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresas   = Empresa::find($id_empresa);
        // $principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);
        $principales = Ct_Transferencia_Bancaria::where('empresa', $id_empresa)->where('estado', '!=', null)->orderby('id', 'desc')->paginate(20);
        $cuentas     = Plan_Cuentas::join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->where('p.estado', '>', 0)->select('plan_cuentas.id as id', 'p.nombre as nombre')->get();
        return view('contable/transferencia_bancaria/index', ['registros' => $principales, 'cuentas' => $cuentas, 'empresa' => $empresas]);
    }
    public function actualizarRetencionBancaria(Request $r)
    {
        $arrayDatos = [
            'id_proveedor' => $r->proveedor,
            'autorizacion' => $r->autorizacion,
            'id_comision' => $r->id_comision,
            'valor_comision' => $r->valor_comision,
            'comision_tarjeta' => $r->comision_tarjeta,
            'base_iva' => $r->baseiva,
            'id_base_iva' => $r->id_base_iva,
            'iva_retenido' => $r->iva_retenido,
            'base_ret_fuente' => $r->base_ret_fuente,
            'id_base_ret_fuente' => $r->id_base_ret_fuente,
            'retencion_fuente' => $r->retencion_fuente,
            'iva_retenido' => $r->iva_retenido,
            'valores_adicionales' => $r->valores_adicionales,
        ];
        Ct_Transferencia_Bancaria::where('id', $r->id)->update($arrayDatos);
        return json_encode(['status' => true, 'mns' => 'ok']);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago    = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas        = Ct_divisas::where('estado', '1')->get();
        $banco          = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->where('id_empresa', $id_empresa)->get();
        $bancos         = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        // $bancos         = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $cuentas        = Plan_Cuentas::join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->where('p.estado', '>', 0)->select('plan_cuentas.id as id', 'p.nombre as nombre')->get();
        $retenciones    = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
        $retencionesFue = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        $proveedores = Proveedor::where('estado', 1)->get();
        return view('contable/transferencia_bancaria/create', ['min_date' => $min_date, 'divisas' => $divisas, 'retencionesR' => $retencionesFue, 'empresa' => $empresa, 'banco' => $banco, 'bancos' => $bancos, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas, 'retenciones' => $retenciones, 'sucursales' => $sucursales, 'proveedores' => $proveedores]);
    }

    public function buscar_cuenta_destino(Request $request)
    {
        //dd($request['search']);
        $id_empresa     = $request->session()->get('id_empresa');
        $cuentas = [];
        if ($request['search'] != null) {
            $cuentas     = Plan_Cuentas::join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
                ->where('p.id_empresa', $id_empresa)
                ->where('p.estado', '>', 0)
                ->where('plan_cuentas.nombre', 'like', '%' . $request['search'] . '%')
                ->select('p.nombre as text', 'plan_cuentas.id as id')->get();
        }
        return response()->json($cuentas);
    }

    public function cambiarformatofecha($fecha)
    {
        $fecha     = str_replace('/', '-', $fecha);
        $timestamp = \Carbon\Carbon::parse($fecha)->timestamp;
        $fecha     = date('Y-m-d', $timestamp);
        return $fecha;
    }

    public function store(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $num    = "1";
            $numero = $this->getNumero();
            foreach ($numero as $row) {
                $num = $row->numero + 1;
            }
            $id_empresa = $request->session()->get('id_empresa');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $sucursal = $request['sucursal'];
            $autoRete_ = '';
            $autoRete = Ct_Autorizaciones_Pendientes::find($request->autorizacion);
            if ($autoRete == '') {
                $autoRete_ = $request['autorizacion'];
            } else {
                $autoRete_ = $autoRete->autorizacion;
            }
            $input = [
                'fecha_asiento'       => $this->cambiarformatofecha($request['fecha_asiento']),
                'empresa'             => $id_empresa,
                'numero'              => $num,
                'tipo'                => $request['tipo'],
                'proyecto'            => $request['proyecto'],
                'concepto'            => $request['concepto'],
                'numcheque'           => $request['numcheque'],
                'fecha_cheque'        => $this->cambiarformatofecha($request['fecha_cheque']),
                'beneficiario'        => $request['beneficiario'],
                'no_increm_cheque'    => $request['no_increm_cheque'],
                'ruc'                 => $request['ruc'],
                'direccion'           => $request['direccion'],
                'glosa'               => $request['glosa'],
                'estado'              => 1,
                'id_cuenta_origen'    => $request['id_cuenta_origen'],
                'id_divisa_origen'    => $request['id_divisa_origen'],
                'id_cambio_origen'    => $request['id_cambio_origen'],
                'valor_origen'        => $request['valor_origen'],
                'id_cuenta_destino'   => $request['id_cuenta_destino'],
                'id_divisa_destino'   => $request['id_divisa_destino'],
                'id_cambio_destino'   => $request['id_cambio_destino'],
                'valor_destino'       => $request['valor_destino'],
                'id_comision'         => $request['id_comision'],
                'valor_comision'      => $request['valor_comision'],
                'comision_tarjeta'    => $request['comision_tarjeta'],
                'base_iva'            => $request['base_iva'],
                'id_base_iva'         => $request['id_base_iva'],
                'iva_retenido'        => $request['iva_retenido'],
                'base_ret_fuente'     => $request['base_ret_fuente'],
                'id_base_ret_fuente'  => $request['id_base_ret_fuente'],
                'retencion_fuente'    => $request['retencion_fuente'],
                'valores_adicionales' => $request['valores_adicionales'],
                'total'               => $request['total'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_proveedor'        => $request['id_proveedor'],
                'autorizacion'        => $autoRete_,
            ];
            $id_asiento = $this->asiento($input, $sucursal);
            if ($id_asiento["status"] == "error") {
                DB::rollback();
                return $id_asiento;
            }
            $input['id_asiento'] = $id_asiento['id_asiento'];
            // $input['id']         = Ct_Transferencia_Bancaria::insertGetId($input);
            $id_transferencia         = Ct_Transferencia_Bancaria::insertGetId($input);
            //$input['msg']        = "Transacción generada satisfactoriamente";
            DB::commit();
            return ['status' => 'success', "msj" => "Transacción generada satisfactoriamente", "id" => $id_asiento["id_asiento"], "id" => $id_transferencia];
            //return response()->json($input);
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msg" => "Error al guardar", "exp" => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function store_autorizacion(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $num    = "1";
            $numero = $this->getNumero();
            foreach ($numero as $row) {
                $num = $row->numero + 1;
            }

            $id_empresa = $request->session()->get('id_empresa');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $sucursal = $request['sucursal'];
            $input = [
                'fecha_asiento'       => $this->cambiarformatofecha($request['fecha_asiento']),
                'empresa'             => $id_empresa,
                'numero'              => $num,
                'tipo'                => $request['tipo'],
                'proyecto'            => $request['proyecto'],
                'concepto'            => $request['concepto'],
                'numcheque'           => $request['numcheque'],
                'fecha_cheque'        => $this->cambiarformatofecha($request['fecha_cheque']),
                'beneficiario'        => $request['beneficiario'],
                'no_increm_cheque'    => $request['no_increm_cheque'],
                'ruc'                 => $request['ruc'],
                'direccion'           => $request['direccion'],
                'glosa'               => $request['glosa'],
                'estado'              => 1,
                'id_cuenta_origen'    => $request['id_cuenta_origen'],
                'id_divisa_origen'    => $request['id_divisa_origen'],
                'id_cambio_origen'    => $request['id_cambio_origen'],
                'valor_origen'        => $request['valor_origen'],
                'id_cuenta_destino'   => $request['id_cuenta_destino'],
                'id_divisa_destino'   => $request['id_divisa_destino'],
                'id_cambio_destino'   => $request['id_cambio_destino'],
                'valor_destino'       => $request['valor_destino'],
                'id_comision'         => $request['id_comision'],
                'valor_comision'      => $request['valor_comision'],
                'comision_tarjeta'    => $request['comision_tarjeta'],
                'base_iva'            => $request['base_iva'],
                'id_base_iva'         => $request['id_base_iva'],
                'iva_retenido'        => $request['iva_retenido'],
                'base_ret_fuente'     => $request['base_ret_fuente'],
                'id_base_ret_fuente'  => $request['id_base_ret_fuente'],
                'retencion_fuente'    => $request['retencion_fuente'],
                'valores_adicionales' => $request['valores_adicionales'],
                'total'               => $request['total'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_proveedor'        => $request['id_proveedor'],
                'autorizacion'        => $request['autorizacion'],

            ];

            $id_asiento= $this->asiento($input, $sucursal);
            if ($id_asiento["status"] == "error") {
                DB::rollback();
                return $id_asiento;
            }
            $input['id_asiento'] = $id_asiento['id_asiento'];
            // $input['id']         = Ct_Transferencia_Bancaria::insertGetId($input);
            $id_transferencia         = Ct_Transferencia_Bancaria::insertGetId($input);
            //$input['msg']        = "Transacción generada satisfactoriamente";
            DB::commit();
            return ['status' => 'success', "msj" => "Transacción generada satisfactoriamente", "id" => $id_asiento["id_asiento"], "id" => $id_transferencia];
            //return response()->json($input);
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "error" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function getNumero()
    {
        $num = Ct_Transferencia_Bancaria::whereRaw('numero = (select ifnull(max(`numero`),0) from ct_transferencia_bancaria)')->get();
        return $num;
    }

    public function asiento($request, $sucursal)
    {
        try {
            $input_cabecera = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $request['fecha_asiento'],
                'id_empresa'      => $request['empresa'],
                'valor'           => $request['total'],
                'id_usuariocrea'  => $request['id_usuariocrea'],
                'id_usuariomod'   => $request['id_usuariomod'],
                'ip_creacion'     => $request['ip_creacion'],
                'ip_modificacion' => $request['ip_modificacion'],
                'sucursal'        => $sucursal,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //TODO: Asiento con el movimento del debe
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $request['id_cuenta_origen'],
                'descripcion'         => $request['glosa'],
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $request['total'],
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $request['id_usuariocrea'],
                'id_usuariomod'       => $request['id_usuariomod'],
                'ip_creacion'         => $request['ip_creacion'],
                'ip_modificacion'     => $request['ip_modificacion'],
            ];
            Ct_Asientos_Detalle::create($asiento_detalle);
            if ($request['comision_tarjeta'] != 0) {
                //TODO: Asiento con el movimento del debe + (Comision)
                $id_plan_confg = LogConfig::busqueda('5.2.03.03.03');
                $planCuentas = Plan_Cuentas::find($id_plan_confg);
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => "5.2.03.03.03", // cuenta de comison
                    'id_plan_cuenta'      => $id_plan_confg, // cuenta de comison
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['comision_tarjeta'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                Ct_Asientos_Detalle::create($asiento_detalle);
            }
            if ($request['retencion_fuente'] != 0) {
                //TODO: Asiento con el movimento del debe + (Retencion a la fuente)
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request['id_base_ret_fuente'], // cuenta de retencion a la fuente
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['retencion_fuente'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                Ct_Asientos_Detalle::create($asiento_detalle);
            }
            if ($request['iva_retenido'] != 0) {
                //TODO: Asiento con el movimento del debe + (Retencion a la fuente)
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request['id_base_iva'], // cuenta de retencion a la fuente
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['iva_retenido'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                Ct_Asientos_Detalle::create($asiento_detalle);
            }
            //TODO: Asiento con el movimento del haber
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $request['id_cuenta_destino'],
                'descripcion'         => $request['glosa'],
                'fecha'               => $request['fecha_asiento'],
                'haber'               => '0',
                'debe'                => $request['valor_destino'],
                'estado'              => '1',
                'id_usuariocrea'      => $request['id_usuariocrea'],
                'id_usuariomod'       => $request['id_usuariomod'],
                'ip_creacion'         => $request['ip_creacion'],
                'ip_modificacion'     => $request['ip_modificacion'],
            ];
            Ct_Asientos_Detalle::create($asiento_detalle);
            return ["status" => "success", "msj" => "Guardado Correctamente", "id_asiento" => $id_asiento_cabecera];
            // return $id_asiento_cabecera;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => "Error al crear los asientos", "exp" => $e->getMessage(), "linea" => $e->getLine()];
        }
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $constraints = [
            'id_asiento'        => $request['buscar_asiento'],
            'fecha_asiento'     => $request['fecha_asiento'],
            'concepto'          => $request['concepto'],
            'numcheque'         => $request['numcheque'],
            'fecha_cheque'      => $request['fecha_cheque'],
            'id_cuenta_destino' => $request['id_cuenta_destino'],
            'id'                => $request['numero'],
        ];
        $cuentas    = Plan_Cuentas::all();
        $id_empresa = $request->session()->get('id_empresa');
        $registros  = $this->doSearchingQuery($constraints, $id_empresa);
        return view('contable/transferencia_bancaria/index', ['request' => $request, 'cuentas' => $cuentas, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $empresa)
    {

        $query  = Ct_Transferencia_Bancaria::query();
        $fields = array_keys($constraints);

        $index = 0;
        //dd($constraints);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('empresa', $empresa)->orderby('id', 'desc')->paginate(20);
    }

    public function anular(Request $request)
    {
        if ($this->rol()) {

            return response()->view('errors.404');
        }
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        $id = $request['id'];
        $observacion = $request['observacion'];
        $fechahoy   = Date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
        $estado_transferencia = Ct_Transferencia_Bancaria::where('id', $id)->where('estado', '<>', 0)->first();
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($estado_transferencia->fecha_asiento));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        if (!empty($estado_transferencia)) {
            $act_estado = [
                'estado'          => '0',
                'ip_modificacion' => $ip_cliente,
            ];
            $registro = Ct_Transferencia_Bancaria::findorfail($id);
            Ct_Transferencia_Bancaria::where('id', $id)->update($act_estado);
            $this->anular_asiento($registro->id_asiento, $observacion);
        }
        DB::commit();
        return ['status' => 'success', 'msj' => 'Anulado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'error' => $e->getLine()];
        }
    }

    public function anular_asiento($id, $observacion)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        // TODO: CABECERA DEL ASIENTO CONTABLE
        $cabasiento           = Ct_Asientos_Cabecera::find($id);

        //dd($cabasiento);
        $estado_transferencia = Ct_Asientos_Cabecera::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($estado_transferencia)) {
            $input_cabecera = [
                //'observacion'     => $cabasiento->observacion,
                'observacion'     => $observacion,
                'fecha_asiento'   => $cabasiento->fecha_asiento,
                'id_empresa'      => $cabasiento->id_empresa,
                'valor'           => $cabasiento->valor,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'sucursal'        => $cabasiento->sucursal,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            // dd($id_asiento_cabecera);
            // TODO: DETALLE DEL ASIENTO CONTABLE
            $detasiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id)->orderBy('id', 'desc')->get();
            foreach ($detasiento as $row) {
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $row->id_plan_cuenta,
                    'descripcion'         => $row->descripcion,
                    'fecha'               => $cabasiento->fecha_asiento,
                    'debe'                => $row->haber,
                    'haber'               => $row->debe,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                Ct_Asientos_Detalle::create($asiento_detalle);
            }
        }
        // Log_Contable::create([
        //     'tipo'           => 'TB',
        //     'valor_ant'      => $estado_transferencia->valor,
        //     'valor'          => $estado_transferencia->valor,
        //     'id_usuariocrea' => $idusuario,
        //     'id_usuariomod'  => $idusuario,
        //     'observacion'    => $estado_transferencia->concepto,
        //     'id_ant'         => $estado_transferencia->id,
        //     'id_referencia'  => $id_asiento_cabecera,
        // ]);
        LogAsiento::anulacion('TB', $id_asiento_cabecera, $id);
    }

    public function show($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro    = Ct_Transferencia_Bancaria::findorfail($id);
        $id_empresa  = Session::get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas     = Ct_divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->where('id_empresa', $id_empresa)->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $cuentas     = Plan_Cuentas::all();
        $retenciones = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
        $retencionesFue = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();
        $proveedores = Proveedor::where('estado', 1)->get();
        return view('contable/transferencia_bancaria/show', [
            'divisas' => $divisas, 'empresa'    => $empresa, 'banco'       => $banco,
            'bancos'  => $bancos, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas, 'registro' => $registro,
            'retenciones' => $retenciones, 'retencionesR' => $retencionesFue, 'proveedores' => $proveedores
        ]);
    }
    public function imprimir($id)
    {
        $registro        = Ct_Transferencia_Bancaria::findorfail($id);
        $detalle         = Ct_Asientos_Cabecera::where('id', $registro->id_asiento)->first();
        $detalle_asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $detalle->id)->get();
        //dd($detalle_asiento);

        $vistaurl = "contable.transferencia_bancaria.pdf";
        $view     = \View::make($vistaurl, compact('registro', 'detalle', 'detalle_asiento'))->render();
        //dd($registro);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Trasnferencia Bancaria-' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));

    }

    public function devuelvefloat(Request $request)
    {
        $cantidad  = $request['cantidad'];
        $decimales = $request['decimales'];
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }
        // $valor          = round($cantidad, $decimales);
        $data['valor'] = number_format($cantidad, $decimales, '.', '');
        return response()->json($data);
    }

    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consulta       = null;
        $fecha_asiento2 = $request['fecha_asiento'];
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $constraints    = [
            'id_asiento'    => $request['buscar_asiento2'],
            'fecha_asiento' => $request['fecha_asiento2'],
            'concepto'      => $request['concepto2'],
            'id'            => $request['numero2'],
        ];
        $consulta = Ct_Transferencia_Bancaria::query();
        $fields   = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        $consulta = $consulta->get();
        Excel::create('Ct_Transferencia_Bancaria-' . $fecha_asiento2 . '-al-' . $fecha_asiento2, function ($excel) use ($empresa, $consulta) {
            $excel->sheet('Ct_Transferencia_Bancaria', function ($sheet) use ($empresa, $consulta) {
                $sheet->mergeCells('A1:F1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $sheet->mergeCells('A2:A2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Asiento');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Estado');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = 3;

                foreach ($consulta as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_asiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d-m-Y', strtotime($value->fecha_asiento)));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->concepto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor_destino);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (($value->estado) != 0) {
                            $cell->setValue('ACTIVO');
                        } else {
                            $cell->setValue('ANULADO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        // $this->setSangria($cont, $cell);
                    });

                    $i++;
                }
                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function guardar_autorizacion(Request $request)
    {
        $proveedores = Proveedor::where('estado', 1)->get();
        $retenciones    = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
        $retencionesFue = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();
        return view('contable/transferencia_bancaria/guardar_autorizaciones', ['proveedores' => $proveedores, 'retencionesR' => $retencionesFue, 'retenciones' => $retenciones]);
    }

    public function save_autorizacion(Request $request)
    {
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            Ct_Autorizaciones_Pendientes::create([
                'id_proveedor' => $request['proveedor'],
                'autorizacion' => $request['autorizacion'],
                'base_iva' => $request['base_autorizacion'],
                'porcentaje_iva' => $request['iva_porcentaje'],
                'base_retencion' => $request['base_porcentaje'],
                'porcentaje_fuente' => $request['porcentaje_base'],
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            return ['status' => true, 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public function traer_autorizaciones(Request $request)
    {
        $autorizaciones = Ct_Autorizaciones_Pendientes::where("id_proveedor", $request->id_proveedor)->get();
        return ['autorizaciones' => $autorizaciones];
    }

    public function traer_datos_generales(Request $request)
    {
        $autorizacion = Ct_Autorizaciones_Pendientes::find($request->id);
        return ['dato' => $autorizacion];
    }
}
