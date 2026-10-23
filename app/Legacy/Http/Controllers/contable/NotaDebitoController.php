<?php

namespace Sis_medico\Http\Controllers\contable;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nota_Debito;
use Sis_medico\Nota_Debito_Detalle;
use Sis_medico\Plan_Cuentas;
use Excel;
use Sis_medico\Log_Contable;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Sucursales;
use Sis_medico\LogAsiento;

class NotaDebitoController extends Controller
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
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $principales = Nota_Debito::where('estado', '!=', null)->orderby('id', 'desc')->where('empresa', $id_empresa)->paginate(15);

        return view('contable/nota_debito/index', ['registros' => $principales, 'empresa' => $empresa]);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $constraints = [
            'id_asiento' => $request['buscar_asiento'],
            'fecha'      => $request['fecha'],
            'concepto'   => $request['concepto'],
            'id'         => $request['numero'],
        ];
        $registros = $this->doSearchingQuery($constraints, $id_empresa);
        $fecha     = $constraints['fecha'];
        $numero    = $constraints['id'];
        return view('contable/nota_debito/index', ['request' => $request, 'registros' => $registros, 'fecha' => $fecha, 'numero' => $numero, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Nota_Debito::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('empresa', $id_empresa)->orderby('id', 'desc')->paginate(15);
    }

    public function buscarasiento(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return $cuenta;
        //return view('contable/nota_debito/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        // por favor no tocar mas aqui
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        // $cuentas = plan_cuentas::where('estado', '2')->get();
        $cuentas = Plan_Cuentas_Empresa::where('estado', '2')->where("id_empresa", $id_empresa)->get();

        $bancos  = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $divisas = Ct_Divisas::where('estado', '1')->get();
        //validacion cierre año
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        //dd($min_date);
        return view('contable/nota_debito/create', ['cuentas' => $cuentas, 'bancos' => $bancos, 'divisas' => $divisas, 'empresa' => $empresa, 'min_date' => $min_date, 'sucursales' => $sucursales]);
    }

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        //dd($request->all());
        /*$verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_asiento);
        if ($verifica_cierre['status'] == 'error') {
            return ['status' => $verifica_cierre['status'], 'error' => $verifica_cierre['msj']];
        }*/
        //dd($request['fecha_asiento']);
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_asiento']));
        //dd($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'error' => $verifica_cierre->message];
        }

        try {

            $fecha_as       = $request['fecha_asiento'];
            $id_empresa     = $request->session()->get('id_empresa');
            $input_cabecera = [
                'observacion'     => $request['observacion'],
                'fecha_asiento'   => $fecha_as,
                'id_empresa'      => $id_empresa,
                'valor'           => $request['valor'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'sucursal'        => $request['sucursal'],
            ];
            //$id_asiento_cabecera=0;
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $nota_debito         = [
                'concepto'        => $request['observacion'],
                'fecha'           => $request['fecha_asiento'] . ' ' . date('H:i:s'),
                'valor'           => $request['valor'],
                'empresa'         => $id_empresa,
                'tipo'            => "BAN-ND",
                'id_asiento'      => $id_asiento_cabecera,
                'id_banco'        => $request['id_banco'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            //$id_nota = 0;
            $id_nota = Nota_Debito::insertGetId($nota_debito);

            $arr_total = [];
            for ($i = 0; $i < count($request->input("debe")); $i++) {
                if ($request->input("debe")[$i] != "" && $request->input("nombre")[$i] != "") {
                    $arr = [
                        'debe' => $request->input("debe")[$i],
                        'codigo'       => $request->input("nombre")[$i],
                        'nombre'       => $request->input("no_no")[$i],
                    ];
                    //  print_r($arr);
                    array_push($arr_total, $arr);
                }
            }
            //  echo '---';
            // print_r($arr_total);
            //  $arr_total = array_pop($arr_total);
            //    print_r($arr_total);

            foreach ($arr_total as $valor) {
                //foreach($request['debe'] as $valores){
                //foreach($request->input("debe") as $valores){

                //for ($i = 1; $i <= $request['contador']; $i++) {
                $nota_deb_detalle = [
                    'id_nota_debito'  => $id_nota,
                    'codigo'          => $valor['codigo'], //$request['id_plan' . $i],
                    'cuenta'          => $valor['nombre'], //$request['nombre' . $i],
                    'debe'            => $valor['debe'], //number_format($valor['debe'], 2),
                    'haber'           => number_format(0, 2),
                    'valor_base'      => $valor['debe'], //number_format($valor['debe'], 2),
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                Nota_Debito_Detalle::create($nota_deb_detalle);

                $as_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $valor['codigo'],
                    'descripcion'         => $valor['nombre'],
                    'fecha'               => $fecha_as,
                    'debe'                => $valor['debe'], //number_format($valor['debe'], 2),
                    'haber'               => '0.00',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                Ct_Asientos_Detalle::create($as_detalle);
            }
            $cuenta_banco = Ct_Caja_Banco::where('id', $request['id_banco'])->first();

            $plan_c      = Plan_Cuentas::where('id', $cuenta_banco['cuenta_mayor'])->first();
            $as_cuenta_g = [

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_c['id'],
                'descripcion'         => $plan_c['nombre'],
                'fecha'               => $fecha_as,
                'debe'                => '0.00',
                'haber'               => $request['valor'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ];
            Ct_Asientos_Detalle::create($as_cuenta_g);
            DB::commit();

            return ['status' => 'success', 'idasiento' => $id_asiento_cabecera, 'iddebito' => $id_nota];
        } catch (\Exception $e) {

            DB::rollBack();
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    public function imprimir($id)
    {
        $registro = Nota_Debito::findorfail($id);
        $detalle  = Nota_Debito_Detalle::where('id_nota_debito', $id)->get();

        //$fact_venta = Ct_Orden_Venta::findorfail($id);
        //$ct_for_pag = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();

        $vistaurl = "contable.nota_debito.pdf_nota";
        $view     = \View::make($vistaurl, compact('registro', 'detalle'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        //echo convertir(12312);
        return $pdf->stream('Nota de debito -' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));
    }

    public function imprimir_nuevo($id)
    {
        $registro = Nota_Debito::findorfail($id);
        $detalle  = Nota_Debito_Detalle::where('id_nota_debito', $id)->get();

        //$fact_venta = Ct_Orden_Venta::findorfail($id);
        //$ct_for_pag = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();

        $vistaurl = "contable.nota_debito.pdf_nota_debito";
        $view     = \View::make($vistaurl, compact('registro', 'detalle'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        //echo convertir(12312);
        return $pdf->stream('Nota de debito -' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));
    }


    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Nota_Debito::findorfail($id);
        $empresa  = Empresa::where('id', '0992704152001')->first();
        $banco    = Ct_Caja_banco::where('id', $registro['id_banco'])->first();
        $bancos   = Ct_Caja_Banco::where('estado', '1')->get();
        $divisas  = Ct_Divisas::where('estado', '1')->get();

        $detalle = Nota_Debito_Detalle::where('id_nota_debito', $registro['id'])->get();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($registro->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        return view('contable/nota_debito/edit', ['registro' => $registro, 'empresa' => $empresa, 'banco' => $banco, 'bancos' => $bancos, 'divisas' => $divisas, 'detalle' => $detalle, 'sucursal' => $sucursal]);
    }


    public function anular(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //Obtenemos la fecha de Hoy
        $registro = Nota_Debito::findorfail($id);
        $fechahoy   = $registro->fecha;
        $fechahoy = substr($fechahoy, 0, 10);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($fechahoy);
        try {
            /*$verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'error' => $verifica_cierre['msj']];
            }*/

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'error' => $verifica_cierre->message];
            }

            if($registro->estado != '0'){
                //$registro    = Nota_Debito::where('id', $id)->where('estado', '<>', 0)->first();
                if (!empty($registro)) {
                    $act_estado = [
                        'estado' => '0',
                    ];
                
                    Nota_Debito::where('id', $id)->update($act_estado);

                    $consulta_cabecera_ctventas = Ct_Asientos_Cabecera::where('estado', '1')
                        ->where('id', $registro['id_asiento'])->first();
                    $input_cabecera = [
                        'observacion'     => $request['observacion'],
                        'fecha_asiento'   => $consulta_cabecera_ctventas->fecha_asiento,
                        'id_empresa'      => $consulta_cabecera_ctventas->id_empresa,
                        'valor'           => $consulta_cabecera_ctventas->valor,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'sucursal'        => $consulta_cabecera_ctventas->sucursal,
                    ];

                    //INSERCION EN LA TABLA
                    $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                    $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_ctventas->id)->get();

                    if ($consulta_detalle != '[]') {

                        foreach ($consulta_detalle as $value) {
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $id_asiento_cab,
                                'id_plan_cuenta'      => $value->id_plan_cuenta,
                                'descripcion'         => 'ANULACION DE NOTA DE DEBITO',
                                'fecha'               => $consulta_cabecera_ctventas->fecha_asiento,
                                'haber'               => $value->debe,
                                'debe'                => $value->haber,
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                            ]);
                        }
                    }
                    Log_Contable::create([
                        'tipo'           => 'ND',
                        'valor_ant'      => $consulta_cabecera_ctventas->valor,
                        'valor'          => $consulta_cabecera_ctventas->valor,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod'  => $idusuario,
                        'observacion'    => $consulta_cabecera_ctventas->concepto,
                        'id_ant'         => $consulta_cabecera_ctventas->id,
                        'id_referencia'  => $id_asiento_cab,
                    ]);

                    return ['status' => 'success', 'msj' => 'Anulado Correctamente'];
                }
                
            }else{
                return ['status' => 'error', 'error' => 'Registro ya anulado'];
            }
           
        } catch (\Exception $ex) {

            return ['status' => 'error', 'error' => $ex->getMessage()];
        }
        //return redirect()->intended('/contable/Banco/notadebito');
    }
    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consulta = null;
        $fecha2 = $request['fecha'];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id_asiento' => $request['buscar_asiento2'],
            'fecha'      => $request['fecha2'],
            'concepto'   => $request['concepto2'],
            'id'         => $request['numero2'],
        ];
        $consulta  = Nota_Debito::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        $consulta = $consulta->where('empresa', $id_empresa)->get();
        Excel::create('Nota_Debito-' . $fecha2 . '-al-' . $fecha2, function ($excel) use ($empresa, $consulta) {
            $excel->sheet('Nota_Debito', function ($sheet) use ($empresa,  $consulta) {
                $sheet->mergeCells('A1:H1');
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
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# de Asiento');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# de Nota');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G2:G2');
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Estado');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H2:H2');
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Creado Por');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $i = 3;
                $total = 0;
                foreach ($consulta as $value) {
                    $total += $value->valor;

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d-m-Y', strtotime($value->fecha)));
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
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue('BAN-ND');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->concepto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (($value->estado) != 0) {
                            $cell->setValue('ACTIVO');
                        } else {
                            $cell->setValue('ANULADO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->creador->nombre1 . $value->creador->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $i++;
                }
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('F' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                ));
            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function setDetalles($consulta, $sheet, $i)
    {

        foreach ($consulta as $value) {

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->numero2);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });


            return $i;
        }
    }
}
