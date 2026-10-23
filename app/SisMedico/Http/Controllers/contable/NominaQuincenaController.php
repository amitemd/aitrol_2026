<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Nomina;
use Sis_medico\Nota_Debito;
use Sis_medico\Nota_Debito_Detalle;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;

use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Valida_Anticipo;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\Ct_Globales;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\LogAsiento;

class NominaQuincenaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index_quincena(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $anio = date('Y');$mes = date('m');$mes = $mes - 1;

        $cab_anticipo = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)->where('estado', '1')->where('anio', $anio)->orderby('mes', 'desc')->get();


        return view('contable.nuevo_quincena.index_quincena', ['cab_anticipo' => $cab_anticipo, 'empresa' => $empresa, 'anio' => $anio, 'mes' => $mes]);
    }

    public function busca_quincena(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $anio = $request['anio'];
        $mes  = $request->mes;
            //dd($request->all());

        $cab_anticipo = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)->where('estado', '1')->orderby('mes', 'desc');
        if (!is_null($anio)) {
            $cab_anticipo = $cab_anticipo->where('anio', $anio);
        }
        if(!is_null($mes) && $mes > 0){
            $xmes = $mes;
            $cab_anticipo = $cab_anticipo->where('mes',$xmes);
        }

        $cab_anticipo = $cab_anticipo->get();

        return view('contable.nuevo_quincena.index_quincena', ['cab_anticipo' => $cab_anticipo, 'empresa' => $empresa, 'anio' => $anio, 'mes' => $mes ]);
    }

    public function _crea_anticipo(Request $request)
    {

        $fecha_actual = date('Y-m-d');
        $anio_actual  = date('Y');
        $mes_actual   = date('m');

        //dd($mes_actual);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        //Obtenemos los empleados ingresados por empresa para calculo de Anticipo
        // $empl_nomina = Ct_Nomina::where('estado', '1')
        //     ->where('id_empresa', $id_empresa)
        //     ->orderby('nombres', 'asc')->get();
        //->get();

        $empl_nomina = Ct_Nomina::where('ct_nomina.estado', '1')
            ->where('id_empresa', $id_empresa)
            ->join('users as u', 'u.id', 'ct_nomina.id_user')
            ->select('u.*', 'ct_nomina.*')
            ->orderby('u.apellido1', 'asc')->get();

        //Obtenemos el Numero de Empleados por empresa
        $cont_empl = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')
            ->get()->count();

        //dd($empl_nomina);

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $valida_anticipo = 0;
        if (!is_null($inf_val_anticip)) {
            $valida_anticipo = 1;
        }

        if (!is_null($anticipo_valida)) {
            $valida_anticipo = 1;
        }
        return view('contable.nuevo_quincena.create', ['empresa' => $empresa, 'empl_nomina' => $empl_nomina, 'cont_empl' => $cont_empl, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'fecha_actual' => $fecha_actual, 'mes_actual' => $mes_actual, 'anio_actual' => $anio_actual, 'valida_anticipo' => $valida_anticipo]);
    }

    public function crea_anticipo(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        //dd($request->all());

        try {
            $anio = $request->anio;
            $mes  = $request->mes;

            $valida = Ct_Valida_Anticipo::where('anio',$anio)->where('mes',$mes)->where('id_empresa',$empresa->id)->where('estado','1')->first();
            //dd($valida);

            if(is_null($valida)){
                $arr_cab = [
                    'id_empresa'        => $id_empresa,
                    'anio'              => $anio,
                    'mes'               => $mes,
                    'id_usuariocrea'    => $id_usuario,
                    'id_usuariomod'     => $id_usuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $anticipo_valida = Ct_Valida_Anticipo::insertGetId($arr_cab);

                $nomina = Ct_Nomina::where('id_empresa', $id_empresa)->where('estado', '1')->get();

                foreach ($nomina as $val) {
                    $arr_det = [
                        'id_valida'         => $anticipo_valida,
                        'id_user'           => $val->id_user,
                        'id_empresa'        => $id_empresa,
                        'anio'              => $anio,
                        'mes'               => $mes,
                        'fecha_creacion'    => date('Y-m-d'),
                        'sueldo'            => $val->sueldo_neto,
                        'valor_anticipo'    => $val->val_anticip_quince,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,

                    ];

                    $valor_anticipo = Ct_Rh_Valor_Anticipos::create($arr_det);
                }



                DB::commit();
                return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito', 'id_valida' => $anticipo_valida];
            }else{

                $nomina = Ct_Nomina::where('id_empresa', $id_empresa)->where('estado', '1')->get();

                foreach ($nomina as $val) {
                    $detalle = Ct_Rh_Valor_Anticipos::where('id_user', $val->id_user)->where('id_empresa',$id_empresa)->where('id_valida',$valida->id)->first();
                    if(is_null($detalle)){
                        $arr_det = [
                            'id_valida'         => $valida->id,
                            'id_user'           => $val->id_user,
                            'id_empresa'        => $id_empresa,
                            'anio'              => $anio,
                            'mes'               => $mes,
                            'fecha_creacion'    => date('Y-m-d'),
                            'sueldo'            => $val->sueldo_neto,
                            'valor_anticipo'    => $val->val_anticip_quince,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,

                        ];

                        $valor_anticipo = Ct_Rh_Valor_Anticipos::create($arr_det);
                    }
                        
                }    
                return ['respuesta' => 'error', 'msj' => 'Ya existe Anticipo para el periodo solicitado', 'titulos' => 'Error'];
            }    
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function edit_anticipo(Request $request, $id_valida)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $anticipo_valida = Ct_Valida_Anticipo::find($id_valida);

        $valor_anticipos = Ct_Rh_Valor_Anticipos::where('ct_rh_valor_anticipos.estado', '1')
            ->where('ct_rh_valor_anticipos.id_empresa', $id_empresa)
            ->where('id_valida', $id_valida)
            ->join('ct_nomina as n', 'n.id_user', 'ct_rh_valor_anticipos.id_user')
            ->join('users as u', 'u.id', 'ct_rh_valor_anticipos.id_user')
            ->where('n.estado', '1')
            ->where('n.id_empresa', $id_empresa)
            ->select('ct_rh_valor_anticipos.*', 'n.fecha_ingreso', 'n.area', 'n.cargo', 'n.sueldo_neto', 'u.apellido1', 'u.apellido2', 'u.nombre1')
            ->orderby('u.apellido1', 'asc')
            ->get();

        return view('contable.nuevo_quincena.edit', ['id_valida' => $id_valida, 'valor_anticipos' => $valor_anticipos, 'anticipo_valida' => $anticipo_valida, 'empresa' => $empresa, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'bancos' => $bancos]);
    }

    public function tabla_detalle(Request $request, $id_valida)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $anticipo_valida = Ct_Valida_Anticipo::find($id_valida);

        $valor_anticipos = Ct_Rh_Valor_Anticipos::where('ct_rh_valor_anticipos.estado', '1')
            ->where('ct_rh_valor_anticipos.id_empresa', $id_empresa)
            ->where('id_valida', $id_valida)
            //->join('ct_nomina as n', 'n.id_user', 'ct_rh_valor_anticipos.id_user')
            ->join('users as u', 'u.id', 'ct_rh_valor_anticipos.id_user')
           // ->where('n.estado', '1')
            //->where('n.id_empresa', $id_empresa)
            ->select('ct_rh_valor_anticipos.*', 'u.apellido1', 'u.apellido2', 'u.nombre1')
            ->orderby('u.apellido1', 'asc')
            ->get(); 

        if(Auth::user()->id == '0951561075'){
            //dd($valor_anticipos);
        }

        return view('contable.nuevo_quincena.tabla_detalle', ['valor_anticipos' => $valor_anticipos, 'anticipo_valida' => $anticipo_valida, 'empresa' => $empresa]);
    }

    public function actualiza_anticipo($id_anticipo, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request['val_anticipo'.$id_anticipo]);
        try {
            $anticipo = Ct_Rh_Valor_Anticipos::find($id_anticipo);

            $arr_det = [
                'valor_anticipo'    => $request['val_anticipo' . $id_anticipo],
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $anticipo->update($arr_det);

            $nomina = Ct_Nomina::where('id_user', $anticipo->id_user)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

            $arr_nomina = [
                'val_anticip_quince' => $request['val_anticipo' . $id_anticipo],
                'id_usuariomod'      => $id_usuario,
                'ip_modificacion'    => $ip_cliente,
            ];
            $nomina->update($arr_nomina);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito', 'id_valida' => $request->id_valida];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function guarda_asiento_anticipo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);
        $roles = $request->roles;
        if(count($roles) < 1){
            return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN ROL PARA GENERAR', 'titulos' => 'Error'];
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa',$id_empresa)->where('cuenta_mayor',$request->cuenta_saliente)->where('estado',1)->first();
        if(is_null($caja_banco)){
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        DB::beginTransaction();
        try {
           
            $valida_anticipo = Ct_Valida_Anticipo::find($request->id_valida);
            $valor_anticipos = Ct_Rh_Valor_Anticipos::where('id_valida', $request->id_valida)->where('id_empresa', $id_empresa)->get();
            //dd($valor_anticipos);
            if ($valida_anticipo->asiento == null) {

                /*$sum_anticipo = 0;
                foreach ($valor_anticipos as $value) {
                    $sum_anticipo += $value->valor_anticipo;

                    $arr_valor = [
                        'id_tipo_pago'          => $request->tipo_pago,
                        'numero_cuenta'         => $request->numero_cuenta,
                        'banco'                 => $request->banco,
                        'cuenta_saliente'       => $request->cuenta_saliente,
                        'num_cheque'            => $request->numero_cheque,
                        'fecha_cheque'          => $request->fecha_cheque,
                        'id_usuariomod'         => $id_usuario,
                        'ip_modificacion'       => $ip_cliente,
                    ];

                    $value->update($arr_valor);
                }*/

                $sum_anticipo = 0;
                foreach($roles as $rol){

                    $valor_anticipo = Ct_Rh_Valor_Anticipos::find($rol);
                    if($valor_anticipo->id_asiento_cabecera == null){
                        $sum_anticipo   += $valor_anticipo->valor_anticipo;
                    }    
                }


                $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                $ms = intval($valida_anticipo->mes) - 1;
                $concepto  = 'ANTICIPOS EMPLEADOS 1ERA QUINCENA:' . ' ' . 'Año Cobro Anticipo' . ':' . $valida_anticipo->anio . ' ' . 'Mes Cobro Anticipo' . ':' . $meses[$ms] . ' ' . 'Por la Cantidad de' . ':' . $sum_anticipo;
                $concepto2 = 'ANT. 1ERA15NA Año: ' . $valida_anticipo->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_anticipo;
                $input_cabecera = [
                    'observacion'     => $concepto,
                    'fecha_asiento'   => $request->fecha_creacion,
                    'id_empresa'      => $id_empresa,
                    'valor'           => $sum_anticipo,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                
                foreach($roles as $rol){

                    $valor_anticipo = Ct_Rh_Valor_Anticipos::find($rol);
                    if($valor_anticipo->id_asiento_cabecera == null){

                        $arr_valor = [
                            'id_tipo_pago'          => $request->tipo_pago,
                            'numero_cuenta'         => $request->numero_cuenta,
                            'banco'                 => $request->banco,
                            'cuenta_saliente'       => $request->cuenta_saliente,
                            'num_cheque'            => $request->numero_cheque,
                            'fecha_cheque'          => $request->fecha_cheque,
                            'id_usuariomod'         => $id_usuario,
                            'ip_modificacion'       => $ip_cliente,
                            'id_asiento_cabecera'   => $id_asiento_cabecera,
                        ];

                        $valor_anticipo->update($arr_valor);
                    }    

                }

                /*$arr_valida = [
                    'asiento'           => $id_asiento_cabecera,
                    'id_usuariomod'     => $id_usuario,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $valida_anticipo->update($arr_valida);*/

                if ($sum_anticipo > 0) {

                   // $plan_cuentas = Plan_Cuentas::where('id', $request->cuenta_saliente)->first(); //cambiar a plan cuentas empresa

                    $plan_cuentas = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                    $plan_cuentas = Plan_Cuentas::find(is_null($plan_cuentas->id_plan) ? $plan_cuentas->plan : $plan_cuentas->id_plan);

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $request->cuenta_saliente,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $request->fecha_creacion,
                        'debe'                => '0',
                        'haber'               => $sum_anticipo,
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);

                    $globales = Ct_Globales::where('id_modulo', 19)->where('id_empresa', $id_empresa)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $globales->debe,
                        'descripcion'         => $globales->debec->nombre,
                        'fecha'               => $request->fecha_creacion,
                        'debe'                => $sum_anticipo,
                        'haber'               => '0',
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);

                    if($ct_tipo_pago->tipo == 'ACREDITACION'){ //DEBITO

                        $nota_debito         = [
                            'concepto'        => $concepto,
                            'fecha'           => $request->fecha_creacion,
                            'valor'           => $sum_anticipo,
                            'empresa'         => $id_empresa,
                            'tipo'            => "BAN-ND",
                            'id_asiento'      => $id_asiento_cabecera,
                            'id_banco'        => $caja_banco->id,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $id_usuario,
                            'id_usuariomod'   => $id_usuario,
                            'modulo'          => 'ANTICIPO DE QUINCENA',  
                        ];
                        //$id_nota = 0;
                        $id_nota = Nota_Debito::insertGetId($nota_debito);

                        $nota_deb_detalle = [
                            'id_nota_debito'  => $id_nota,
                            'codigo'          => $globales->debe,
                            'cuenta'          => $globales->debec->nombre,
                            'debe'            => $sum_anticipo,
                            'haber'           => number_format(0, 2),
                            'valor_base'      => $sum_anticipo, //number_format($valor['debe'], 2),
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $id_usuario,
                            'id_usuariomod'   => $id_usuario,
                        ];
                        Nota_Debito_Detalle::create($nota_deb_detalle);

                    }else{ //COMPROBANTE DE EGRESO
                        $numero_factura = LogAsiento::getSecuencia(2);

                        $input_comprobante = [
                            'descripcion'     => $concepto,
                            'estado'          => '1',
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_secuencia'    => 'null',
                            'nota'            => $concepto,
                            'fecha_comprobante' => $request->fecha_creacion,
                            'beneficiario'    => $concepto2,
                            'check'           => 0,
                            'girado'          => $concepto2,
                            'id_caja_banco'   => $caja_banco->id,
                            'nro_cheque'      => $request->numero_cheque,
                            'valor'           => $sum_anticipo,
                            'fecha_cheque'    => $request->fecha_cheque,
                            'secuencia'       => $numero_factura,
                            'id_empresa'      => $id_empresa,
                            'id_usuariocrea'  => $id_usuario,
                            'id_usuariomod'   => $id_usuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'modulo'          => 'ANTICIPO DE QUINCENA',
                        ];
                        $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                        Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $globales->debe,
                            'cuenta'                         => $globales->debec->nombre,
                            'descripcion'                    => $concepto,
                            'debe'                           => $sum_anticipo,
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $id_usuario,
                            'id_usuariomod'                  => $id_usuario,
                        ]);
                    }
                }
            }else{
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Ya tiene asiento creado...', 'titulos' => 'Error'];
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
}
