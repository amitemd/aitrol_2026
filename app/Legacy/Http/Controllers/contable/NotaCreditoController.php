<?php

namespace Sis_medico\Http\Controllers\contable;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Ct_Nota_Credito_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Excel;
use Sis_medico\Log_Contable;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Sucursales;
use Sis_medico\LogAsiento;

class NotaCreditoController extends Controller
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
        $empresa = Empresa::where('id', $id_empresa)->first();
        $principales = Ct_Nota_Credito::where('estado', '!=', null)->where('empresa', $id_empresa)->orderby('id', 'desc')->paginate(15);
        return view('contable/nota_credito/index', ['registros' => $principales, 'empresa' => $empresa]);
    }

    public function crear(Request $request)
    {
        ini_set('max_execution_time', 300);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $cuentas = Plan_Cuentas::where('plan_cuentas.estado', '2')->join('plan_cuentas_empresa as pe', 'plan_cuentas.id', '=', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->select('plan_cuentas.*', 'pe.plan as plan_empresa')->get();
        $bancos  = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //Validacion cierre año

        /*$cierreCabecera = Ct_Cierre_Cabecera::where('id_empresa', $id_empresa)->where('estado', '1')->orderBy('id', 'desc')->first();
        $mindate = null;
        if (!is_null($cierreCabecera)) {
            $mindate = date('Y-m-d', strtotime($cierreCabecera->fecha_cierre));
        }*/
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable/nota_credito/create', ['cuentas' => $cuentas, 'bancos' => $bancos, 'divisas' => $divisas, 'min_date' => $min_date, 'sucursales' => $sucursales]);
    }

    public function buscarasiento(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return view('contable/nota_credito/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_asiento' => $request['buscar_asiento'],
            'fecha'      => $request['fecha'],
            'concepto'   => $request['concepto'],
            'id'         => $request['numero'],
        ];
        $id_empresa = $request->session()->get('id_empresa');
        $registros = $this->doSearchingQuery($constraints, $id_empresa);
        return view('contable/nota_credito/index', ['request' => $request, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Nota_Credito::query();
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

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');

        DB::beginTransaction();
        try {
            /*$verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_asiento);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'error' => $verifica_cierre['msj']];
            }*/
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_asiento']));
            //dd($verifica_cierre);
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'error' => $verifica_cierre->message];
            }

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
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $nota_credito        = [
                'concepto'        => $request['observacion'],
                'fecha'           => $request['fecha_asiento'] . ' ' . date('H:i:s'),
                'valor'           => $request['valor'],
                'empresa'         => $id_empresa,
                'tipo'            => "BAN-NC",
                'id_asiento'      => $id_asiento_cabecera,
                'id_banco'        => $request['id_banco'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            $id_nota = Ct_Nota_Credito::insertGetId($nota_credito);

            $arr_total = [];
            for ($i = 0; $i < count($request->input("haber")); $i++) {
                if ($request->input("haber")[$i] != "" && $request->input("nombre")[$i] != "") {
                    $arr = [
                        'haber' => $request->input("haber")[$i],
                        'codigo'        => $request->input("nombre")[$i],
                        'nombre'        => $request->input("no_no")[$i],
                    ];
                    //    print_r($arr);
                    array_push($arr_total, $arr);
                }
            }
            foreach ($arr_total as $valor) {
                //for ($i = 1; $i <= $request['contador']; $i++) {
                $nota_cre_detalle = [
                    'id_nota_credito' => $id_nota,
                    'codigo'          => $valor['codigo'],
                    'cuenta'          => $valor['nombre'],
                    'debe'            => number_format(0, 2),
                    'haber'           => $valor['haber'],
                    'valor_base'      => $valor['haber'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                Ct_Nota_Credito_Detalle::create($nota_cre_detalle);

                $as_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $valor['codigo'],
                    'descripcion'         => $valor['nombre'],
                    'fecha'               => $fecha_as,
                    'debe'                => '0.00',
                    'haber'               => $valor['haber'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                Ct_Asientos_Detalle::create($as_detalle);
            }
            $cuenta_banco = Ct_Caja_Banco::where('id', $request['id_banco'])->first();
            $plan_c       = Plan_Cuentas::where('id', $cuenta_banco['cuenta_mayor'])->first();
            $as_cuenta_g  = [

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_c['id'],
                'descripcion'         => $plan_c['nombre'],
                'fecha'               => $fecha_as,
                'debe'                => $request['valor'],
                'haber'               => '0.00',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ];
            Ct_Asientos_Detalle::create($as_cuenta_g);
            DB::commit();
            //return redirect()->route('notacredito.index');
            return ['status' => 'success', 'idasiento' => $id_asiento_cabecera, 'iddebito' => $id_nota];
        } catch (\Exception $ex) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $ex->getMessage()];
        }
    }

    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Nota_Credito::findorfail($id);
        $empresa  = Empresa::where('id', '0992704152001')->first();
        $bancos   = Ct_Caja_Banco::where('estado', '1')->get();
        $divisas  = Ct_divisas::where('estado', '1')->get();

        $banco   = Ct_Caja_banco::where('id', $registro['id_banco'])->first();
        $detalle = Ct_Nota_Credito_Detalle::where('id_nota_credito', $registro['id'])->get();
        $sucursal = "";
        return view('contable/nota_credito/edit', ['registro' => $registro, 'empresa' => $empresa, 'banco' => $banco, 'bancos' => $bancos, 'divisas' => $divisas, 'detalle' => $detalle, 'sucursal' => $sucursal]);
    }

    public function imprimir($id)
    {
        $registro = Ct_Nota_Credito::findorfail($id);
        $detalle  = Ct_Nota_Credito_Detalle::where('id_nota_credito', $id)->get();

        $vistaurl = "contable.nota_credito.pdf_nota";
        $view     = \View::make($vistaurl, compact('registro', 'detalle'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Nota de Credito -' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));

    }

    public function anular($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        //Obtenemos la fecha de Hoy
        //$fechahoy   = Date('Y-m-d H:i:s');
        //$fechahoy = '2022-01-01'; //prueba
        $registro = Ct_Nota_Credito::findorfail($id);
        $fechahoy   = $registro->fecha;
        $fechahoy = substr($fechahoy, 0, 10);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        try {

            /*$verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'error' => $verifica_cierre['msj']];
            }*/
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            //$estado_credito = Ct_Nota_Credito::where('id', $id)->where('estado', '<>', 0)->first();
            if($registro->estado != '0'){
                
                if (!empty($registro)) {
                    $act_estado = [
                        'estado' => '0',
                    ];
                    //$registro = Ct_Nota_Credito::findorfail($id);
    
                    Ct_Nota_Credito::where('id', $id)->update($act_estado);
    
                    $consulta_cabecera_nc = Ct_Asientos_Cabecera::where('estado', '1')
                        ->where('id', $registro['id_asiento'])->first();
                    $input_cabecera = [
                        'observacion'     => 'ANULACIÓN NOTA DE CRÉDITO',
                        'fecha_asiento'   => $consulta_cabecera_nc->fecha_asiento,
                        'id_empresa'      => $consulta_cabecera_nc->id_empresa,
                        'valor'           => $consulta_cabecera_nc->valor,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'sucursal'        => $consulta_cabecera_nc->sucursal,
                    ];
    
                    //INSERCION EN LA TABLA
                    $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
    
                    $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_nc->id)->get();
    
                    if ($consulta_detalle != '[]') {
    
                        foreach ($consulta_detalle as $value) {
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $id_asiento_cab,
                                'id_plan_cuenta'      => $value->id_plan_cuenta,
                                'descripcion'         => 'ANULACION DE NOTA DE DEBITO',
                                'fecha'               => $consulta_cabecera_nc->fecha_asiento,
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
                        'tipo'           => 'NC',
                        'valor_ant'      => $consulta_cabecera_nc->valor,
                        'valor'          => $consulta_cabecera_nc->valor,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod'  => $idusuario,
                        'observacion'    => $consulta_cabecera_nc->concepto,
                        'id_ant'         => $consulta_cabecera_nc->id,
                        'id_referencia'  => $id_asiento_cab,
                    ]);
                }
                return ['status' => 'success', 'msj' => 'Anulado Correctamente'];
            }
            else{
                return ['status' => 'error', 'msj' => 'Registro ya anulado'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
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
        $consulta  = Ct_Nota_Credito::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        $consulta = $consulta->get();

        Excel::create('Ct_Nota_Credito-' . $fecha2 . '-al-' . $fecha2, function ($excel) use ($empresa, $consulta) {
            $excel->sheet('Ct_Nota_Credito', function ($sheet) use ($empresa,  $consulta) {
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
