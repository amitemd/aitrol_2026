<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Session;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Deposito_Bancario;
use Sis_medico\Ct_Detalle_Deposito_Bancario;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\LogAnularDeposito;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Detalle_Pago_Ingreso_Varios;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_Cierre_Cabecera;

class DepositoBancarioController extends Controller
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
        // $principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $cuentas     = Plan_Cuentas::all();
        $principales = Ct_Deposito_Bancario::orderby('id', 'desc')->where('empresa', $id_empresa)->paginate(25);
        return view('contable/deposito_bancario/index', ['registros' => $principales, 'cuentas' => $cuentas, 'empresa' => $empresa]);
    }

    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $cuentas     = plan_cuentas::all();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/deposito_bancario/create', ['min_date' => $min_date, 'divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'bancos' => $bancos, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas]);
    }

    public function buscarformapago(Request $request)
    {
        $id_auth = Auth::user()->id;
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $todo = $request->id_forma_pago;
        $letra = "";
        for ($i = 0; $i < count($todo); $i++) {
            if ($todo[$i] == "T") {
                $letra = "T";
            }
        }
        if ($letra == "T") {
            $detalle     = Ct_Detalle_Pago_Ingreso::where('id_tipo', '!=', '0')->where('estado_deposito', '<>', '1');
            //$det_vario   = Ct_Detalle_Pago_Ingreso_Varios::where('id_tipo', '!=' , '0' )->where('estado_deposito','!=','1');
        } else {
            $detalle     = Ct_Detalle_Pago_Ingreso::whereIn('id_tipo', $request['id_forma_pago'])->where('estado_deposito', '<>', '1');
            //det_vario   = Ct_Detalle_Pago_Ingreso_Varios::whereIn('id_tipo', $request['id_forma_pago'])->where('estado_deposito','!=','1');
        }
        if (!empty($fecha_desde) || !empty($fecha_hasta)) {
            if (!empty($fecha_desde)) {
                $detalle = $detalle->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                //$det_vario = $det_vario->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $detalle = $detalle->where('fecha', '<=', $fecha_hasta);
                //$det_vario = $det_vario->whereBetween('fecha','<=', $fecha_hasta);
            }
        }
        //$detalle = $detalle->select('new', 'id_tipo', 'id', 'fecha', 'numero', 'id_tipo_tarjeta', 'id_banco', 'cuenta', 'girador', 'total');
        //$det_vario = $det_vario->select('new', 'id_tipo', 'id', 'fecha', 'numero', 'id_tipo_tarjeta', 'id_banco', 'cuenta', 'girador', 'total');
        //$detalle = $detalle->union($det_vario)->orderBy('fecha', 'desc')->get();
        $detalle = $detalle->orderBy('fecha', 'desc')->get();
        //dd($detalle);
        $html = "";
        $i = 0;

        foreach ($detalle as $value) {
            // dd($value);
            if ($value != null) {
                if ($value->new == 0) {
                    if ($value->cabecera_ingreso != null) {
                        if ($value->cabecera_ingreso->estado == 1 && $value->cabecera_ingreso->id_empresa == $id_empresa) {
                            if ($value->tipo_pago != null) {
                                $html .= "<tr>";
                                $html .= "<td>";
                                $html .= "<input type='hidden' name='tipo_c[]' value='CI'>";
                                $html .= "<input type='hidden' id='{$value->id}' name='id_comp_n[]' >";
                                $html .= "<input  type='checkbox' id='id_comp" . $i . "' class='form-check-input relactivo' name='id_comp[]' onchange='calcular_todo(); verificarChecks({$value->id}, {$value->cabecera_ingreso->id},id_comp{$i});' value='$value->id'> <input type='hidden' name='veractivo[]' class='veractivo' value='0'> <input class='totax' type='hidden' name='totales' value='$value->total'> <input type='hidden'class='tipox' name='tipx[]' value='" . $value->tipo_pago->nombre . "'>";
                                $html .= "</td>";
                                $html .= "<td>" . $value->cabecera_ingreso->id . "</td>";
                                $html .= "<td>" . $value->tipo_c . "</td>";
                                $html .= "<td>" . $value->tipo_pago->nombre . "</td>";
                                $html .= "<td>" . date('d/m/Y', strtotime($value->fecha)) . "</td>";
                                //$html .= "<td>" . $value->cabecera_ingreso->secuencia . "</td>";
                                //$html .= "<td>" . $value->detalle_ingreso . "</td>";
                                $html .= "<td>";
                                if (isset($value->cabecera_ingreso->detalle)) {
                                    foreach ($value->cabecera_ingreso->detalle as $det) {
                                        if (isset($det->ventas) > 0) {
                                            $html .= "<small> {$det->ventas->nro_comprobante} </small>";
                                        }
                                    }
                                }
                                $html .= "</td>";
                                $html .= "<td>" . $value->numero . "</td>";
                                if ($request->id_forma_pago == 999 || $request->id_forma_pago == "-1") {

                                    $traer = Ct_Tipo_Tarjeta::where('id', $value->id_tipo_tarjeta)->first();
                                    if (($traer) != null) {
                                        $html .= "<td>" . $traer->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                } else {
                                    if (isset($value->banco)) {
                                        $html .= "<td>" . $value->banco->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                }
                                $html .= "<td>" . $value->cuenta . "</td>";
                                $html .= "<td>" . $value->girador . "</td>";
                                $html .= "<td>$</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "</tr>";
                            }
                        }
                    }
                } else {
                    if ($value->cabecera_ingresov != null) {
                        if ($value->cabecera_ingresov->estado == 1 && $value->cabecera_ingresov->id_empresa == $id_empresa) {
                            if ($value->tipo_pago != null) {
                                $html .= "<tr>";
                                $html .= "<td>";
                                $html .= "<input type='hidden' name='tipo_c[]' value='CIV'>";
                                $html .= "<input type='hidden' name='id_comp_v[]'  value='{$value->cabecera_ingresov->id}'>";
                                $html .= "<input type='checkbox' id='id_comp" . $i . "' class='form-check-input relactivo' name='id_comp[]' onchange='calcular_todo();' value='$value->id'> <input type='hidden' name='veractivo[]' class='veractivo' value='0'> <input class='totax' type='hidden' name='totales' value='$value->total'> <input type='hidden'class='tipox' name='tipx[]' value='" . $value->tipo_pago->nombre . "'>";
                                $html .= "</td>";
                                $html .= "<td>" . $value->cabecera_ingresov->id . "</td>";
                                $html .= "<td>" . $value->tipo_c . "</td>";
                                $html .= "<td>" . $value->tipo_pago->nombre . "</td>";
                                $html .= "<td>" . date('d/m/Y', strtotime($value->fecha)) . "</td>";
                                $html .= "<td>" . $value->cabecera_ingresov->secuencia . "</td>";
                                //$html .= "<td>" . $value->cabecera_ingreso->secuencia . "</td>";
                                // $html .= "<td>";
                                // $html .= "</td>";
                                $html .= "<td>" . $value->numero . "</td>";
                                if ($request->id_forma_pago == 999) {

                                    $traer = Ct_Tipo_Tarjeta::where('id', $value->id_tipo_tarjeta)->first();
                                    if (isset($traer)) {
                                        $html .= "<td>" . $traer->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                } else {
                                    if (isset($value->banco)) {
                                        $html .= "<td>" . $value->banco->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                }
                                $html .= "<td>" . $value->cuenta . "</td>";
                                $html .= "<td>" . $value->girador . "</td>";
                                $html .= "<td>$</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "</tr>";
                            }
                        }
                    }
                }
            }
            $i++;
        }
        return $html;
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

        DB::beginTransaction();

        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }

            $request["nota"] = $request["referencia_deposito"];
            unset($request['referencia_deposito']);
            $num = "1";
            $id_empresa = $request->session()->get('id_empresa');
            $nums = Ct_Deposito_Bancario::where('empresa', $id_empresa)->get()->last();
            //dd($nums);
            if (!is_null($nums)) {
                $num = $nums->numero + 1;
            }

            $id_empresa = $request->session()->get('id_empresa');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;

            $id_plan_config = LogConfig::busqueda("1.01.01.1.01");
            $sucursal = $request['sucursal'];
            $input = [
                'fecha_asiento'      => $this->cambiarformatofecha($request['fecha_asiento']),
                'empresa'            => $id_empresa,
                'numero'             => $num,
                'tipo'               => $request['tipo'],
                'proyecto'           => $request['proyecto'],
                'concepto'           => $request['concepto'],
                'nota'               => $request['nota'],
                'estado'             => 1,
                'id_cuenta_origen'   => $id_plan_config,
                'valor_origen'       => $request['total_efectivo'],
                'id_cuenta_destino'  => $request['id_cuenta_destino'],
                'id_cuenta_destino2' => $request['id_cuenta_destino2'],
                'valor_destino'      => $request['valor1'],
                'valor_destino2'     => $request['valor2'],
                'total_efectivo'     => $request['total_efectivo'],
                'total_pap_deposito' => $request['total_pap_deposito'],
                'total_cheque'       => $request['total_cheques'],
                'total_tarjeta'      => $request['total_tarjetas'],
                'total_deposito'     => $request['total_deposito'],
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
            ];
            //dd($input);

            $id_asiento = $this->asiento($input);

            //dd($id_asiento);

            if ($id_asiento["status"] == "error") {
                DB::rollback();
                return $id_asiento;
            }
            // if(Auth::user()->id == "0957258056"){
            //     dd($id_asiento);
            // }
            $input['id_asiento'] = $id_asiento['id_asiento'];
            $input['id']         = Ct_Deposito_Bancario::insertGetId($input);
            // TODO: DETALLE
            $detalle = $request['id_comp'];
            //dd($input);
            $facturas = "";
            if (!is_null($detalle)) {
                foreach ($detalle as $det) {
                    $facturas = "";
                    $data     = Ct_Detalle_Pago_Ingreso::where('id', $det)->first();

                    $data->estado_deposito = 1;
                    $data->save();
                    if (isset($data->cabecera_ingreso->detalle)) {
                        foreach ($data->cabecera_ingreso->detalle as $detdeldet) {
                            $facturas .= "$detdeldet->secuencia_factura,";
                        }
                    }
                    $inputdetalle = [
                        'deposito_bancario_id' => $input['id'],
                        'numero'               => $data->numero,
                        'tipo'                 => $data->id_tipo,
                        'id_ingreso'           => $det,
                        'fecha'                => $data->fecha,
                        'facturas'             => $facturas,
                        'banco'                => $data->id_banco,
                        'cuenta'               => $data->cuenta,
                        'girador'              => $data->girador,
                        'id_divisa'            => 1,
                        'importe'              => $data->total,
                        'valor'                => $data->total,
                        'valor_base'           => $data->total,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'tipo_comprobante'     => is_null($data->tipo_c) ? 'CI' : $data->tipo_c
                    ];
                    Ct_Detalle_Deposito_Bancario::create($inputdetalle);
                }
            }
            if (isset($request->id_comp_n)) {
                for ($i = 0; $i < count($request->id_comp_n); $i++) {
                    if (!is_null($request['id_comp_n'][$i])) {
                        $asiento = LogAsiento::depositoBancario($request['id_comp_n'][$i], $input['id'], 'ING-DEPOSITO-BANCARIO', 'I');
                    }
                }
            }
            $input['msg'] = "Transacción generada satisfactoriamente";
            DB::commit();
            return ["status" => "success", "msj" => 'Transacción generada satisfactoriamente'];
            //return response()->json($input);

        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function getNumero()
    {
        $num = Ct_Deposito_Bancario::whereRaw('numero = (select ifnull(max(`numero`),0) from ct_deposito_bancario)')->get();
        return $num;
    }

    public function asiento($request)
    {

        try {

            $total_final = 0;
            if (!is_null($request['total_efectivo'])) {
                $total_final = $request['total_efectivo'];
            }
            if (!is_null($request['total_pap_deposito'])) {
                $total_final = $request['total_pap_deposito'];
            }
            if (!is_null($request['total_cheque'])) {
                $total_final = $request['total_cheque'];
            }
            if (!is_null($request['total_tarjeta'])) {
                $total_final = $request['total_tarjeta'];
            }
            if (!is_null($request['total_deposito'])) {
                $total_final = $request['total_deposito'];
            }
            $input_cabecera = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $request['fecha_asiento'],
                'id_empresa'      => $request['empresa'],
                'valor'           => $total_final,
                'id_usuariocrea'  => $request['id_usuariocrea'],
                'id_usuariomod'   => $request['id_usuariomod'],
                'ip_creacion'     => $request['ip_creacion'],
                'ip_modificacion' => $request['ip_modificacion'],
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //TODO: Asiento con el movimento del debe
            $id_plan_config = LogConfig::busqueda("1.01.01.1.01");
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'                => $request['id_cuenta_origen'],
                //'id_plan_cuenta'      => '1.01.01.01.01',
                'id_plan_cuenta'      => $id_plan_config,
                'descripcion'         => $request['concepto'],
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $total_final,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $request['id_usuariocrea'],
                'id_usuariomod'       => $request['id_usuariomod'],
                'ip_creacion'         => $request['ip_creacion'],
                'ip_modificacion'     => $request['ip_modificacion'],
            ];
            Ct_Asientos_Detalle::create($asiento_detalle);
            //TODO: Asiento con el movimento del haber

            $id_cuenta_destino = Plan_Cuentas_Empresa::where('id_plan', $request['id_cuenta_destino'])->where('id_empresa', $request['empresa'])->first();
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $id_cuenta_destino->id_plan,
                'descripcion'         => $request['concepto'],
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

            if (!is_null($request['id_cuenta_destino2']) and $request['valor_destino2'] > 0) {
                $id_cuenta_destino2 = Plan_Cuentas_Empresa::where('id_plan', $request['id_cuenta_destino2'])->where('id_empresa', $request['empresa'])->first();
                // dd($id_cuenta_destino2);
                if (is_null($id_cuenta_destino2)) {
                    return ["status" => "error", "msj" => "No se encontro la cuenta de destino 2", "mod" => "asiento"];
                }
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => $request['id_cuenta_destino2'],
                    'id_plan_cuenta'      => $id_cuenta_destino2->id_plan,
                    'descripcion'         => $request['concepto'],
                    'fecha'               => $request['fecha_asiento'],
                    'haber'               => '0',
                    'debe'                => $request['valor_destino2'],
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                Ct_Asientos_Detalle::create($asiento_detalle);
            }
            //return $id_asiento_cabecera;
            return ["status" => "success", "msj" => "guardado correctamente", "id_asiento" => $id_asiento_cabecera];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => "Error al crear asientos", "mod" => "asiento", "exp" => $e->getMessage()];
        }
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_asiento'        => $request['id_asiento'],
            'fecha_asiento'     => $request['fecha_asiento'],
            'id_cuenta_destino' => $request['id_cuenta_destino'],
            'concepto'          => $request['concepto'],
            'id'                => $request['numero'],
            'nota' => $request["nota"]
        ];
        //dd($constraints);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $cuentas    = Plan_Cuentas::all();
        $registros  = $this->doSearchingQuery($constraints, $id_empresa);
        return view('contable/deposito_bancario/index', ['request' => $request, 'cuentas' => $cuentas, 'registros' => $registros, 'searchingVals' => $constraints, "empresa" => $empresa]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Deposito_Bancario::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }

        return $query->where('empresa', $id_empresa)->orderby('id', 'desc')->paginate(25);
    }

    public function anular($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        DB::beginTransaction();
        try {

            //Obtenemos la fecha de Hoy
            $fechahoy   = Date('Y-m-d H:i:s');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $act_estado = [
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $registro = Ct_Deposito_Bancario::findorfail($id);

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($registro->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }

        $log_debito = LogAnularDeposito::where('id_deposito', $registro->id)->get();
        $detalles = Ct_Detalle_Deposito_Bancario::where('deposito_bancario_id', $registro->id)->get();
        //dd($detalles);
        if (!is_null($detalles)) {
            foreach ($detalles as $z) {
                $x = [
                    'estado'          => '0',
                    'estado_deposito' => '0',
                    'ip_modificacion' => $ip_cliente,
                ];
                Ct_Detalle_Pago_Ingreso::where('id', $z->id_ingreso)->update($x);
            }
        }
        foreach ($log_debito as $log) {
            $log->estado = 0;
            $log->id_usuariomod = $idusuario;
            $log->ip_modificacion = $ip_cliente;
            $log->save();
        }

        Ct_Deposito_Bancario::where('id', $id)->update($act_estado);
        Ct_Detalle_Deposito_Bancario::where('deposito_bancario_id', $registro->id)->update($act_estado);

            $this->anular_asiento($request, $registro->id_asiento);
            DB::commit();
            return ['status' => 'success', 'msj' => 'Anulado Correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
        //return redirect()->intended('/contable/Banco/depositobancario');
    }

    public function anular_asiento(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        // TODO: CABECERA DEL ASIENTO CONTABLE
        $cabasiento                = Ct_Asientos_Cabecera::find($id);
        $cabasiento->estado        = 1;
        $cabasiento->id_usuariomod = $idusuario;
        $cabasiento->save();
        $estado_asiento = Ct_Asientos_Cabecera::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($estado_asiento)) {
            $input_cabecera = [
                'observacion'     => $request['observacion'],
                'fecha_asiento'   => $cabasiento->fecha_asiento,
                'id_empresa'      => $cabasiento->id_empresa,
                'valor'           => $cabasiento->valor,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
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
            Log_Contable::create([
                'tipo'           => 'DBC',
                'valor_ant'      => $estado_asiento->valor,
                'valor'          => $estado_asiento->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'observacion'    => $estado_asiento->concepto,
                'id_ant'         => $estado_asiento->id,
                'id_referencia'  => $id_asiento_cabecera,
            ]);
        }
    }

    public function show($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Deposito_Bancario::findorfail($id);
        $detalles = Ct_Detalle_Deposito_Bancario::where('deposito_bancario_id', $id)->get();
        if (Auth::user()->id == '0951561075') {
            // dd($detalles);
        }
        // dd($detalles);
        $id_empresa  = \Session::get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $cuentas     = plan_cuentas::all();
        //validacion cierre año
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/deposito_bancario/show', [
            'divisas' => $divisas,
            'empresa'    => $empresa,
            'banco'       => $banco,
            'bancos'  => $bancos,
            'formas_pago' => $formas_pago,
            'cuentas' => $cuentas,
            'registro' => $registro,
            'detalles' => $detalles,
            "empresa" => $empresa,
            'mindate' => $min_date
        ]);
    }

    public function imprimir($id)
    {
        $registro = Ct_Deposito_Bancario::findorfail($id);
        //dd($registro);
        $vistaurl = "contable.deposito_bancario.pdf";
        $view     = \View::make($vistaurl, compact('registro'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Trasnferencia Bancaria-' . $id . '.pdf');
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
        $consulta = Ct_Deposito_Bancario::query();
        $fields   = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        $consulta = $consulta->get();
        Excel::create('Deposito Bancario-' . $fecha_asiento2 . '-al-' . $fecha_asiento2, function ($excel) use ($empresa, $consulta) {
            $excel->sheet('Deposito Bancario', function ($sheet) use ($empresa, $consulta) {
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
                    $cell->setValue('Cuentas Destino');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Efectivo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Cheque');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Tarjeta');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Deposito');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Transferencia');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
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
                        $cell->setValue($value->total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (isset($value->CuentaDestino)) {
                            $cell->setValue($value->CuentaDestino->nombre);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_efectivo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_cheque);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_tarjeta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_deposito);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_pap_deposito);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->usuario->nombre1 . ' ' . $value->usuario->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->estado == 0) {
                            $cell->setValue($value->modifica->nombre1 . ' ' . $value->modifica->apellido1);
                        } else {
                            $cell->setValue('');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->estado == 0) {
                            $cell->setValue("ANULADO");
                        } else {
                            $cell->setValue("ACTIVO");
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
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }
    public function update($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $input      = [
            'fecha_asiento'      => $this->cambiarformatofecha($request['fecha_asiento']),
            'empresa'            => $id_empresa,
            'concepto'           => $request['concepto'],
            'nota'               => $request['nota'],
            'estado'             => 1,
            'id_cuenta_destino'  => $request['id_cuenta_destino'],
            'id_cuenta_destino2' => $request['id_cuenta_destino2'],
            'id_usuariomod'      => $idusuario,
            'ip_modificacion'    => $ip_cliente,
        ];
        $depositoBancario = Ct_Deposito_Bancario::find($id);
        if (!is_null($depositoBancario)) {
            $depositoBancario->update($input);
            $cabecera = Ct_Asientos_Cabecera::find($depositoBancario->id_asiento);
            if (!is_null($cabecera)) {
                $cabecera->fecha_asiento = $this->cambiarformatofecha($request['fecha_asiento']);
                $cabecera->observacion   = $request['concepto'];
                $cabecera->save();
                foreach ($cabecera->detalles as $value) {
                    if ($value->id_plan_cuenta != "1.01.01.01.01") {
                        $details = Ct_Asientos_Detalle::find($value->id);
                        if (!is_null($details)) {
                            $details->id_plan_cuenta  = $request['id_cuenta_destino'];
                            $details->fecha           = $this->cambiarformatofecha($request['fecha_asiento']);
                            $details->id_usuariomod   = $idusuario;
                            $details->ip_modificacion = $ip_cliente;
                            $details->save();
                        }
                    }
                }
            }
        }
        return redirect()->route("depositobancario.show", ['id' => $id]);
    }
}
