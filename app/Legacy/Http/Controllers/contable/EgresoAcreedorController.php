<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Mail;
use Sis_medico\User;
use Excel;
use Sis_medico\Ct_rfir;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Factura_Contable;
use Sis_medico\Ct_compras;
use Sis_medico\ct_master_tipos;
use Sis_medico\Ct_Detalle_Pago;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Contable;
use Sis_medico\Ct_Anticipo_Proveedores;
use Sis_medico\Retenciones;
use Sis_medico\Validate_Decimals;
use Sis_medico\Numeros_Letras;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Comprobante_Secuencia;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Globales;
use Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Comprobante_Egreso_Solicitud;
use Sis_medico\Ct_configuraciones_pdf;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Solicitud;
use Sis_medico\LogConfig;
use Sis_medico\Numeros_Letras2;

class EgresoAcreedorController extends Controller
{
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
    // public static function confgcuenta_abono()
    // {
    //     $id_empresa = Session::get("id_empresa");
    //     $cuentaIva = "2.01.03.01.01";
    //     if ($id_empresa == "1793135579001") {
    //         $cuentaIva = "2.01.01.01.01";
    //     }
    //     return $cuentaIva;
    // }
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('contable/egresos/index');
    }
    public function create()
    {
        //dd(Ct_Asientos_Cabecera::get());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_divisas::where('estado', '1')->get();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        return view('contable/egresos/create', ['divisas' => $divisas, 'c_tributario' => $c_tributario, 't_comprobante' => $t_comprobante]);
    }
    public function buscar(Request $request)
    {
        //dd($request->all());

        if ($request->excel == 0) {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            $id_empresa = $request->session()->get('id_empresa');
            $armHtml = '';
            if ($request->proveedor != null) {
                $proveeBuscar = Proveedor::find($request->proveedor);
                $armHtml = $proveeBuscar->id . '-' . $proveeBuscar->nombrecomercial;
            }



            $proveedores = Proveedor::where('estado', '1')->get();
            $constraints = [
                'id'                  => $request['id'],
                'id_proveedor'        => $request['proveedor'],
                'secuencia'           => $request['secuencia'],
                'no_cheque'           => $request['cheque'],
                'descripcion'         => $request['descripcion'],
                'fecha_cheque'        => $request['fecha'],
                'id_asiento_cabecera' => $request['asiento_id'],
            ];
            //dd($constraints);
            $comp_egreso = $this->doSearchingQuery($constraints, $id_empresa);
            $empresa = Empresa::where('id', $id_empresa)->first();
            return view('contable/comp_egreso/index', ['armHtml' => $armHtml, 'comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'proveedor' => $proveedores, 'empresa' => $empresa]);
        }
        if ($request->excel == 1) {
            self::excel_comprobante_egreso($request);
        }
    }

    public function excel_comprobante_egreso(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proveedores = Proveedor::where('estado', '1')->get();
        $query = Ct_Comprobante_Egreso::query();
        if ($request->has('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
        if ($request->has('proveedor') && $request->proveedor !== null) {
            $query->where('id_proveedor', $request->proveedor);
        }
        if ($request->has('secuencia') && $request->secuencia !== null) {
            $query->where('secuencia', $request->secuencia);
        }
        if ($request->has('descripcion') && $request->descripcion !== null) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }
        if ($request->has('fecha') && $request->fecha !== null) {
            $query->where('fecha_cheque', $request->fecha);
        }
        if ($request->has('cheque') && $request->cheque !== null) {
            $query->where('nro_cheque', $request->cheque);
        }
        if ($request->has('asiento_id') && $request->asiento_id !== null) {
            $query->where('id_asiento_cabecera', $request->asiento_id);
        }
        $comp_egreso = $query->orderby('id', 'desc')->get();

        $titulos = array("Id", "Asiento", "Serie", "Fecha de Comprobante", "Fecha Cheque", "Nro_Cheque", "Proveedor", "Descripción", "Valor", "Creado por", "Anulado por", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('COMPROBANTE DE EGRESO', function ($excel) use ($titulos, $posicion, $comp_egreso) {
            $excel->sheet('COMPROBANTE DE EGRESO', function ($sheet) use ($titulos, $posicion, $comp_egreso) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE DE EGRESO');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });
                $comienzo = 4;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $total = 0;
                foreach ($comp_egreso as $detalle) {


                    if ($detalle->valor_pago > 0) {
                        $valor = round($detalle->valor_pago, 2);
                        $total += $detalle->valor_pago;
                        $valor = round($detalle->valor_pago, 2);
                    } else if (isset($detalle->asiento_cabecera)) {
                        $valor = round($detalle->asiento_cabecera->valor, 2);
                        $total += $detalle->asiento_cabecera->valor;
                        $valor = round($detalle->asiento_cabecera->valor, 2);
                    }


                    $total_formateado = round($total, 2);


                    $estado = '';
                    if ($detalle->estado == '1') {
                        $estado = 'Activo';
                    } elseif ($detalle->estado == '0') {
                        $estado = 'Anulada';
                    }
                    $anulado_por = '';
                    if ($detalle->estado == '0') {
                        if (isset($detalle->usuariomod)) {
                            $anulado_por = $detalle->usuariomod->nombre1 . ' ' . $detalle->usuariomod->apellido1;
                        }
                    }
                    $Creado_por = '';

                    if (isset($detalle->usuario)) {
                        $Creado_por     =  $detalle->usuario->nombre1 . ' ' . $detalle->usuario->nombre2;
                    }




                    $proveedor = '';
                    if (isset($detalle->proveedor)) {
                        $proveedor = $detalle->proveedor->razonsocial;
                    }

                    $datos_excel = [];

                    $datos_excel = [$detalle->id, $detalle->id_asiento_cabecera, $detalle->secuencia, $detalle->fecha_comprobante, $detalle->fecha_cheque, $detalle->no_cheque, $proveedor, $detalle->descripcion, $valor, $Creado_por, $anulado_por, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 8) {
                                $cell->setAlignment('right');
                            } elseif ($i == 6) {
                                $cell->setAlignment('left');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;

                $sheet->cell('H' . $comienzo, function ($cell) {
                    $cell->setValue('VALOR TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                if (isset($total_formateado)) {
                    $sheet->cell('I' . $comienzo, function ($cell) use ($total_formateado) {
                        $cell->setValue($total_formateado);
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                    });
                }


                //dd($total_formateado);
                $sheet->setWidth('A', 10);
                $sheet->setWidth('B', 10);
                $sheet->setWidth('C', 12);
                $sheet->setWidth('D', 21);
                $sheet->setWidth('E', 14);
                $sheet->setWidth('F', 12);
                $sheet->setWidth('G', 60);
                $sheet->setWidth('H', 100);
                $sheet->setWidth('I', 15);
                $sheet->setWidth('j', 23);
                $sheet->setWidth('k', 23);
                $sheet->setWidth('L', 12);
            });
        })->export('xlsx');
    }

    public function buscar_varios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if ($request->excel == 0) {
            $id_empresa = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->first();
            $constraints = [
                'id'                  => $request['id'],
                'beneficiario'        => $request['beneficiario'],
                'secuencia'           => $request['secuencia'],
                'nro_cheque'          => $request['cheque'],
                'descripcion'         => $request['descripcion'],
                'fecha_cheque'        => $request['fecha'],
                'id_asiento_cabecera' => $request['asiento_cabecera'],
            ];
            $comp_egreso = $this->doSearchingQuery2($constraints, $id_empresa);
            return view('contable/comp_egreso_varios/index', ['comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'empresa' => $empresa]);
        }
        if ($request->excel == 1) {
            self::excel_comprobante_varios($request);
        }
    }

    public function excel_comprobante_varios(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }



        $query = Ct_Comprobante_Egreso_Varios::query();
        if ($request->has('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
        if ($request->has('beneficiario') && $request->beneficiario !== null) {
            $query->where('beneficiario', $request->beneficiario);
        }
        if ($request->has('secuencia') && $request->secuencia !== null) {
            $query->where('secuencia', $request->secuencia);
        }
        if ($request->has('descripcion') && $request->descripcion !== null) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }
        if ($request->has('fecha') && $request->fecha !== null) {
            $query->where('fecha_cheque', $request->fecha);
        }
        if ($request->has('cheque') && $request->cheque !== null) {
            $query->where('nro_cheque', $request->cheque);
        }
        if ($request->has('id_asiento_cabecera') && $request->id_asiento_cabecera !== null) {
            $query->where('id_asiento_cabecera', $request->id_asiento_cabecera);
        }
        $comp_egreso_varios = $query->orderby('id', 'desc')->get();

        $titulos = array("Asiento Cabecera", "Fecha de Comprobante", "Serie", "Fecha Cheque", "Nro_Cheque", "Proveedor", "Descripción", "Valor", "Creado por", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Comprobante de Egreso Varios', function ($excel) use ($titulos, $posicion, $comp_egreso_varios) {
            $excel->sheet('Comprobante de Egreso Varios', function ($sheet) use ($titulos, $posicion, $comp_egreso_varios) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Comprobante de Egreso Varios');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });
                $comienzo = 4;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $total = 0;

                foreach ($comp_egreso_varios as $detalle) {

                    $proveedor = '';
                    if (($detalle->proveedor != null)) {
                        $proveedor = $detalle->proveedor->razonsocial;
                    }

                    $valor = round($detalle->valor, 2);
                    $total += $detalle->valor;
                    $valor = round($detalle->valor, 2);
                    $total_formateado = round($total, 2);

                    $estado = '';
                    if ($detalle->estado == '1') {
                        $estado = 'Activo';
                    } elseif ($detalle->estado == '0') {
                        $estado = 'Anulada';
                    }

                    $Creado_por = '';

                    if (isset($detalle->usuario)) {
                        $Creado_por     =  $detalle->usuario->nombre1 . ' ' . $detalle->usuario->nombre2;
                    }
                    $datos_excel = [];

                    $datos_excel = [$detalle->id_asiento_cabecera, $detalle->fecha_comprobante, $detalle->secuencia, $detalle->fecha_cheque, $detalle->no_cheque, $proveedor, $detalle->descripcion, $valor, $Creado_por, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 7) {
                                $cell->setAlignment('right');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }

                $comienzo++;
                $sheet->cell('G' . $comienzo, function ($cell) {
                    $cell->setValue('VALOR TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                if (isset($total_formateado)) {
                    $sheet->cell('H' . $comienzo, function ($cell) use ($total_formateado) {
                        $cell->setValue($total_formateado);
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                    });
                }
                //dd($total_formateado);
                $sheet->setWidth('A', 18);
                $sheet->setWidth('B', 15);
                $sheet->setWidth('C', 15);
                $sheet->setWidth('D', 15);
                $sheet->setWidth('E', 15);
                $sheet->setWidth('F', 25);
                $sheet->setWidth('G', 120);
                $sheet->setWidth('H', 25);
                $sheet->setWidth('I', 20);
            });
        })->export('xlsx');
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, $id_empresa)
    {
        $query  = Ct_Comprobante_Egreso::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == "id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                if ($fields[$index] == "id_asiento_cabecera") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }


    private function doSearchingQuery1($constraints, $id_empresa)
    {
        $query  = Ct_Comprobante_Egreso_Solicitud::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == "id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                if ($fields[$index] == "id_asiento_cabecera") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }

    private function doSearchingQuery2($constraints, $id_empresa)
    {
        $query  = Ct_Comprobante_Egreso_Varios::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == "id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                if ($fields[$index] == "id_asiento_cabecera") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }
    public function edit()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('contable/egresos/edit');
    }
    public function comprobante_index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $empresas = Empresa::all();
        foreach ($empresas as $emp) {
            $maestra = Ct_Globales::where('id_empresa', '0992704152001')->where('id_modulo', 18)->first();
            $globales = Ct_Globales::where('id_empresa', $emp->id)->where('id_modulo', 18)->first();
            if (is_null($globales)) {
                $data = $maestra['attributes'];
                unset($data['id']);
                //dd($emp->id,$data);
                $data['id_empresa'] = $emp->id;
                Ct_Globales::create($data);
            }
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $facturas_compras = Ct_compras::where('estado', '>', '0')->get();
        $comp_egreso = Ct_Comprobante_Egreso::where('tipo', '1')->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($comp_egreso);
        return view('contable/comp_egreso/index', ['empresa' => $empresa, 'divisas' => $divisas, 'facturas_compras' => $facturas_compras, 'proveedor' => $proveedores, 'comp_egreso' => $comp_egreso]);
    }
    public function comprobante_create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')
            ->where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->get();
        return view('contable/comp_egreso/create', ['min_date' => $min_date, 'divisas' => $divisas, 'sucursales' => $sucursales, 'proveedor' => $proveedores, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago]);
    }
    public function comprobante_edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd("hola");
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //dd($banco);
        $comprobante_egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $comprobante_egreso->id)->get();
        //dd($comprobante_egreso);
        return view('contable/comp_egreso/edit', ['divisas' => $divisas, 'sucursales' => $sucursales, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago, 'detalle_egreso' => $detalle_comprobante, 'comprobante_egreso' => $comprobante_egreso]);
    }
    public function  update_rt_asumidas(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('empresa');
        $comprobante = Ct_Comprobante_Egreso::where("id", $request->id)->where("estado", 1)->first();
        if (!is_null($comprobante)) {
            if ($request->estado == 0) {
                $comprobante->rt_asumida_estado = $request->estado;
                $comprobante->save();
            } else {
                $asiento = Ct_Asientos_Cabecera::find($comprobante->id_asiento_cabecera);
                $caja_banco = Ct_Caja_Banco::find($request->banco);
                $id_proveedor = $request->id_proveedor;
                foreach ($asiento->detalles as $value) {
                    $globales = Ct_Globales::where('id_empresa', $id_empresa)->where('id_modulo', 18)->first();
                    //if ($value->id_plan_cuenta == '1.01.04.03') {
                    if ($value->id_plan_cuenta == $globales->debe) {
                        $value->debe = 0;
                        $value->haber = 0;
                        $value->save();
                    }
                }
                if ($request->rt_asumidas >= 0) {
                    //dd($request->rt_asumidas);
                    $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();
                    //dd($rt_cuenta);
                    $details = Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $comprobante->id_asiento_cabecera,
                        'id_plan_cuenta'                => $rt_cuenta->id,
                        'descripcion'                   => $rt_cuenta->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'debe'                          => $request->rt_asumidas,
                        'haber'                         => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                    //dd($details);
                    $comprobante->rt_asumida_valor = $request->rt_asumidas;
                }
                $comprobante->rt_asumida_estado = $request->estado;
                $comprobante->save();
            }
        }
        //dd($comprobante->secuencia);
        return ['estado' => $request->estado];
        //dd($comprobante);
    }
    public function update_comprobante_observacion($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $egreso = Ct_Comprobante_Egreso::find($id);
        $input_comprobante = [
            'descripcion'     => $request['aaa'],
            'no_cheque'       => $request['numero_cheque'],
            'comentarios'     => $request['nota'],
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $egreso->update($input_comprobante);
        return redirect()->route('egresoa_edit', ['id' => $id]);
    }
    public function update_comprobante($id, Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $egreso = Ct_Comprobante_Egreso::find($id);
        $asiento = Ct_Asientos_Cabecera::find($egreso->id_asiento_cabecera);
        $id_proveedor = $request['id_proveedor'];
        $id_asiento_cabecera = $asiento->id;
        $rt_asumida_valor = 0;
        $rt_asumida_estado = 0;
        $rt_asumida_resta = 0;
        $cabecera_valor = Ct_Asientos_Cabecera::find($egreso->id_asiento_cabecera);
        if ($id_empresa != "0992704152001" && $request->rt_asumidas > 0) {
            $rt_asumida_valor = $request->rt_asumidas;
            $rt_asumida_estado = 1;
            $rt_asumida_resta = $cabecera_valor->valor - $request->rt_asumidas;
        }
        $input_comprobante = [
            'descripcion'     => $request['aaa'],
            'estado'          => '1',
            'beneficiario'    => $request['nombre_proveedor'],
            'fecha_cheque'    => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'check'           => $request['verificar_cheque'],
            'id_secuencia'    => $request['numero'],
            'id_pago'         => $request['formas_pago'],
            'id_caja_banco'   => $request['banco'],
            'no_cheque'       => $request['numero_cheque'],
            'fecha_comprobante' => $request['fecha_hoy'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'rt_asumida_estado' => $rt_asumida_estado,
            'rt_asumida_valor' => $rt_asumida_valor,
        ];
        $egreso->update($input_comprobante);
        $cabecera = Ct_Asientos_Cabecera::find($egreso->id_asiento_cabecera);
        $caja_banco = Ct_Caja_Banco::find($request['banco']);
        $cuenta = $caja_banco->cuenta_mayor;
        if (!is_null($cabecera)) {
            $cabecera->fecha_asiento = $request['fecha_hoy'];
            $cabecera->observacion = $request['aaa'];
            $cabecera->save();
            // $cuentaanticipo = "1.01.04.03";
            // $cuenta_prov->cuenta_guardar = "2.01.03.01.02";
            // if ($id_empresa == "1793135579001") {
            //     $cuentaanticipo = "1.01.04.03.01";
            //     $cuenta_prov->cuenta_guardar = "2.02.01.01.01";
            // }

            $cuenta_prov = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EGRESOACREEDOR_PROV_LOC'); //proveedores locales
            $cuenta_anticipo = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EGRESOACREEDOR_ANT_PROV'); //anticipo proveedores


            foreach ($cabecera->detalles as $value) {
                if ($value->id_plan_cuenta != $cuenta_prov->secuencial_mostrar && $value->id_plan_cuenta != $cuenta_prov->secuencial_mostrar && $value->id_plan_cuenta != $cuenta_anticipo->secuencial_mostrar) {
                    $details = Ct_Asientos_Detalle::find($value->id);
                    if (!is_null($details)) {
                        $details->id_plan_cuenta = $cuenta;
                        $details->fecha = $request['fecha_hoy'];
                        $details->id_usuariomod = $idusuario;
                        $details->ip_modificacion = $ip_cliente;
                        $details->save();
                    }
                }
            }
        }
        return redirect()->route('egresoa_edit', ['id' => $id]);
    }
    public function getSecuencia($tipo)
    {
        $user = Auth::user()->id;
        $secuencia = "00000000001";
        $id_empresa = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $comp_secuencia = Ct_Comprobante_Secuencia::where('tipo', $tipo)->where('empresa', $id_empresa)->first();
        if (is_null($comp_secuencia)) {
            Ct_Comprobante_Secuencia::create([
                'tipo'              => $tipo,
                'secuencia'         => '0000000001',
                'empresa'           => $id_empresa,
                'id_usuariocrea'    => $user,
                'id_usuariomod'     => $user,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ]);
            return '0000000001';
        } else {
            $max_id = intval($comp_secuencia->secuencia);
            if (strlen($max_id) < 10) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                $comp_secuencia->secuencia = $numero_factura;
                $comp_secuencia->id_usuariomod = $user;
                $comp_secuencia->ip_modificacion = $ip_cliente;
                $comp_secuencia->save();
                return $numero_factura;
            }
        }
    }
    public function comprobante_store(Request $request)
    {
        $numero_factura = 0;
        $superavit = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia = 0;
        $id_proveedor = $request['id_proveedor'];
        $id_empresa = session('id_empresa');
        $cuentas = Proveedor::where('id', $id_proveedor)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $contador_ctv = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->get()->count();
        // $id_comprobante = array();
        $numero_factura = 0;
        $banco = (int) $request['banco'];
        $objeto_validar = new Validate_Decimals();
        $valor_asumida = 0;
        DB::beginTransaction();
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            if ($superavit != 0) {
                // if ($request['contador'] != null) {
                // if ($contador_ctv == 0) { //si no tiene secuencia le creara una
                //     $num = '1';
                //     $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                //     DB::table('ct_comprobante_secuencia')->insert([
                //         'tipo' => 2,
                //         'empresa' => $id_empresa,
                //         'secuencia' => $numero_factura,
                //         'ip_creacion'           => $ip_cliente,
                //         'ip_modificacion'       => $ip_cliente,
                //         'id_usuariocrea'        => $idusuario,
                //         'id_usuariomod'         => $idusuario
                //     ]);
                // } else { //si tiene secuencia 
                //     /**Cambios**/
                //     $max_id = DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->latest('id')->first();
                //     if (is_null($max_id)) {
                //         $nu = 1;
                //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                //         DB::table('ct_comprobante_secuencia')->insert([
                //             'tipo' => 1,
                //             'empresa' => $id_empresa,
                //             'secuencia' => $numero_factura,
                //             'ip_creacion'           => $ip_cliente,
                //             'ip_modificacion'       => $ip_cliente,
                //             'id_usuariocrea'        => $idusuario,
                //             'id_usuariomod'         => $idusuario
                //         ]);
                //     } else {
                //         $max_id = intval($max_id->secuencia);
                //         if (strlen($max_id) < 10) {
                //             $nu = $max_id + 1;
                //             $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                //         }
                //         DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->update([
                //             'secuencia'             => $numero_factura,
                //             'ip_creacion'           => $ip_cliente,
                //             'ip_modificacion'       => $ip_cliente,
                //             'id_usuariocrea'        => $idusuario,
                //             'id_usuariomod'         => $idusuario,
                //         ]);
                //     }
                // }
                $numero_factura =  LogAsiento::getSecuencia(1, 1);
                if (!is_null($request['total_favor'])) {
                    //$numero_factura = $this->getSecuencia(1);
                    $nuevo_saldof = $objeto_validar->set_round($request['total_favor']);
                    $input_cabecera = [
                        'observacion'                   => $request['aaa'],
                        'fecha_asiento'                 => $request['fecha_hoy'],
                        'fact_numero'                   => $numero_factura,
                        'valor'                         => $request['total_favor'],
                        'id_empresa'                    => $id_empresa,
                        'estado'                        => '1',
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'sucursal'                      => $request['sucursal'],
                        //'estado_manual'                 => 2,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    if (($banco) != null) {
                        $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                            'descripcion'                   => $consulta_db_cajab->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'haber'                         => $nuevo_saldof,
                            'debe'                          => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    $saldos_ret = 0;
                    $rt_asumida_valor = 0;
                    $rt_estado = 0;
                    if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0) {
                        $saldos_ret = $nuevo_saldof - $request->rt_asumidas;
                    } else {
                        $saldos_ret = $nuevo_saldof;
                    }
                    if ($id_proveedor != null) {
                        $globales = Ct_Globales::where('id_empresa', $id_empresa)->where('id_modulo', 18)->first();
                        $desc_cuenta = Plan_Cuentas::where('id', $globales->debe)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            //'id_plan_cuenta'                => '1.01.04.03',
                            'id_plan_cuenta'                => $desc_cuenta->id,
                            'descripcion'                   => $desc_cuenta->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'debe'                          => $saldos_ret,
                            'haber'                         => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    if ($request->rt_asumidas != null  || $request->rt_asumidas != "") {
                        if ($request->rt_asumidas > 0) {
                            $rt_estado = 1;
                            $rt_asumida_valor = $request->rt_asumidas;
                            $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();
                            if ($rt_cuenta == '') {
                            }
                            $arrayAsiento = [
                                'id_asiento_cabecera'           => $id_asiento_cabecera,
                                'id_plan_cuenta'                => $rt_cuenta->id,
                                'descripcion'                   => $rt_cuenta->nombre,
                                'fecha'                         => $request['fecha_hoy'],
                                'debe'                          => $request->rt_asumidas,
                                'haber'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ];
                            Ct_Asientos_Detalle::insert($arrayAsiento);
                        }
                    }
                    $aux_numero_factura = $numero_factura;
                    $input_comprobante = [
                        'descripcion'     => $request['aaa'] . ' REF: ' . $aux_numero_factura,
                        'estado'          => '1',
                        'beneficiario'    => $request['nombre_proveedor'],
                        'tipo'            => '2',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => $aux_numero_factura,
                        'id_pago'         => $request["formas_pago"],
                        'check'           => $request['verificar_cheque'],
                        'fecha_cheque'    => $request['fecha_cheque'],
                        'id_caja_banco'   => $request['banco'],
                        'no_cheque'       => $request['numero_cheque'],
                        'fecha_comprobante' => $request['fecha_hoy'],
                        'secuencia'       => $aux_numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_proveedor'    => $id_proveedor,
                        'valor_pago'      => $nuevo_saldof,
                        'valor'           => $nuevo_saldof,
                        'comentarios'     => $request['nota'],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'rt_asumida_estado' => $rt_estado,
                        'rt_asumida_valor' => $rt_asumida_valor,
                        'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
                    Ct_Detalle_Comprobante_Egreso::create([
                        'id_comprobante'                 => $id_comprobante,
                        'id_secuencia'                   => $aux_numero_factura,
                        'saldo_base'                     => $request['total_favor'],
                        'abono'                          => $request['total_favor'],
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                    //}
                }
                //  }
            } else {
                if ($request['contador'] != null) {
                    $rt_asimida_creado = 0;
                    $numero_factura = LogAsiento::getSecuencia(1, 1);
                    $aux_numero_factura = $numero_factura;
                    $input_cabecera = [
                        'observacion'                   => $request['aaa'],
                        'fecha_asiento'                 => $request['fecha_hoy'],
                        'fact_numero'                   => $numero_factura,
                        'valor'                         => $request['valor_cheque'],
                        'id_empresa'                    => $id_empresa,
                        'estado'                        => '1',
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'sucursal'                      => $request['sucursal'],
                        //'estado_manual'                 => 2,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    if (($banco) != null) {
                        $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
                        $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                        $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                            'descripcion'                   => $consulta_db_cajab->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'haber'                         => $nuevo_saldof,
                            'debe'                          => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    $input_comprobante = [
                        'descripcion'     => $request['aaa'],
                        'estado'          => '1',
                        'beneficiario'    => $request['nombre_proveedor'],
                        'fecha_cheque'    => $request['fecha_cheque'],
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'check'           => $request['verificar_cheque'],
                        'id_secuencia'    => $request['numero'],
                        'id_pago'         => $request['formas_pago'],
                        'id_caja_banco'   => $request['banco'],
                        'no_cheque'       => $request['numero_cheque'],
                        'fecha_comprobante' => $request['fecha_hoy'],
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_proveedor'    => $id_proveedor,
                        'valor_pago'      => $request['valor_cheque'],
                        'valor'           => $request['valor_cheque'],
                        'comentarios'     => $request['nota'],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'rt_asumida_estado' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? 1 : 0,
                        'rt_asumida_valor' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? $request->rt_asumidas : 0,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
                    for ($i = 0; $i <= $request['contador']; $i++) {
                        //$cuenta_prov = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EGRESOACREEDOR_PROV_LOC');//proveedores locales
                        //$id_plan_config = LogConfig::busqueda('1.01.04.03.01');
                        $id_plan_config = LogConfig::busqueda('2.01.03.01.01');
                        $desc_cuenta  = Plan_Cuentas::where('id', $id_plan_config)->first();
                        if (!is_null($request['abono' . $i])) {
                            if ($request['abono' . $i] > 0) {
                                if ($id_proveedor != null) {
                                    $nuevo_saldof = $objeto_validar->set_round($request['abono' . $i]);

                                    if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0) {
                                        if ($rt_asimida_creado == 0) {
                                            // $nuevo_saldof = $nuevo_saldof - $request->rt_asumidas;
                                            $nuevo_saldof = $request["valor_cheque"] - $request->rt_asumidas;
                                            $rt_asimida_creado = 1;
                                        }
                                    }

                                    //$desc_cuenta = Plan_Cuentas::where('id', EgresoAcreedorController::confgcuenta_abono())->first();
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                                        'id_plan_cuenta'                => $desc_cuenta->id,
                                        'descripcion'                   => $desc_cuenta->nombre,
                                        'fecha'                         => $request['fecha_hoy'],
                                        'debe'                          => $nuevo_saldof,
                                        'haber'                         => '0',
                                        'estado'                        => '1',
                                        'id_usuariocrea'                => $idusuario,
                                        'id_usuariomod'                 => $idusuario,
                                        'ip_creacion'                   => $ip_cliente,
                                        'ip_modificacion'               => $ip_cliente,
                                    ]);
                                }
                                $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();

                                if (!is_null($consulta_compra)) {
                                    $valor_compra = $request['abono' . $i];
                                    if ($rt_asimida_creado == 1) {
                                        $valor_compra = $request->valor_cheque;
                                    }
                                    Ct_Detalle_Comprobante_Egreso::create([
                                        'id_comprobante'                 => $id_comprobante,
                                        'observacion'                    => $request['aaa'],
                                        'id_compra'                      => $consulta_compra->id,
                                        'id_secuencia'                   => $request['numero' . $i],
                                        'saldo_base'                     => $request['saldo' . $i],
                                        // 'abono'                          => $request['abono' . $i],
                                        'abono'                          => $valor_compra,
                                        'estado'                         => '1',
                                        'ip_creacion'                    => $ip_cliente,
                                        'ip_modificacion'                => $ip_cliente,
                                        'id_usuariocrea'                 => $idusuario,
                                        'id_usuariomod'                  => $idusuario,
                                    ]);
                                }
                            }
                        }
                    }
                    $nuevosaldox = $request['total_favor'];

                    // if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0){
                    if ($rt_asimida_creado == 1) {

                        $valor_asumida = $request->rt_asumidas;
                        // $id_plan_config = 

                        $cuenta_rt_asumida = LogConfig::busqueda('5.2.02.16.17');
                        $rt_cuenta = Plan_Cuentas::find($cuenta_rt_asumida);
                        if (is_null($rt_cuenta)) {
                            DB::rollback();
                            return ['status' => 'error', 'msj' => 'No se encontro la cuenta para la Retencion Asumida'];
                        }
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $rt_cuenta->id,
                            'descripcion'                   => $rt_cuenta->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'debe'                          => $request->rt_asumidas,
                            'haber'                         => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                        $rt_asimida_creado = 1;
                    }


                    if ($request['comprobarx'] == '1' || $request['comprobarx'] == 1) {




                        // if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0){
                        //     $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();
                        //     Ct_Asientos_Detalle::create([
                        //          'id_asiento_cabecera'           => $id_asiento_cabecera,
                        //         'id_plan_cuenta'                => $rt_cuenta->id,
                        //         'descripcion'                   => $rt_cuenta->nombre,
                        //         'fecha'                         => $request['fecha_hoy'],
                        //         'debe'                          => $request->rt_asumidas,
                        //         'haber'                         => '0',
                        //         'estado'                        => '1',
                        //         'id_usuariocrea'                => $idusuario,
                        //         'id_usuariomod'                 => $idusuario,
                        //         'ip_creacion'                   => $ip_cliente,
                        //         'ip_modificacion'               => $ip_cliente,
                        //     ]);
                        // }
                        if ($rt_asimida_creado == 0) {
                            $globales = Ct_Globales::where('id_empresa', $id_empresa)->where('id_modulo', 18)->first();
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera'           => $id_asiento_cabecera,
                                //'id_plan_cuenta'                => '1.01.04.03', //cambiar a anticipo proveedores
                                'id_plan_cuenta'                => $globales->debe,
                                'descripcion'                   => 'Anticipo a Proveedores',
                                'fecha'                         => $request['fecha_hoy'],
                                'debe'                          => $request['total_favor'],
                                'haber'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        }



                        /**Tipo 2 CAMBIO */
                        // $aux = DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->latest('id')->first();
                        // $aux_numero_factura = 0;
                        // if (is_null($aux)) {
                        //     $aux_nu = 1;
                        //     $aux_numero_factura = str_pad($aux_nu, 10, "0", STR_PAD_LEFT);
                        //     DB::table('ct_comprobante_secuencia')->insert([
                        //         'tipo'                  => 1,
                        //         'empresa'               => $id_empresa,
                        //         'secuencia'             => $aux_numero_factura,
                        //         'ip_creacion'           => $ip_cliente,
                        //         'ip_modificacion'       => $ip_cliente,
                        //         'id_usuariocrea'        => $idusuario,
                        //         'id_usuariomod'         => $idusuario
                        //     ]);
                        // } else {
                        //     $aux_max_id = intval($aux->secuencia);
                        //     if (strlen($aux_max_id) < 10) {
                        //         $aux_nu = $aux_max_id + 1;
                        //         $aux_numero_factura = str_pad($aux_nu, 10, "0", STR_PAD_LEFT);
                        //     }
                        //     DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->update([
                        //         'secuencia'             => $aux_numero_factura,
                        //         'ip_creacion'           => $ip_cliente,
                        //         'ip_modificacion'       => $ip_cliente,
                        //         'id_usuariocrea'        => $idusuario,
                        //         'id_usuariomod'         => $idusuario,
                        //     ]);
                        // }
                        if (!isset($request["ch_rt_asumidas"])) {
                            $aux_numero_factura = LogAsiento::getSecuencia(2, 1);
                            /**FINN DE CAMBIOS */
                            $input_comprobante2 = [
                                'descripcion'     => $request['aaa'] . ' REF: ' . $aux_numero_factura,
                                'estado'          => '1',
                                'beneficiario'    => $request['nombre_proveedor'],
                                'tipo'            => '2',
                                'id_asiento_cabecera' => $id_asiento_cabecera,
                                'id_secuencia'    => $aux_numero_factura,
                                'id_pago'         => '1',
                                'check'           => $request['verificar_cheque'],
                                'fecha_cheque'    => $request['fecha_cheque'],
                                'id_caja_banco'   => $request['banco'],
                                'no_cheque'       => $request['numero_cheque'],
                                'fecha_comprobante' => $request['fecha_hoy'],
                                'secuencia'       => $aux_numero_factura,
                                'id_empresa'      => $id_empresa,
                                'id_proveedor'    => $id_proveedor,
                                'valor_pago'      => $nuevosaldox,
                                'valor'           => $nuevosaldox,
                                'comentarios'     => $request['nota'],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                // 'rt_asumida_estado' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? 1 : 0,
                                // 'rt_asumida_valor' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? $request->rt_asumidas : 0,
                                'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                            ];
                            $id_comprobante2 = Ct_Comprobante_Egreso::insertGetId($input_comprobante2);
                            Ct_Detalle_Comprobante_Egreso::create([
                                'id_comprobante'                 => $id_comprobante2,
                                'id_secuencia'                   => $aux_numero_factura,
                                'saldo_base'                     => $request['total_favor'],
                                'abono'                          => $request['total_favor'],
                                'estado'                         => '1',
                                'ip_creacion'                    => $ip_cliente,
                                'ip_modificacion'                => $ip_cliente,
                                'id_usuariocrea'                 => $idusuario,
                                'id_usuariomod'                  => $idusuario,
                            ]);
                        }
                    }
                    $consulta_compra = null;
                    $input_actualiza = null;

                    /*************************************
                     ****ACTUALIZO CUANDO ES COMPRA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE EGRESO***
                        /*************************************/

                    for ($i = 0; $i <= $request['contador']; $i++) {
                        if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                            $nuevo_saldo = 0;
                            //actualizar valor contable de cada tabla
                            $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->where('id_empresa', $id_empresa)->first();

                            if (!empty($consulta_compra) || $consulta_compra != '[]') {

                                if ($request['abono' . $i] > 0) {
                                    $new_valor = $request['abono' . $i];
                                    //805.35
                                    if ($valor_asumida > 0) {
                                        $new_valor = $request->valor_cheque - $valor_asumida;
                                    }
                                    if ($new_valor > ($consulta_compra->valor_contable)) {
                                        // $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                                        $nuevo_saldo = $new_valor - $consulta_compra->valor_contable;
                                    } else {
                                        // $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                                        $nuevo_saldo = $consulta_compra->valor_contable - $new_valor;
                                    }
                                    $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                                    $input_actualiza = null;
                                    if ($nuevo_saldof != 0) {
                                        $input_actualiza = [
                                            'estado'                        => '2', //poner otro estado para que no salga en las consultas
                                            'valor_contable'                => $nuevo_saldof,
                                            'ip_modificacion'               => $ip_cliente,
                                            'id_usuariomod'                 => $idusuario,
                                        ];
                                    } else {
                                        $input_actualiza = [
                                            'estado'                        => '3', //poner otro estado para que no salga en las consultas
                                            'valor_contable'                => $nuevo_saldof,
                                            'ip_modificacion'               => $ip_cliente,
                                            'id_usuariomod'                 => $idusuario,
                                        ];
                                    }
                                    $consulta_compra->update($input_actualiza);
                                }
                                //Contable::pagofactura($consulta_compra->id,$id_comprobante,'EG');
                            }
                        }
                    }
                }
            }
            /*$asiento = Ct_Asientos_Cabecera::leftjoin('ct_asientos_detalle as d','ct_asientos_cabecera.id','d.id_asiento_cabecera')
            ->where('ct_asientos_cabecera.id', $id_asiento_cabecera)
            ->get();
            echo '<pre>';
            print_r($asiento);
            DB::rollback();
            exit;*/
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado Correctamente', 'id_comprobante' => $id_comprobante, 'id_asiento' => $id_asiento_cabecera];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['status' => 'error', 'msj' =>  $e->getMessage(), 'linea' => $e->getLine()];
        }

        return ['status' => 'success', 'id_comprobante' => $id_comprobante];
    }
    public function egreso_anulado_store(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nu = 1;

        $id_empresa = $request->session()->get('id_empresa');
        $objeto_validar = new Validate_Decimals();
        $aux_numero_factura = "";
        $numero_factura = "";

        $contador_ctv = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->get()->count();
        if ($contador_ctv != null) {
            $max_id = DB::table('ct_comprobante_secuencia')->where('tipo', 2)->where('empresa', $id_empresa)->latest('id')->first();
            if (is_null($max_id)) { //si no tiene secuencia la crea
                $nu = 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                DB::table('ct_comprobante_secuencia')->insert([
                    'tipo' => 2,
                    'empresa' => $id_empresa,
                    'secuencia' => $numero_factura,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario
                ]);
            } else { //si hay secuencia busca la ultima 
                $max_id = intval($max_id->secuencia);
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
                DB::table('ct_comprobante_secuencia')->where('tipo', 2)->where('empresa', $id_empresa)->update([
                    'secuencia'             => $numero_factura,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ]);
            }
        } else {
            $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            DB::table('ct_comprobante_secuencia')->insert([
                'tipo'                  => 2,
                'empresa'               => $id_empresa,
                'secuencia'             => $numero_factura,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario
            ]);
        }

        //$nuevo_saldof = $objeto_validar->set_round($request['total_favor']);


        $input_comprobante = [
            'descripcion'     => $request['aaa'] . ' REF: ' . $numero_factura,
            'estado'          => 1,
            'beneficiario'    => $request['nombre_proveedor'],
            'tipo'            => '1',
            'id_asiento_cabecera' => null,
            'id_secuencia'    => $numero_factura,
            'id_pago'         => '1',
            'check'           => $request['verificar_cheque'],
            'fecha_cheque'    => $request['fecha_cheque'],
            'id_caja_banco'   => $request['banco'],
            'no_cheque'       => $request['numero_cheque'],
            'fecha_comprobante' => $request['fecha_hoy'],
            'secuencia'       => $numero_factura,
            'id_empresa'      => $id_empresa,
            'id_proveedor'    => $request->id_proveedor,
            'valor_pago'      => $request->valor_cheque,
            'valor'           => $request->valor_cheque,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'anulado_tipo'    => 1,
            'fecha_entregado' => $request['verificar_cheque'] == 1 ? date('Y-m-d') : NULL
        ];
        $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
        // Ct_Detalle_Comprobante_Egreso::create([
        //     'id_comprobante'                 => $id_comprobante,
        //     'id_secuencia'                   => $numero_factura,
        //     'saldo_base'                     => $request->valor_cheque,
        //     'abono'                          => $request['total_favor'],
        //     'estado'                         => '1',
        //     'ip_creacion'                    => $ip_cliente,
        //     'ip_modificacion'                => $ip_cliente,
        //     'id_usuariocrea'                 => $idusuario,
        //     'id_usuariomod'                  => $idusuario,
        // ]);
        return ['id_egreso' => $id_comprobante, 'secuencia' => $numero_factura];
    }
    public function egreso_anulado(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();

        return view('contable/comp_egreso/create_anulado', ['divisas' => $divisas, 'sucursales' => $sucursales, 'proveedor' => $proveedores, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago]);
    }

    public function buscar_codigo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_factura    = $request['id_factura'];
        $tipo = 1;
        $data      = null;
        if ($tipo == 1) {
            /*$productos= DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c','c.id','a.id_asiento_cabecera')
            ->join('ct_compras as co','co.id','c.id_ct_compras')
            ->where('c.fact_numero',$id_factura)
            ->select('a.fecha','a.descripcion','co.proveedor')->get();*/
            /*$consulta= DB::table('ct_asientos_cabecera')->where('fact_numero',$id_factura)->first();
            $productos= DB::table('ct_asientos_detalle')->where('id_asiento_cabecera',$consulta->id)->get();*/
            $productos = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.secuencia_f', $id_factura)
                ->where('c.id_empresa', $id_empresa)
                ->where('c.estado', '1')
                ->select(
                    'co.proveedor',
                    'p.razonsocial',
                    'p.direccion',
                    'a.id',
                    'a.descripcion',
                    'p.razonsocial',
                    'co.fecha',
                    'p.id_tipoproveedor',
                    'c.observacion',
                    'c.fecha_asiento',
                    '.c.valor',
                    'co.numero',
                    'co.tipo',
                    'p.id_porcentaje_iva',
                    'p.id_porcentaje_ft',
                    'co.id',
                    'c.fact_numero',
                    'co.autorizacion',
                    'co.subtotal',
                    'co.iva_total'
                )

                ->get();

            $deudas = DB::table('ct_asientos_cabecera as c')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.proveedor', $productos[0]->proveedor)
                ->where('c.estado', '1')
                ->where('c.id_empresa', $id_empresa)
                ->select('c.valor', 'p.id_tipoproveedor', 'p.id_porcentaje_iva', 'co.tipo', 'p.id_porcentaje_ft', 'c.fact_numero', 'c.observacion', 'c.fecha_asiento', 'co.proveedor', 'c.valor_nuevo')
                ->orderby('co.fecha', 'asc')
                ->get();
        }

        if ($productos != '[]') {

            $data = [
                $productos[0]->proveedor,
                $productos[0]->id,
                $productos[0]->razonsocial,
                $productos[0]->direccion,
                $productos[0]->descripcion,
                $productos[0]->razonsocial,
                $productos,
                $productos[0]->id_tipoproveedor,
                $productos[0]->observacion,
                $productos[0]->fecha_asiento,
                $productos[0]->valor,
                $productos[0]->numero,
                $productos[0]->id_porcentaje_iva,
                $productos[0]->id_porcentaje_ft,
                $productos[0]->id,
                $productos[0]->fact_numero,
                $deudas,
                $productos[0]->autorizacion,
                $productos[0]->subtotal,
                $productos[0]->iva_total,
                $productos[0]->tipo
            ];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function buscarproveedor(Request $request)
    {
        $id_proveedor = $request['proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $data = 0;
        $tipo = 1;
        $facturas = '[]';
        $deudas = null;
        if ($tipo == 1) {
            $facturas = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.proveedor', $id_proveedor)
                ->where('c.id_empresa', $id_empresa)
                ->where('co.estado', '>', '0')
                ->where('co.valor_contable', '>', '0')
                ->select('co.valor_contable', 'co.tipo', 'co.secuencia_f', 'c.observacion', 'a.id', 'c.fecha_asiento', 'co.id')
                ->get();
            $deudas = DB::table('ct_asientos_cabecera as c')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->where('co.proveedor', $id_proveedor)
                ->where('c.id_empresa', $id_empresa)
                ->where('co.estado', '>', '0')
                ->where('co.valor_contable', '>', '0')
                ->select('co.valor_contable', 'co.secuencia_f', 'co.tipo', 'c.observacion', 'co.f_caducidad as fecha_asiento', 'co.numero', 'co.proveedor', 'c.valor as valor_nuevo', 'co.id')
                ->orderby('c.id', 'desc')
                ->get();
        }
        $retenciones = array();

        if ($facturas != '[]') {
            foreach ($deudas as $d) {
                $retencion  = Ct_Retenciones::where('estado', 1)->where('id_compra', $d->id)->first();
                if (!is_null($retencion)) {
                    array_push($retenciones, "Si");
                } else {
                    array_push($retenciones, "No");
                }
            }

            $data = [$facturas[0]->valor_contable, $facturas[0]->secuencia_f, $facturas[0]->observacion, $facturas[0]->id, $facturas[0]->fecha_asiento, $deudas, $facturas[0]->tipo, $facturas[0]->id, $retenciones];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }

    public function buscarAntAnticipos(Request $request)
    {
        //  dd("Holisss");
        $id_empresa = $request->session()->get('id_empresa');
        $sql = Ct_Detalle_Comprobante_Egreso::join('ct_comprobante_egreso as egr', 'egr.id', 'ct_detalle_comprobante_egreso.id_comprobante')
            ->where('egr.estado', '<>', '0')
            ->where('egr.valor_pago', '>', 0)
            ->where('id_empresa', $id_empresa)
            ->where('tipo', '2')
            ->where('egr.id_proveedor', $request->id_proveedor)
            ->get();

        $html = "";
        $status = "error";
        foreach ($sql as $value) {


            $html .= "<tr>
                        <td style='text-align:center;'>{$value->fecha_comprobante}</td>
                        <td style='text-align:center;'>ACR-EG</td>
                        <td style='text-align:center;'>{$value->id_secuencia}</td>
                        <td style='text-align:center;'> <input readonly style='color: #F3785A; background:none;' class='form-control' type='text' value='{$value->descripcion}'></td>
                        <td style='text-align:center;'>$</td>
                        <td style='text-align:center;'>{$value->valor_pago}</td>
                        <td style='text-align:center;'>{$value->valor}</td>
                    </tr>";
            $status = "success";
        }
        return ['html' => $html, "status" => $status];
    }




    public function pdfcomprobante($id, Request $request)
    {
        //dd('entra');
        $id_empresa = session('id_empresa');
        //dd($id_empresa);
        $comp_egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id', $id)->first();
        //dd($comp_egreso);
        $empresa = Empresa::where('id', $id_empresa)->first();
        //dd($empresa);
        $letras = new Numeros_Letras();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_egreso->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $compras = Ct_compras::where('secuencia_f', $comp_egreso->id_secuencia)->first();
        /*$asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')
            ->where('id_asiento_cabecera', $asiento_cabecera->id)
            ->get();*/
        $asiento_detalle = Ct_Asientos_Detalle::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->join('plan_cuentas_empresa as pe', function ($q) {
                $q->on('p.id', '=', 'pe.id_plan')
                    ->where('pe.id_empresa', '=', session('id_empresa'));
            })
            ->where('ct_asientos_detalle.estado', '1')
            ->where('ct_asientos_detalle.id_asiento_cabecera', $asiento_cabecera->id)
            ->get([
                'ct_asientos_detalle.id',
                'ct_asientos_detalle.id_asiento_cabecera',
                'ct_asientos_detalle.id_plan_cuenta',
                'p.nombre as descripcion',
                'ct_asientos_detalle.fecha',
                'ct_asientos_detalle.debe',
                'ct_asientos_detalle.haber',
                'ct_asientos_detalle.estado',
                'ct_asientos_detalle.id_usuariocrea',
                'ct_asientos_detalle.id_usuariomod',
                'ct_asientos_detalle.ip_creacion',
                'ct_asientos_detalle.ip_modificacion',
                'ct_asientos_detalle.created_at',
                'ct_asientos_detalle.updated_at',
                'ct_asientos_detalle.establecimiento',
            ]);
        //dd($asiento_detalle);
        if ($comp_egreso != '[]') {
            // if (($comp_egreso->tipo) != 1) {
            //     $vistaurl = "contable.comp_egreso_varios.pdf_comprobante_egreso_varios";
            //     $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
            //     $pdf      = \App::make('dompdf.wrapper');
            //     $pdf->loadHTML($view);
            //     $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            //     $pdf->setPaper('A4', 'portrait');
            //     return $pdf->stream('resultado-' . $id . '.pdf');
            // } else {
            $vistaurl = "contable.comp_egreso.pdf_comprobante_egreso";
            $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'compras'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('resultado-' . $id . '.pdf');
            //}
        }
    }
    public function egresosv(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->get();
        $comp_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
        return view('contable/comp_egreso_varios/index', ['comp_egreso' => $comp_egreso, 'empresa' => $empresa, 'cuentas' => $cuentas]);
    }
    public function egresov_create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        return view('contable/comp_egreso_varios/create', ['min_date' => $min_date, 'divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'sucursales' => $sucursales, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas]);
    }
    function buscar_banco($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $caja_banco = Ct_Caja_Banco::find($id);
        if (!is_null($caja_banco)) {
            $clase_banco = $caja_banco->clase;
        } else {
            $clase_banco = "no encontrado";
        }
        return ['clase_banco' => $clase_banco];
    }
    public function egresosvedit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $comprobante_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->get();
        $detalle_comprobante = Ct_Detalle_Comprobante_Egreso_Varios::where('id_comprobante_varios', $comprobante_egreso->id)->get();
        //dd($comprobante_egreso);
        return view('contable/comp_egreso_varios/edit', ['cuentas' => $cuentas, 'divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago, 'detalle_egreso' => $detalle_comprobante, 'varios' => $comprobante_egreso]);
    }

    public function egresov_update_observacion($id, Request $request)
    {
        //dd("hola");
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $egreso = Ct_Comprobante_Egreso_Varios::find($id);
        $input_cabecera = [
            'observacion'       => $request['concepto'],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];
        $asiento_cabecera = Ct_Asientos_Cabecera::find($egreso->id_asiento_cabecera);
        $asiento_cabecera->update($input_cabecera);

        $egreso = Ct_Comprobante_Egreso_Varios::find($id);
        $input_comprobante = [
            'descripcion'     => $request['concepto'],
            'comentarios'     => $request['nota'],
            'nro_cheque'      => $request['numero_cheque'],
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_comprobante = $egreso->update($input_comprobante);

        return redirect()->route('egresosv.edit', ['id' => $id]);
    }

    public function egresov_update($id, Request $request)
    {
        $sucursal = $request['sucursal'];
        $id_empresa = $request->session()->get('id_empresa');
        $punto_emision = $request['punto_emision'];
        $sucursal = substr($punto_emision, 0, -4);
        $punto_emision = substr($punto_emision, 4);
        $contador_ctv = DB::table('ct_comprobante_egreso_varios')->where('id_empresa', $id_empresa)->get()->count();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $objeto_validar = new Validate_Decimals();
        $numero_factura = 0;
        $egreso = Ct_Comprobante_Egreso_Varios::find($id);
        //dd($egreso);
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $input_cabecera = [
            'observacion' => $request['concepto'],
            'fecha_asiento' => $request['fecha_hoy'],
            'fact_numero' => $numero_factura,
            'valor' => $objeto_validar->set_round($request['valor_cheque']),
            'estado' => '3',
            'ip_modificacion'               => $ip_cliente,
            'id_usuariomod'                 => $idusuario,
        ];
        $asiento_cabecera = Ct_Asientos_Cabecera::find($egreso->id_asiento_cabecera);
        $asiento_cabecera->update($input_cabecera);
        $banco = $request['banco'];
        $asiento_detalles = Ct_Asientos_Detalle::where('id_asiento_cabecera', $egreso->id_asiento_cabecera)->delete();
        $varios = Ct_Detalle_Comprobante_Egreso_Varios::where('id_comprobante_varios', $egreso->id)->delete();
        //dd($banco);
        // SE DESCUADRO EL BALANCE
        if (($banco) != null) {
            $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
            $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();

            $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();

            if ($request['haber'] > 0) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera'           => $egreso->id_asiento_cabecera,
                    'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                    'descripcion'                   => $consulta_db_cajab->nombre,
                    'fecha'                         => $request['fecha_hoy'],
                    'debe'                         => $nuevo_saldof,
                    'haber'                          => '0',
                    'estado'                        => '1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ]);
            } else {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera'           => $egreso->id_asiento_cabecera,
                    'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                    'descripcion'                   => $consulta_db_cajab->nombre,
                    'fecha'                         => $request['fecha_hoy'],
                    'haber'                         => $nuevo_saldof,
                    'debe'                          => '0',
                    'estado'                        => '1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ]);
            }
        }
        $input_comprobante = [
            'descripcion'     => $request['concepto'],
            'estado'          => '1',
            'id_asiento_cabecera' => $egreso->id_asiento_cabecera,
            'id_secuencia'    => 'null',
            'nota'            => $request['nota'],
            'fecha_comprobante' => $request['fecha_hoy'],
            'beneficiario'    => strtoupper($request['beneficiario']),
            'check'           => $request['verificar_cheque'],
            'girado'          => $request['giradoa'],
            'id_caja_banco'   => $request['banco'],
            'nro_cheque'      => $request['numero_cheque'],
            'valor'           => $objeto_validar->set_round($request['valor_cheque']),
            'fecha_cheque'    => $request['fecha_cheque'],
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_comprobante = $egreso->update($input_comprobante);


        for ($i = 0; $i <= $request['contador']; $i++) {
            $nuevo_saldof = $objeto_validar->set_round($request['debe' . $i]);
            if (!is_null($request['codigo' . $i])) {
                $desc_cuenta = Plan_Cuentas::where('id', $request['codigo' . $i])->first();
                if ($desc_cuenta != null) {
                    if ($request['visibilidad' . $i] == 1 || $request['visibilidad' . $i] == '1') {
                        if (!is_null($request['debe' . $i]) && $request['debe' . $i] > 0) {
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera'           => $egreso->id_asiento_cabecera,
                                'id_plan_cuenta'                => $request['codigo' . $i],
                                'descripcion'                   => $desc_cuenta->nombre,
                                'fecha'                         => $request['fecha_hoy'],
                                'debe'                          => $nuevo_saldof,
                                'haber'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        } elseif (!is_null($request['haber' . $i]) && $request['haber' . $i] > 0) {
                            $nuevo_saldof = $objeto_validar->set_round($request['haber' . $i]);

                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera'           => $egreso->id_asiento_cabecera,
                                'id_plan_cuenta'                => $request['codigo' . $i],
                                'descripcion'                   => $desc_cuenta->nombre,
                                'fecha'                         => $request['fecha_hoy'],
                                'haber'                         => $nuevo_saldof,
                                'debe'                          => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        }
                    }
                }
            }
            if (!is_null($request['debe' . $i]) && $request['debe' . $i] > 0) {
                $cons = Plan_Cuentas::find($request['codigo' . $i]);
                Ct_Detalle_Comprobante_Egreso_Varios::create([
                    'id_comprobante_varios'          => $id,
                    'codigo'                         => $request['codigo' . $i],
                    'cuenta'                         => $cons->nombre,
                    'descripcion'                    => $request['observacion'],
                    'debe'                           => $request['debe' . $i],
                    'id_secuencia'                   => $numero_factura,
                    'estado'                         => '1',
                    'ip_creacion'                    => $ip_cliente,
                    'ip_modificacion'                => $ip_cliente,
                    'id_usuariocrea'                 => $idusuario,
                    'id_usuariomod'                  => $idusuario,
                ]);
            }
            if (!is_null($request['haber' . $i]) && $request['haber' . $i] > 0) {
                $cons = Plan_Cuentas::find($request['codigo' . $i]);
                Ct_Detalle_Comprobante_Egreso_Varios::create([
                    'id_comprobante_varios'          => $id,
                    'codigo'                         => $request['codigo' . $i],
                    'cuenta'                         => $cons->nombre,
                    'descripcion'                    => $request['observacion'],
                    'debe'                           => $request['haber' . $i],
                    'id_secuencia'                   => $numero_factura,
                    'estado'                         => '1',
                    'ip_creacion'                    => $ip_cliente,
                    'ip_modificacion'                => $ip_cliente,
                    'id_usuariocrea'                 => $idusuario,
                    'id_usuariomod'                  => $idusuario,
                ]);
            }
        }
        return redirect()->route('egresosv.edit', ['id' => $id]);
    }
    public function egresov_store(Request $request)
    {
        if (!is_null($request['contador'])) {
            $sucursal = $request['sucursal'];
            $id_empresa = $request->session()->get('id_empresa');
            $punto_emision = $request['punto_emision'];
            $sucursal = substr($punto_emision, 0, -4);
            $punto_emision = substr($punto_emision, 4);
            //$contador_ctv = DB::table('ct_comprobante_secuencia')->where('empresa', $id_empresa)->where('tipo', 2)->get()->count();
            $ip_cliente     = $_SERVER["REMOTE_ADDR"];
            $objeto_validar = new Validate_Decimals();
            $numero_factura = 0;
            $idusuario  = Auth::user()->id;
            $id_empresa = $request->session()->get('id_empresa');
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            DB::beginTransaction();
            try {
                $numero_factura = LogAsiento::getSecuencia(2);
                $input_cabecera = [
                    'observacion' => $request['concepto'],
                    'fecha_asiento' => $request['fecha_hoy'],
                    'fact_numero' => $numero_factura,
                    'valor' => $objeto_validar->set_round($request['valor_cheque']),
                    'id_empresa' => $id_empresa,
                    'estado' => '3',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'sucursal'                      => $request['sucursal'],
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $banco = $request['banco'];
                // SE DESCUADRO EL BALANCE
                if (($banco) != null) {
                    $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
                    $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                        'descripcion'                   => $consulta_db_cajab->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'haber'                         => $nuevo_saldof,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $input_comprobante = [
                    'descripcion'     => $request['concepto'],
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    //'nota'            => $request['nota'],
                    'comentarios'     => $request['nota'],
                    'fecha_comprobante' => $request['fecha_hoy'],
                    'beneficiario'    => strtoupper($request['beneficiario']),
                    // 'check'           => $request['verificar_cheque'],
                    'check'           => isset($request['cheque_entregado']) ? 1 : 0,
                    'girado'          => $request['giradoa'],
                    'id_caja_banco'   => $request['banco'],
                    'nro_cheque'      => $request['numero_cheque'],
                    'valor'           => $objeto_validar->set_round($request['valor_cheque']),
                    'fecha_cheque'    => $request['fecha_cheque'],
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'fecha_entregado'  => isset($request['cheque_entregado']) ? date("Y-m-d") : NULL,
                ];
                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);
                for ($i = 0; $i < count($request['debe']); $i++) {
                    $nuevo_saldof = $objeto_validar->set_round($request['debe'][$i]);
                    //dd("asads", $i,  $request['codigo'][$i], $request['debe' . $i]);
                    if (!is_null($request['codigo'][$i])) {
                        // dd("aqui4", $request['codigo' . $i] );
                        $desc_cuenta = Plan_Cuentas::where('id', $request['codigo'][$i])->first();
                        if ($desc_cuenta != null) {
                            if ($request['visibilidad'][$i] == 1 || $request['visibilidad'][$i] == '1') {
                                if (!is_null($request['debe'][$i]) && $request['debe'][$i] > 0) {
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $request['codigo'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $request['fecha_hoy'],
                                        'debe'                => $nuevo_saldof,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                                if (!is_null($request['haber'][$i]) && $request['haber'][$i] > 0) {
                                    $nuevo_saldof = $objeto_validar->set_round($request['haber'][$i]);
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $request['codigo'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $request['fecha_hoy'],
                                        'haber'               => $nuevo_saldof,
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                            }
                        }
                    }
                    if (!is_null($request['debe'][$i]) && $request['debe'][$i] > 0) {
                        $cons = Plan_Cuentas::find($request['codigo'][$i]);
                        Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $request['codigo'][$i],
                            'cuenta'                         => $cons->nombre,
                            'descripcion'                    => $request['observacion'],
                            'debe'                           => $request['debe'][$i],
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }
                    if (!is_null($request['haber'][$i]) && $request['haber'][$i] > 0) {
                        $cons = Plan_Cuentas::find($request['codigo'][$i]);
                        Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $request['codigo'][$i],
                            'cuenta'                         => $cons->nombre,
                            'descripcion'                    => $request['observacion'],
                            'debe'                           => $request['haber'][$i],
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }
                }
                DB::commit();
                return ["status" => "success", "msj" => "Guardado con exito", "id" => $id_comprobante];
                // return $id_comprobante;
            } catch (\Exception $e) {
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return ["status" => "error", "msj" => "No se pudo guardar", "exp" => $e->getMessage()];
                // return $e->getMessage();
            }
        } else {
            return ["status" => "error", "msj" => "No se pudo guardar"];
            // return 'error no guardó nada';
        }
    }
    public function pdfegresovarios($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $comp_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $total_str = $letras->convertir($asiento_cabecera->valor, "DOLARES", "CTVS");
        $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        $vistaurl = "contable.comp_egreso_varios.pdf_comprobante_egreso_varios";
        $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function anular_egreso($id, Request $request)
    {

        if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso::where('id', $id)->where('estado', '1')->first();
            $cab_asiento = Ct_Asientos_Cabecera::find($comp_ingreso->id_asiento_cabecera);
            //dd($cab_asiento);
            // $verifica_cierre = LogAsiento::verificar_cierre_anio($cab_asiento->fecha_asiento);
            // if ($verifica_cierre['status'] == 'error') {
            //     return $verifica_cierre;
            // }
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($cab_asiento['fecha_asiento']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $concepto = $request['concepto'];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {
                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12
                if (isset($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->where('id_empresa', $id_empresa)->first();
                        if (!is_null($consulta_venta)) {
                            $valor = $consulta_venta->valor_contable;
                            $suma = ($value->abono) + $valor;
                            $input_actualiza = [
                                'valor_contable'                => $suma,
                                'estado'                        => '1',
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                            //$a= Contable::recovery_price($value->id_compra,'C');    
                        }
                    }
                }
                $input = [
                    'nota' => strtoupper($concepto),
                    'estado' => '0',
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariomod'                 => $idusuario,
                ];
                //dd($comp_ingreso);
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::find($comp_ingreso->id_asiento_cabecera);
                if ($asiento != null) {
                    $asiento->estado = 1;
                    $asiento->id_usuariomod = $idusuario;
                    $asiento->save();
                    $detalles = $asiento->detalles;
                    $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                        'observacion'     => strtoupper($concepto),
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comp_ingreso->secuencia,
                        'valor'           => $asiento->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal'        => $asiento->sucursal,
                    ]);
                    foreach ($detalles as $value) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'debe'                => $value->haber,
                            'haber'               => $value->debe,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => $asiento->fecha_asiento,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ]);
                    }
                    LogAsiento::anulacion("AC-EG", $id_asiento, $asiento->id);
                    // Log_Contable::create([
                    //     'tipo'           => 'CE',
                    //     'valor_ant'      => $asiento->valor,
                    //     'valor'          => $asiento->valor,
                    //     'id_usuariocrea' => $idusuario,
                    //     'id_usuariomod'  => $idusuario,
                    //     'observacion'    => $asiento->concepto,
                    //     'id_ant'         => $asiento->id,
                    //     'id_referencia'  => $id_asiento,
                    // ]);
                }
                //dd("aqui");

                return ['status' => "success", 'msj' => "Anulado correctamente"];
            }
        } else {
            return ['status' => "error", 'msj' => "Hubo un error al anular"];
        }
    }
    public function anular_egreso_v($id, Request $request)
    {

        if (!is_null($id)) {

            $comp_ingreso = Ct_Comprobante_Egreso_Varios::where('estado', '1')->where('id', $id)->first();

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comp_ingreso['fecha_comprobante']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $concepto = $request['concepto'];
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {

                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12

                $input = [
                    'estado' => '0',
                    'nota'   => $concepto,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,

                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => $concepto,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'sucursal'        => $asiento->sucursal,
                ]);
                foreach ($detalles as $value) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }

                LogAsiento::anulacion("AC-EGV", $id_asiento, $asiento->id);

                return ['status' => "success", 'msj' => "Anulado correctamente"];
            }
        } else {
            return ['status' => "error", 'msj' => "Hubo un error al anular"];
        }
    }
    public function envioCorreo($id, Request $request)
    {
        $rol     = Ct_Comprobante_Egreso::find($id);
        $usuario = $rol->proveedor;
        $correo = $rol->proveedor->email;
        $mes = "";
        $roldate = date('m', strtotime($rol->fecha));
        if ($roldate == 1) {
            $mes = 'Enero';
        } elseif ($roldate == 2) {
            $mes = 'Febrero';
        } elseif ($roldate == 3) {
            $mes = 'Marzo';
        } elseif ($roldate == 4) {
            $mes = 'Abril';
        } elseif ($roldate == 5) {
            $mes = 'Mayo';
        } elseif ($roldate == 6) {
            $mes = 'Junio';
        } elseif ($roldate == 7) {
            $mes = 'Julio';
        } elseif ($roldate == 8) {
            $mes = 'Agosto';
        } elseif ($roldate == 9) {
            $mes = 'Septiembre';
        } elseif ($roldate == 10) {
            $mes = 'Octubre';
        } elseif ($roldate == 11) {
            $mes = 'Noviembre';
        } elseif ($roldate == 12) {
            $mes = 'Diciembre';
        }
        $rol_2 = $this->pdfcomprobante2($id);

        $asunto = "Comprobante de pago " . $rol->fecha_comprobante;
        $titulo = "Comprobante de pago " . $rol->fecha_comprobante  . '.pdf';
        Mail::send('mails.proveedores', ['usuario' => $usuario], function ($msj) use ($correo, $asunto, $rol_2, $titulo) {
            $msj->subject($asunto);
            $msj->from('rol@mdconsgroup.com', 'Sistema de Pago Proveedores SIAAM');
            $msj->to($correo);
            $msj->attachData($rol_2, $titulo, [
                'mime' => 'application/pdf',
            ]);
        });
        return 'ok';
    }
    public function pdfcomprobante2($id)
    {

        $comp_egreso = Ct_Comprobante_Egreso::find($id);
        $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_egreso->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $compras = Ct_compras::where('secuencia_f', $comp_egreso->id_secuencia)->first();
        $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        //dd($asiento_detalle);
        if ($comp_egreso != '[]') {
            if (($comp_egreso->tipo) != 1) {
                $vistaurl = "contable.comp_egreso_varios.pdf_comprobante_egreso_varios";
                $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
                $pdf      = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('resultado-' . $id . '.pdf');
            } else {
                $vistaurl = "contable.comp_egreso.pdf_comprobante_egreso";
                $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'compras'))->render();
                $pdf      = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('resultado-' . $id . '.pdf');
            }
        }
    }

    public function reporte_compegreso(Request $request, $id, $tipo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        //$fecha_proc = date('d/m/Y');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();

        $reporte_datos = [];
        //$rol_form_pag = [];
        //dd($tipo); 

        if ($tipo == '1') {
            $reporte_datos = DB::table('ct_comprobante_egreso as ctc')
                ->join('ct_detalle_comprobante_egreso as ctdc', 'ctdc.id_comprobante', 'ctc.id')
                ->join('ct_tipo_pago as ctp', 'ctc.id_pago', 'ctp.id')
                ->join('proveedor as p', 'ctc.id_proveedor', 'p.id')
                ->join('ct_configuracion_bancos as ctcb', 'ctcb.id', 'p.id_configuracion')
                ->groupBy('ctc.id_proveedor')
                ->where('ctc.id', $id)
                ->select('ctc.*', 'ctcb.submotivo_pago')
                ->get();
        }
        //dd($reporte_datos);


        Excel::create('ORDEN COMPROBANTE EGRESO', function ($excel) use ($empresa, $reporte_datos) {
            $excel->sheet('Comprobante Egreso', function ($sheet) use ($empresa, $reporte_datos) {


                //$fecha_d = date('Y/m/d');
                $i = 3;
                $j = 0;

                $sum_valor = 0;
                $cont_empl = 0;

                $sheet->mergeCells('A1:K1');

                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue('ORDEN COMPROBANTE EGRESO');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Forma Pag/Cob');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Banco');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Num.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Identificacion');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Doc.');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUC');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Telefono');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Referencia');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A2:K2', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                });
                // DETALLES
                $sheet->setColumnFormat(array(
                    'E' => '0.00',
                ));

                foreach ($reporte_datos as $value) {
                    //dd($value);
                    $txtcolor = '#000000';
                    $id_proveedor = Proveedor::find($value->id_proveedor);
                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        $tipo_pago = '';
                        if (is_null($value->id_pago)) {
                            $tipo_pago = "NO TIENE";
                        } else {
                            if ($value->tipo == '1') {
                                $tipo_pago = 'CU';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'EF';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'CH';
                            }
                        }

                        //dd($tipo_pago);
                        $cell->setValue($tipo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->submotivo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // dd($value->nombre);
                        $tip_cuent = '';

                        // Tipos de Cuenta 
                        //10: AHORRO 00:CORRIENTE 
                        // manipulate the cel
                        if ($id_proveedor->tipo_cuenta == 1) {

                            $cell->setValue('10');
                        } else {
                            $cell->setValue('00');
                        }
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        //dd($id_proveedor->cuenta);
                        $cell->setValue(' ' . $id_proveedor->cuenta);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->valor_pago);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        //C: CEDULA, R:RUC, P:PASAPORTE, X:NINGUNO
                        if (strlen($id_proveedor->identificacion) == 10) {
                            $cell->setValue('C');
                        } elseif (strlen($id_proveedor->identificacion) > 10) {
                            $cell->setValue('R');
                        } else {
                            $cell->setValue('P');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(' ' . $id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->beneficiario);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->telefono1);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->submotivo_pago == "30") {
                            $cell->setValue('PR');
                        } else {
                            $cell->setValue('RU');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sum_valor = $sum_valor + $value->valor_pago;

                    $i = $i + 1;
                    $cont_empl = $cont_empl + 1;
                }

                $j = $i + 1;
                $k = $j + 1;
                $l = $k + 1;
                $txtcolor = '#000000';

                //Subtotales
                $sheet->cell('A' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTALES');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Termina Sub Total
                //Total
                $sheet->cell('A' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('C' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('FORMA');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CANT.');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                $sheet->cells('D' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');

                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Fin TOtal
                //USD
                $sheet->cell('A' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('USD');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CU');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $k, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $k, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //FIN USD
                //TOTAL GENERAL
                $sheet->cell('A' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL GENERAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('DOLARES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $l, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $l, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                //FIN TOTAL GENERAL   


            });
        })->export('xlsx');
    }
    public function anticipo_proveedores(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $proveedores = Proveedor::all();
        $id_proveedor = $request['id_proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $informe = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('tipo', '2')->where('rt_asumida_estado', 0)->where('estado', '<>', '0')->where('valor_pago', '>', 0);
        if ($fecha_hasta != null && $fecha_desde != null) {
            $informe = $informe->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_hasta != null) {
            $informe = $informe->where('fecha_comprobante', '<=', $fecha_hasta);
        }
        if (!is_null($id_proveedor)) {
            $informe = $informe->where('id_proveedor', $id_proveedor);
        }
        $informe = $informe->orderBy('fecha_comprobante', 'DESC')->get();
        $empresa = Empresa::find($id_empresa);
        //dd($informe);
        return view('contable.anticipos.informe', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_proveedor' => $id_proveedor, 'proveedores' => $proveedores, 'informe' => $informe, 'empresa' => $empresa]);
    }



    public function comprobante_create_solicitud(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')
            ->where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->get();
        return view('contable/comp_egreso/create_solicitud', ['divisas' => $divisas, 'sucursales' => $sucursales, 'proveedor' => $proveedores, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago]);
    }

    public function comprobante_store_solicitud(Request $request)
    {
        //dd($request->all());
        $numero_factura = 0;
        $superavit = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia = 0;
        $id_proveedor = $request['id_proveedor'];
        $id_empresa = session('id_empresa');
        $cuentas = Proveedor::where('id', $id_proveedor)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $contador_ctv = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->get()->count();
        // $id_comprobante = array();
        $numero_factura = 0;
        $banco = (int) $request['banco'];
        $objeto_validar = new Validate_Decimals();
        $valor_asumida = 0;
        DB::beginTransaction();
        try {
            if ($superavit != 0) {
                // if ($request['contador'] != null) {
                // if ($contador_ctv == 0) { //si no tiene secuencia le creara una
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                //     DB::table('ct_comprobante_secuencia')->insert([
                //         'tipo' => 2,
                //         'empresa' => $id_empresa,
                //         'secuencia' => $numero_factura,
                //         'ip_creacion'           => $ip_cliente,
                //         'ip_modificacion'       => $ip_cliente,
                //         'id_usuariocrea'        => $idusuario,
                //         'id_usuariomod'         => $idusuario
                //     ]);
                // } else { //si tiene secuencia 
                //     /**Cambios**/
                //     $max_id = DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->latest('id')->first();
                //     if (is_null($max_id)) {
                $nu = 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                //         DB::table('ct_comprobante_secuencia')->insert([
                //             'tipo' => 1,
                //             'empresa' => $id_empresa,
                //             'secuencia' => $numero_factura,
                //             'ip_creacion'           => $ip_cliente,
                //             'ip_modificacion'       => $ip_cliente,
                //             'id_usuariocrea'        => $idusuario,
                //             'id_usuariomod'         => $idusuario
                //         ]);
                //     } else {
                //         $max_id = intval($max_id->secuencia);
                //         if (strlen($max_id) < 10) {
                //             $nu = $max_id + 1;
                //             $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                //         }
                //         DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->update([
                //             'secuencia'             => $numero_factura,
                //             'ip_creacion'           => $ip_cliente,
                //             'ip_modificacion'       => $ip_cliente,
                //             'id_usuariocrea'        => $idusuario,
                //             'id_usuariomod'         => $idusuario,
                //         ]);
                //     }
                // }
                $numero_factura =  LogAsiento::getSecuencia(1, 1);
                if (!is_null($request['total_favor'])) {
                    $numero_factura = $this->getSecuencia(1);
                    $nuevo_saldof = $objeto_validar->set_round($request['total_favor']);
                    // $input_cabecera = [
                    //     'observacion'                   => $request['aaa'],
                    //     'fecha_asiento'                 => $request['fecha_hoy'],
                    //     'fact_numero'                   => $numero_factura,
                    //     'valor'                         => $request['total_favor'],
                    //     'id_empresa'                    => $id_empresa,
                    //     'estado'                        => '1',
                    //     'ip_creacion'                   => $ip_cliente,
                    //     'ip_modificacion'               => $ip_cliente,
                    //     'id_usuariocrea'                => $idusuario,
                    //     'id_usuariomod'                 => $idusuario,
                    //     'sucursal'                      => $request['sucursal'],
                    //     //'estado_manual'                 => 2,
                    // ];
                    // $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    // if (($banco) != null) {
                    //     $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                    //     Ct_Asientos_Detalle::create([
                    //         'id_asiento_cabecera'           => $id_asiento_cabecera,
                    //         'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                    //         'descripcion'                   => $consulta_db_cajab->nombre,
                    //         'fecha'                         => $request['fecha_hoy'],
                    //         'haber'                         => $nuevo_saldof,
                    //         'debe'                          => '0',
                    //         'estado'                        => '1',
                    //         'id_usuariocrea'                => $idusuario,
                    //         'id_usuariomod'                 => $idusuario,
                    //         'ip_creacion'                   => $ip_cliente,
                    //         'ip_modificacion'               => $ip_cliente,
                    //     ]);
                    // }
                    $saldos_ret = 0;
                    $rt_asumida_valor = 0;
                    $rt_estado = 0;
                    // if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0) {
                    //     $saldos_ret = $nuevo_saldof - $request->rt_asumidas;
                    // } else {
                    //     $saldos_ret = $nuevo_saldof;
                    // }
                    // if ($id_proveedor != null) {
                    //     $globales = Ct_Globales::where('id_empresa', $id_empresa)->where('id_modulo', 18)->first();
                    //     $desc_cuenta = Plan_Cuentas::where('id', $globales->debe)->first();
                    //     Ct_Asientos_Detalle::create([
                    //         // 'id_asiento_cabecera'           => $id_asiento_cabecera,
                    //         //'id_plan_cuenta'                => '1.01.04.03',
                    //         'id_plan_cuenta'                => $desc_cuenta->id,
                    //         'descripcion'                   => $desc_cuenta->nombre,
                    //         'fecha'                         => $request['fecha_hoy'],
                    //         'debe'                          => $saldos_ret,
                    //         'haber'                         => '0',
                    //         'estado'                        => '1',
                    //         'id_usuariocrea'                => $idusuario,
                    //         'id_usuariomod'                 => $idusuario,
                    //         'ip_creacion'                   => $ip_cliente,
                    //         'ip_modificacion'               => $ip_cliente,
                    //     ]);
                    // }
                    // if ($request->rt_asumidas != null  || $request->rt_asumidas != "") {
                    //     if ($request->rt_asumidas > 0) {
                    //         $rt_estado = 1;
                    //         $rt_asumida_valor = $request->rt_asumidas;
                    //         $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();
                    //         if ($rt_cuenta == '') {
                    //         }
                    //         $arrayAsiento = [
                    //             // 'id_asiento_cabecera'           => $id_asiento_cabecera,
                    //             'id_plan_cuenta'                => $rt_cuenta->id,
                    //             'descripcion'                   => $rt_cuenta->nombre,
                    //             'fecha'                         => $request['fecha_hoy'],
                    //             'debe'                          => $request->rt_asumidas,
                    //             'haber'                         => '0',
                    //             'estado'                        => '1',
                    //             'id_usuariocrea'                => $idusuario,
                    //             'id_usuariomod'                 => $idusuario,
                    //             'ip_creacion'                   => $ip_cliente,
                    //             'ip_modificacion'               => $ip_cliente,
                    //         ];

                    //         Ct_Asientos_Detalle::insert($arrayAsiento);
                    //     }
                    // }

                    $aux_numero_factura = $numero_factura;
                    //dd($numero_factura);
                    $input_comprobante = [
                        'descripcion'     => $request['aaa'] . ' REF: ' . $aux_numero_factura,
                        'estado'          => '1',
                        'beneficiario'    => $request['nombre_proveedor'],
                        'tipo'            => '2',
                        // 'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => $aux_numero_factura,
                        'id_pago'         => $request["formas_pago"],
                        'check'           => $request['verificar_cheque'],
                        'fecha_cheque'    => $request['fecha_cheque'],
                        'aprobacion'    => $request['aprobacion_directorio'],
                        'id_caja_banco'   => $request['banco'],
                        'sucursal'                      => $request['sucursal'],
                        'no_cheque'       => $request['numero_cheque'],
                        'fecha_revision' => $request['fecha_revision'],
                        'fecha_comprobante' => $request['fecha_hoy'],
                        'secuencia'       => $aux_numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_proveedor'    => $id_proveedor,
                        'valor_pago'      => $nuevo_saldof,
                        'valor'           => $nuevo_saldof,
                        'comentarios'     => $request['nota'],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'rt_asumida_estado' => $rt_estado,
                        'rt_asumida_valor' => $rt_asumida_valor,
                        'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Solicitud::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Solicitud::create([
                        'id_comprobante'                 => $id_comprobante,
                        'id_secuencia'                   => $aux_numero_factura,
                        'saldo_base'                     => $request['total_favor'],
                        'abono'                          => $request['total_favor'],
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                    //}
                }
                //  }
            } else {
                if ($request['contador'] != null) {
                    $rt_asimida_creado = 0;

                    $numero_factura = LogAsiento::getSecuencia(1, 1);
                    $aux_numero_factura = $numero_factura;

                    // $input_cabecera = [
                    //     'observacion'                   => $request['aaa'],
                    //     'fecha_asiento'                 => $request['fecha_hoy'],
                    //     'fact_numero'                   => $numero_factura,
                    //     'valor'                         => $request['valor_cheque'],
                    //     'id_empresa'                    => $id_empresa,
                    //     'estado'                        => '1',
                    //     'ip_creacion'                   => $ip_cliente,
                    //     'ip_modificacion'               => $ip_cliente,
                    //     'id_usuariocrea'                => $idusuario,
                    //     'id_usuariomod'                 => $idusuario,
                    //     'sucursal'                      => $request['sucursal'],
                    //     //'estado_manual'                 => 2,
                    // ];
                    // $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    // if (($banco) != null) {
                    //     $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
                    //     $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                    //     $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
                    //     Ct_Asientos_Detalle::create([
                    //         // 'id_asiento_cabecera'           => $id_asiento_cabecera,
                    //         'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                    //         'descripcion'                   => $consulta_db_cajab->nombre,
                    //         'fecha'                         => $request['fecha_hoy'],
                    //         'haber'                         => $nuevo_saldof,
                    //         'debe'                          => '0',
                    //         'estado'                        => '1',
                    //         'id_usuariocrea'                => $idusuario,
                    //         'id_usuariomod'                 => $idusuario,
                    //         'ip_creacion'                   => $ip_cliente,
                    //         'ip_modificacion'               => $ip_cliente,
                    //     ]);
                    // }
                    $input_comprobante = [
                        'descripcion'     => $request['aaa'],
                        'estado'          => '1',
                        'beneficiario'    => $request['nombre_proveedor'],
                        'fecha_cheque'    => $request['fecha_cheque'],
                        // 'id_asiento_cabecera' => $id_asiento_cabecera,
                        'check'           => $request['verificar_cheque'],
                        'id_secuencia'    => $request['numero'],
                        'id_pago'         => $request['formas_pago'],
                        'id_caja_banco'   => $request['banco'],
                        'no_cheque'       => $request['numero_cheque'],
                        'fecha_revision' => $request['fecha_revision'],
                        'sucursal'                      => $request['sucursal'],
                        'aprobacion' => $request['aprobacion_directorio'],
                        'fecha_comprobante' => $request['fecha_hoy'],
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_proveedor'    => $id_proveedor,
                        'valor_pago'      => $request['valor_cheque'],
                        'valor'           => $request['valor_cheque'],
                        'comentarios'     => $request['nota'],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'rt_asumida_estado' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? 1 : 0,
                        'rt_asumida_valor' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? $request->rt_asumidas : 0,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Solicitud::insertGetId($input_comprobante);

                    for ($i = 0; $i <= $request['contador']; $i++) {
                        //$cuenta_prov = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EGRESOACREEDOR_PROV_LOC');//proveedores locales
                        //$id_plan_config = LogConfig::busqueda('1.01.04.03.01');
                        $id_plan_config = LogConfig::busqueda('2.01.03.01.01');
                        $desc_cuenta  = Plan_Cuentas::where('id', $id_plan_config)->first();
                        if (!is_null($request['abono' . $i])) {
                            if ($request['abono' . $i] > 0) {
                                // if ($id_proveedor != null) {
                                //     $nuevo_saldof = $objeto_validar->set_round($request['abono' . $i]);

                                //     if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0) {
                                //         if ($rt_asimida_creado == 0) {
                                //             // $nuevo_saldof = $nuevo_saldof - $request->rt_asumidas;
                                //             $nuevo_saldof = $request["valor_cheque"] - $request->rt_asumidas;
                                //             $rt_asimida_creado = 1;
                                //         }
                                //     }

                                //     //$desc_cuenta = Plan_Cuentas::where('id', EgresoAcreedorController::confgcuenta_abono())->first();
                                //     // Ct_Asientos_Detalle::create([
                                //     //     // 'id_asiento_cabecera'           => $id_asiento_cabecera,
                                //     //     'id_plan_cuenta'                => $desc_cuenta->id,
                                //     //     'descripcion'                   => $desc_cuenta->nombre,
                                //     //     'fecha'                         => $request['fecha_hoy'],
                                //     //     'debe'                          => $nuevo_saldof,
                                //     //     'haber'                         => '0',
                                //     //     'estado'                        => '1',
                                //     //     'id_usuariocrea'                => $idusuario,
                                //     //     'id_usuariomod'                 => $idusuario,
                                //     //     'ip_creacion'                   => $ip_cliente,
                                //     //     'ip_modificacion'               => $ip_cliente,
                                //     // ]);
                                // }
                                $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                                if (!is_null($consulta_compra)) {
                                    $valor_compra = $request['abono' . $i];
                                    if ($rt_asimida_creado == 1) {
                                        $valor_compra = $request->valor_cheque;
                                    }
                                    Ct_Detalle_Comprobante_Egreso_Solicitud::create([
                                        'id_comprobante'                 => $id_comprobante,
                                        'observacion'                    => $request['aaa'],
                                        'id_compra'                      => $consulta_compra->id,
                                        'id_secuencia'                   => $request['numero' . $i],
                                        'saldo_base'                     => $request['saldo' . $i],
                                        // 'abono'                          => $request['abono' . $i],
                                        'abono'                          => $valor_compra,
                                        'estado'                         => '1',
                                        'ip_creacion'                    => $ip_cliente,
                                        'ip_modificacion'                => $ip_cliente,
                                        'id_usuariocrea'                 => $idusuario,
                                        'id_usuariomod'                  => $idusuario,
                                    ]);
                                }
                            }
                        }
                    }
                    $nuevosaldox = $request['total_favor'];
                    // if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0){
                    if ($rt_asimida_creado == 1) {
                        $valor_asumida = $request->rt_asumidas;
                        // $id_plan_config = 
                        $cuenta_rt_asumida = LogConfig::busqueda('5.2.02.16.17');
                        $rt_cuenta = Plan_Cuentas::find($cuenta_rt_asumida);
                        if (is_null($rt_cuenta)) {
                            DB::rollback();
                            return ['status' => 'error', 'msj' => 'No se encontro la cuenta para la Retencion Asumida'];
                        }
                        // Ct_Asientos_Detalle::create([
                        //     'id_asiento_cabecera'           => $id_asiento_cabecera,
                        //     'id_plan_cuenta'                => $rt_cuenta->id,
                        //     'descripcion'                   => $rt_cuenta->nombre,
                        //     'fecha'                         => $request['fecha_hoy'],
                        //     'debe'                          => $request->rt_asumidas,
                        //     'haber'                         => '0',
                        //     'estado'                        => '1',
                        //     'id_usuariocrea'                => $idusuario,
                        //     'id_usuariomod'                 => $idusuario,
                        //     'ip_creacion'                   => $ip_cliente,
                        //     'ip_modificacion'               => $ip_cliente,
                        // ]);
                        $rt_asimida_creado = 1;
                    }
                    //aqui
                    if ($request['comprobarx'] == '1' || $request['comprobarx'] == 1) {
                        // if ($request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0){
                        //     $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();
                        //     Ct_Asientos_Detalle::create([
                        //          'id_asiento_cabecera'           => $id_asiento_cabecera,
                        //         'id_plan_cuenta'                => $rt_cuenta->id,
                        //         'descripcion'                   => $rt_cuenta->nombre,
                        //         'fecha'                         => $request['fecha_hoy'],
                        //         'debe'                          => $request->rt_asumidas,
                        //         'haber'                         => '0',
                        //         'estado'                        => '1',
                        //         'id_usuariocrea'                => $idusuario,
                        //         'id_usuariomod'                 => $idusuario,
                        //         'ip_creacion'                   => $ip_cliente,
                        //         'ip_modificacion'               => $ip_cliente,
                        //     ]);
                        // }
                        // if ($rt_asimida_creado == 0) {
                        //     $globales = Ct_Globales::where('id_empresa', $id_empresa)->where('id_modulo', 18)->first();
                        //     Ct_Asientos_Detalle::create([
                        //         'id_asiento_cabecera'           => $id_asiento_cabecera,
                        //         //'id_plan_cuenta'                => '1.01.04.03', //cambiar a anticipo proveedores
                        //         'id_plan_cuenta'                => $globales->debe,
                        //         'descripcion'                   => 'Anticipo a Proveedores',
                        //         'fecha'                         => $request['fecha_hoy'],
                        //         'debe'                          => $request['total_favor'],
                        //         'haber'                         => '0',
                        //         'estado'                        => '1',
                        //         'id_usuariocrea'                => $idusuario,
                        //         'id_usuariomod'                 => $idusuario,
                        //         'ip_creacion'                   => $ip_cliente,
                        //         'ip_modificacion'               => $ip_cliente,
                        //     ]);
                        // }



                        /**Tipo 2 CAMBIO */
                        // $aux = DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->latest('id')->first();
                        // $aux_numero_factura = 0;
                        // if (is_null($aux)) {
                        //     $aux_nu = 1;
                        //     $aux_numero_factura = str_pad($aux_nu, 10, "0", STR_PAD_LEFT);
                        //     DB::table('ct_comprobante_secuencia')->insert([
                        //         'tipo'                  => 1,
                        //         'empresa'               => $id_empresa,
                        //         'secuencia'             => $aux_numero_factura,
                        //         'ip_creacion'           => $ip_cliente,
                        //         'ip_modificacion'       => $ip_cliente,
                        //         'id_usuariocrea'        => $idusuario,
                        //         'id_usuariomod'         => $idusuario
                        //     ]);
                        // } else {
                        //     $aux_max_id = intval($aux->secuencia);
                        //     if (strlen($aux_max_id) < 10) {
                        //         $aux_nu = $aux_max_id + 1;
                        //         $aux_numero_factura = str_pad($aux_nu, 10, "0", STR_PAD_LEFT);
                        //     }
                        //     DB::table('ct_comprobante_secuencia')->where('tipo', 1)->where('empresa', $id_empresa)->update([
                        //         'secuencia'             => $aux_numero_factura,
                        //         'ip_creacion'           => $ip_cliente,
                        //         'ip_modificacion'       => $ip_cliente,
                        //         'id_usuariocrea'        => $idusuario,
                        //         'id_usuariomod'         => $idusuario,
                        //     ]);
                        // }
                        if (!isset($request["ch_rt_asumidas"])) {
                            $aux_numero_factura = LogAsiento::getSecuencia(2, 1);
                            /**FINN DE CAMBIOS */
                            $input_comprobante2 = [
                                'descripcion'     => $request['aaa'] . ' REF: ' . $aux_numero_factura,
                                'estado'          => '1',
                                'beneficiario'    => $request['nombre_proveedor'],
                                'tipo'            => '2',
                                // 'id_asiento_cabecera' => $id_asiento_cabecera,
                                'id_secuencia'    => $aux_numero_factura,
                                'id_pago'         => '1',
                                'check'           => $request['verificar_cheque'],
                                'fecha_cheque'    => $request['fecha_cheque'],
                                'id_caja_banco'   => $request['banco'],
                                'sucursal'                      => $request['sucursal'],
                                'no_cheque'       => $request['numero_cheque'],
                                'fecha_revision' => $request['fecha_revision'],
                                'fecha_comprobante' => $request['fecha_hoy'],
                                'secuencia'       => $aux_numero_factura,
                                'id_empresa'      => $id_empresa,
                                'id_proveedor'    => $id_proveedor,
                                'valor_pago'      => $nuevosaldox,
                                'valor'           => $nuevosaldox,
                                'comentarios'     => $request['nota'],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                // 'rt_asumida_estado' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? 1 : 0,
                                // 'rt_asumida_valor' => $request->rt_asumidas != null || $request->rt_asumidas != "" &&  $request->rt_asumidas > 0 ? $request->rt_asumidas : 0,
                                'fecha_entregado'  => $request["verificar_cheque"] == 1 ? date("Y-m-d") : NULL
                            ];
                            $id_comprobante2 = Ct_Comprobante_Egreso_Solicitud::insertGetId($input_comprobante2);
                            Ct_Detalle_Comprobante_Egreso_Solicitud::create([
                                'id_comprobante'                 => $id_comprobante2,
                                'id_secuencia'                   => $aux_numero_factura,
                                'saldo_base'                     => $request['total_favor'],
                                'abono'                          => $request['total_favor'],
                                'estado'                         => '1',
                                'ip_creacion'                    => $ip_cliente,
                                'ip_modificacion'                => $ip_cliente,
                                'id_usuariocrea'                 => $idusuario,
                                'id_usuariomod'                  => $idusuario,
                            ]);
                        }
                    }
                    $consulta_compra = null;
                    $input_actualiza = null;

                    /*************************************
                     ****ACTUALIZO CUANDO ES COMPRA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE EGRESO***
                        /*************************************/
                    for ($i = 0; $i <= $request['contador']; $i++) {
                        if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                            $nuevo_saldo = 0;
                            //actualizar valor contable de cada tabla
                            $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->where('id_empresa', $id_empresa)->first();
                            if (!empty($consulta_compra) || $consulta_compra != '[]') {
                                if ($request['abono' . $i] > 0) {
                                    $new_valor = $request['abono' . $i];
                                    //805.35
                                    if ($valor_asumida > 0) {
                                        $new_valor = $request->valor_cheque - $valor_asumida;
                                    }
                                    if ($new_valor > ($consulta_compra->valor_contable)) {
                                        // $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                                        $nuevo_saldo = $new_valor - $consulta_compra->valor_contable;
                                    } else {
                                        // $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                                        $nuevo_saldo = $consulta_compra->valor_contable - $new_valor;
                                    }
                                    $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                                    $input_actualiza = null;
                                    if ($nuevo_saldof != 0) {
                                        $input_actualiza = [
                                            'estado'                        => '2', //poner otro estado para que no salga en las consultas
                                            'valor_contable'                => $nuevo_saldof,
                                            'ip_modificacion'               => $ip_cliente,
                                            'id_usuariomod'                 => $idusuario,
                                        ];
                                    } else {
                                        $input_actualiza = [
                                            'estado'                        => '3', //poner otro estado para que no salga en las consultas
                                            'valor_contable'                => $nuevo_saldof,
                                            'ip_modificacion'               => $ip_cliente,
                                            'id_usuariomod'                 => $idusuario,
                                        ];
                                    }
                                    //$consulta_compra->update($input_actualiza);
                                }
                                //Contable::pagofactura($consulta_compra->id,$id_comprobante,'EG');
                            }
                        }
                    }
                }
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado Correctamente', 'id_comprobante' => $id_comprobante];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['status' => 'error', 'msj' =>  $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public function pdfcomprobante_solicitud($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $comp_egreso = Ct_Comprobante_Egreso_Solicitud::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $contador = User::where('id', $empresa->id_contador)->first();
        $letras = new Numeros_Letras2();
        $total_str = $letras->convertir(number_format($comp_egreso->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $compras = Ct_compras::where('secuencia_f', $comp_egreso->id_secuencia)->first();
        if (!empty($comp_egreso)) {
            $vistaurl = "contable.comp_egreso.pdf_comprobante_egreso_solicitud";
            $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'compras', 'contador'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('resultado-' . $id . '.pdf');
        }
    }
    public function comprobante_index_solicitud(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $empresas = Empresa::all();
        foreach ($empresas as $emp) {
            $maestra = Ct_Globales::where('id_empresa', '0992704152001')->where('id_modulo', 18)->first();
            $globales = Ct_Globales::where('id_empresa', $emp->id)->where('id_modulo', 18)->first();
            if (is_null($globales)) {
                $data = $maestra['attributes'];
                unset($data['id']);
                //dd($emp->id,$data);
                $data['id_empresa'] = $emp->id;
                Ct_Globales::create($data);
            }
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $facturas_compras = Ct_compras::where('estado', '>', '0')->get();
        $comp_egreso = Ct_Comprobante_Egreso_Solicitud::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($comp_egreso);
        return view('contable/comp_egreso/index_solicitud', ['empresa' => $empresa, 'divisas' => $divisas, 'facturas_compras' => $facturas_compras, 'proveedor' => $proveedores, 'comp_egreso' => $comp_egreso]);
    }
    public function buscar_solicitud(Request $request)
    {
        //dd($request->all());

        if ($request->excel == 0) {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            $id_empresa = $request->session()->get('id_empresa');
            $armHtml = '';
            if ($request->proveedor != null) {
                $proveeBuscar = Proveedor::find($request->proveedor);
                $armHtml = $proveeBuscar->id . '-' . $proveeBuscar->nombrecomercial;
            }



            $proveedores = Proveedor::where('estado', '1')->get();
            $constraints = [
                'id'                  => $request['id'],
                'id_proveedor'        => $request['proveedor'],
                'secuencia'           => $request['secuencia'],
                'no_cheque'           => $request['cheque'],
                'descripcion'         => $request['descripcion'],
                'fecha_cheque'        => $request['fecha'],
                'id_asiento_cabecera' => $request['asiento_id'],
            ];
            //dd($constraints);
            $comp_egreso = $this->doSearchingQuery1($constraints, $id_empresa);
            $empresa = Empresa::where('id', $id_empresa)->first();
            return view('contable/comp_egreso/index', ['armHtml' => $armHtml, 'comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'proveedor' => $proveedores, 'empresa' => $empresa]);
        }
        if ($request->excel == 1) {
            self::excel_comprobante_egreso($request);
        }
    }
    public function comprobante_edit_solicitud($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd("hola");
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //dd($banco);
        $comprobante_egreso = Ct_Comprobante_Egreso_Solicitud::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante = Ct_Detalle_Comprobante_Egreso_Solicitud::where('id_comprobante', $comprobante_egreso->id)->get();
        //dd($comprobante_egreso);
        return view('contable/comp_egreso/edit_solicitud', ['divisas' => $divisas, 'sucursales' => $sucursales, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago, 'detalle_egreso' => $detalle_comprobante, 'comprobante_egreso' => $comprobante_egreso]);
    }
    public function anular_egreso_solicitud($id, Request $request)
    {
        if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso_Solicitud::where('id', $id)->where('estado', '1')->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $concepto = $request['concepto'];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {
                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12
                if (isset($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->where('id_empresa', $id_empresa)->first();

                        if (!is_null($consulta_venta)) {
                            $valor = $consulta_venta->valor_contable;
                            $suma = ($value->abono) + $valor;
                            $input_actualiza = [
                                'valor_contable'                => $suma,
                                'estado'                        => '1',
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                            //$a= Contable::recovery_price($value->id_compra,'C');    
                        }
                    }
                }
                $input = [
                    'nota' => strtoupper($concepto),
                    'estado' => '0',
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariomod'                 => $idusuario,
                ];
                //dd($comp_ingreso);
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::find($comp_ingreso->id_asiento_cabecera);
                if ($asiento != null) {
                    $asiento->estado = 1;
                    $asiento->id_usuariomod = $idusuario;
                    $asiento->save();
                    $detalles = $asiento->detalles;
                    $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                        'observacion'     => strtoupper($concepto),
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comp_ingreso->secuencia,
                        'valor'           => $asiento->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal'        => $asiento->sucursal,
                    ]);
                    foreach ($detalles as $value) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'debe'                => $value->haber,
                            'haber'               => $value->debe,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => $asiento->fecha_asiento,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ]);
                    }
                    LogAsiento::anulacion("AC-EG", $id_asiento, $asiento->id);
                    // Log_Contable::create([
                    //     'tipo'           => 'CE',
                    //     'valor_ant'      => $asiento->valor,
                    //     'valor'          => $asiento->valor,
                    //     'id_usuariocrea' => $idusuario,
                    //     'id_usuariomod'  => $idusuario,
                    //     'observacion'    => $asiento->concepto,
                    //     'id_ant'         => $asiento->id,
                    //     'id_referencia'  => $id_asiento,
                    // ]);
                }
                //dd("aqui");

                return ['status' => 'success', 'msj' => 'Guardado Correctamente'];
            }
        } else {
            return 'error';
        }
    }

    public function comprobante_index_anticipo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $empresas = Empresa::all();
        foreach ($empresas as $emp) {
            $maestra = Ct_Globales::where('id_empresa', '0992704152001')->where('id_modulo', 18)->first();
            $globales = Ct_Globales::where('id_empresa', $emp->id)->where('id_modulo', 18)->first();
            if (is_null($globales)) {
                $data = $maestra['attributes'];
                unset($data['id']);
                //dd($emp->id,$data);
                $data['id_empresa'] = $emp->id;
                Ct_Globales::create($data);
            }
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $facturas_compras = Ct_compras::where('estado', '>', '0')->get();
        $comp_egreso = Ct_Comprobante_Egreso::where('tipo', '2')->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($comp_egreso);
        return view('contable/comp_egreso/anticipo', ['empresa' => $empresa, 'divisas' => $divisas, 'facturas_compras' => $facturas_compras, 'proveedor' => $proveedores, 'comp_egreso' => $comp_egreso]);
    }


    public function buscar_anticipo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $armHtml = '';
        if ($request->proveedor != null) {
            $proveeBuscar = Proveedor::find($request->proveedor);
            if ($proveeBuscar) {
                $armHtml = $proveeBuscar->id . '-' . $proveeBuscar->nombrecomercial;
            }
        }

        // // Si se requiere exportar a Excel
        // if ($request->excel == 1) {
        //     return $this->excel_comprobante_egreso($request);
        // }

        $constraints = [
            'id'                  => $request['id'],
            'id_proveedor'        => $request['proveedor'],
            'secuencia'           => $request['secuencia'],
            'no_cheque'           => $request['cheque'],
            'descripcion'         => $request['descripcion'],
            'fecha_cheque'        => $request['fecha'],
            'id_asiento_cabecera' => $request['asiento_id'],
        ];
        $comp_egreso = Ct_Comprobante_Egreso::where('tipo', '2')->where('id_empresa', $id_empresa)
            ->where(function ($query) use ($constraints) {
                foreach ($constraints as $field => $value) {
                    if (!is_null($value)) {
                        if ($field == 'id' || $field == 'id_asiento_cabecera') {
                            $query->where($field, $value);
                        } else {
                            $query->where($field, 'like', '%' . $value . '%');
                        }
                    }
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/comp_egreso/anticipo', ['armHtml' => $armHtml, 'comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'proveedor' => $proveedores, 'empresa' => $empresa]);
    }



    public function excel_comprobante_egreso_anticipo(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proveedores = Proveedor::where('estado', '1')->get();
        $query = Ct_Comprobante_Egreso::query();
        if ($request->has('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
        if ($request->has('proveedor') && $request->proveedor !== null) {
            $query->where('id_proveedor', $request->proveedor);
        }
        if ($request->has('secuencia') && $request->secuencia !== null) {
            $query->where('secuencia', $request->secuencia);
        }
        if ($request->has('descripcion') && $request->descripcion !== null) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }
        if ($request->has('fecha') && $request->fecha !== null) {
            $query->where('fecha_cheque', $request->fecha);
        }
        if ($request->has('cheque') && $request->cheque !== null) {
            $query->where('nro_cheque', $request->cheque);
        }
        if ($request->has('asiento_id') && $request->asiento_id !== null) {
            $query->where('id_asiento_cabecera', $request->asiento_id);
        }
        $comp_egreso = $query->where('tipo', '2')->orderby('id', 'desc')->get();

        $titulos = array("Id", "Asiento", "Serie", "Fecha de Comprobante", "Fecha Cheque", "Nro_Cheque", "Proveedor", "Descripción", "Valor", "Creado por", "Anulado por", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('COMPROBANTE DE EGRESO ANTICIPO', function ($excel) use ($titulos, $posicion, $comp_egreso) {
            $excel->sheet('COMPROBANTE DE EGRESO ANTICIPO', function ($sheet) use ($titulos, $posicion, $comp_egreso) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE DE EGRESO ANTICIPO');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });
                $comienzo = 4;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $total = 0;
                foreach ($comp_egreso as $detalle) {


                    if ($detalle->valor_pago > 0) {
                        $valor = round($detalle->valor_pago, 2);
                        $total += $detalle->valor_pago;
                        $valor = round($detalle->valor_pago, 2);
                    } else if (isset($detalle->asiento_cabecera)) {
                        $valor = round($detalle->asiento_cabecera->valor, 2);
                        $total += $detalle->asiento_cabecera->valor;
                        $valor = round($detalle->asiento_cabecera->valor, 2);
                    }


                    $total_formateado = round($total, 2);


                    $estado = '';
                    if ($detalle->estado == '1') {
                        $estado = 'Activo';
                    } elseif ($detalle->estado == '0') {
                        $estado = 'Anulada';
                    }
                    $anulado_por = '';
                    if ($detalle->estado == '0') {
                        if (isset($detalle->usuariomod)) {
                            $anulado_por = $detalle->usuariomod->nombre1 . ' ' . $detalle->usuariomod->apellido1;
                        }
                    }
                    $Creado_por = '';

                    if (isset($detalle->usuario)) {
                        $Creado_por     =  $detalle->usuario->nombre1 . ' ' . $detalle->usuario->nombre2;
                    }

                    $proveedor = '';
                    if (isset($detalle->proveedor)) {
                        $proveedor = $detalle->proveedor->razonsocial;
                    }

                    $datos_excel = [];

                    $datos_excel = [$detalle->id, $detalle->id_asiento_cabecera, $detalle->secuencia, $detalle->fecha_comprobante, $detalle->fecha_cheque, $detalle->no_cheque, $proveedor, $detalle->descripcion, $valor, $Creado_por, $anulado_por, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 8) {
                                $cell->setAlignment('right');
                            } elseif ($i == 6) {
                                $cell->setAlignment('left');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;

                $sheet->cell('H' . $comienzo, function ($cell) {
                    $cell->setValue('VALOR TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                if (isset($total_formateado)) {
                    $sheet->cell('I' . $comienzo, function ($cell) use ($total_formateado) {
                        $cell->setValue($total_formateado);
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                    });
                }

                //dd($total_formateado);
                $sheet->setWidth('A', 10);
                $sheet->setWidth('B', 10);
                $sheet->setWidth('C', 12);
                $sheet->setWidth('D', 21);
                $sheet->setWidth('E', 14);
                $sheet->setWidth('F', 12);
                $sheet->setWidth('G', 60);
                $sheet->setWidth('H', 100);
                $sheet->setWidth('I', 15);
                $sheet->setWidth('j', 23);
                $sheet->setWidth('k', 23);
                $sheet->setWidth('L', 12);
            });
        })->export('xlsx');
    }
}
