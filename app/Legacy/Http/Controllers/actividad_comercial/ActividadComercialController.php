<?php

namespace Sis_medico\Http\Controllers\actividad_comercial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Creacion_Campo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Excel;
use DateTime;
use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Actividad_Estados_Comercial;
use Sis_medico\ActividadComercial;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_ventas;
use Sis_medico\Http\Requests\Request as RequestsRequest;

class ActividadComercialController extends Controller
{

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');

        // // $fecha_desde = $request->input('fecha_desde');
        // // $fecha_hasta = $request->input('fecha_hasta');
        // // dd($request->all());
        // $datos = ActividadComercial::where('id_empresa', $id_empresa);

        $nombres_vendedores = ActividadComercial::where('estado', 1)->groupBy('id_usuariocrea')->get();
       

        $datos = ActividadComercial::where('id_empresa', $id_empresa);
        
       



        // if (isset($request["fecha_desde"]) || !is_null($request["fecha_desde"])) {
        //     $datos = $datos->where('created_at', '>=', $request["fecha_desde"] . ' 00:00:00');
        //     $fecha_desde = $request["fecha_desde"];
        // } else {
        //     $datos = $datos->where('created_at', '>=', date("Y-m-d") . ' 00:00:00');
        //     $fecha_desde = date("Y-m-d");
        // }
        // if (isset($request["fecha_hasta"]) || !is_null($request["fecha_hasta"])) {
        //     $datos = $datos->where('created_at', '<=', $request["fecha_hasta"] . ' 23:59:59');
        //     $fecha_hasta = $request["fecha_hasta"];
        // } else {
        //     $datos = $datos->where('created_at', '<=', date("Y-m-d") . ' 23:59:59');
        //     $fecha_hasta = date("Y-m-d");
        // }


        // $datos = $datos->orderBy('id', 'desc')->paginate(15);




        $vendedor = ActividadComercial::where('id_usuariocrea', $id_usuario)->where('id_empresa', $id_empresa)->groupBy("id_usuariocrea")->get();



        // //dd($vendedor);
        // // ->get();

        // if (isset($request["fecha_desde"]) || !is_null($request["fecha_desde"])) {
        //     $vendedor = $vendedor->where('created_at', '>=', $request["fecha_desde"] . ' 00:00:00');
        //     $fecha_desde = $request["fecha_desde"];
        // } else {
        //     $vendedor = $vendedor->where('created_at', '>=', date("Y-m-d") . ' 00:00:00');
        //     $fecha_desde = date("Y-m-d");
        // }
        // if (isset($request["fecha_hasta"]) || !is_null($request["fecha_hasta"])) {
        //     $vendedor = $vendedor->where('created_at', '<=', $request["fecha_hasta"] . ' 23:59:59');
        //     $fecha_hasta = $request["fecha_hasta"];
        // } else {
        //     $vendedor = $vendedor->where('created_at', '<=', date("Y-m-d") . ' 23:59:59');
        //     $fecha_hasta = date("Y-m-d");
        // }



        $datos = ActividadComercialController::buscador($request->all());


        // foreach ($ver as $actividad) {
        //     if ($actividad->prospecto == 1) {
        //         $this->verificarYActualizarProspecto($actividad->id_cliente);
        //     }
        // }

        return view('actividad_comercial.index', ["datos" => $datos, "vendedores" => $vendedor, "params" => $request->all()]);
    }

    public function create()
    {
        $estados = Actividad_Estados_Comercial::where('estado', 1)->get();
        return view('actividad_comercial.create', ["estados" => $estados]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $id_usuario = Auth::user()->id;
            $id_empresa = Session::get('id_empresa');

            if (is_null($request->fecha_desde)) {
                return ["status" => "error", "message" => "La fecha desde es obligatoria"];
            }
            if (is_null($request->fecha_hasta)) {
                return ["status" => "error", "message" => "La fecha hasta es obligatoria"];
            }

            $fecha_desde = date('Y-m-d H:m:i', strtotime($request->fecha_desde));
            $fecha_hasta = date('Y-m-d H:m:i', strtotime($request->fecha_hasta));

            if ($fecha_desde > $fecha_hasta) {
                return ["status" => "error", "message" => "La fecha desde no puede ser mayor a la fecha hasta"];
            }

            if (is_null($request->cliente)) {
                return ["status" => "error", "message" => "El cliente es obligatorio"];
            }
            if (is_null($request->actividad)) {
                return ["status" => "error", "message" => "La actividad es obligatoria"];
            } else if ($request->actividad == 0) {
                if (is_null($request->text_otro) or $request->text_otro == '') {
                    return ["status" => "error", "message" => "Debe agregar una descripcion en otros"];
                }
            }


            if (is_null($request->observacion)) {
                return ["status" => "error", "message" => "La observacion es obligatoria"];
            }

            // $cliente = Ct_Clientes::find($request->cliente);
            $prospecto  = $this->verificarYActualizarProspecto($request->cliente);

            ActividadComercial::create([
                "fecha_desde"     => $request->fecha_desde,
                "fecha_hasta"     => $request->fecha_hasta,
                "id_cliente"      => $request->cliente,
                "observacion"     => $request->observacion,
                "id_actividad"    => $request->actividad,
                "ip_creacion"     => $ip_cliente,
                "ip_modificacion" => $ip_cliente,
                "id_usuariocrea"  => $id_usuario,
                "id_usuariomod"   => $id_usuario,
                "prospecto"       => $prospecto,
                "otros"           => isset($request['text_otro']) ? $request["text_otro"] : null,
                "id_empresa"      => $id_empresa
            ]);
            DB::commit();
            return ["status" => "success", "message" => "Guardado correctamente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "message" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }


    private function verificarYActualizarProspecto($clienteId)
    {
        $cliente = Ct_Clientes::find($clienteId);

        if ($cliente->prospecto == 0) {
            return 0;
        }
        $existeEnVentas = Ct_Ventas::where('id_cliente', $clienteId)->exists();

        if ($existeEnVentas) {
            $cliente->prospecto = 0;
            $cliente->save();
        }

        return $existeEnVentas ? 0 : 1;



        // dd($cliente);
        // if ($cliente) {
        //     $existeEnVentas = Ct_Ventas::where('id_cliente', $clienteId)->exists();
        //     $cliente->prospecto = $existeEnVentas ? 0 : 1;
        //     $cliente->save();
        // } else {
        // }


        // $actividad = ActividadComercial::where('id_cliente', $clienteId)->first();
        // if ($actividad) {
        //     $existeEnVentas = Ct_Ventas::where('id_cliente', $clienteId)->exists();
        //     $actividad->prospecto = $existeEnVentas ? 0 : 1;
        //     $actividad->save();
        // } else {
        // }
    }



    public function storeProspecto(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_usuario = Auth::user()->id;
        DB::beginTransaction();
        try {

            if (is_null($request['nombre'])) {
                return ["status" => "error", "message" => "El nombre es obligatorio"];
            }
            if (is_null($request['id_cliente'])) {
                return ["status" => "error", "message" => "El identificacion es obligatorio"];
            }
            if (is_null($request['ciudad_prospecto'])) {
                return ["status" => "error", "message" => "La ciudad es obligatoria"];
            }
            if (is_null($request['direccion_prospecto'])) {
                return ["status" => "error", "message" => "La direccion es obligatoria"];
            }
            if (is_null($request['telefono'])) {
                return ["status" => "error", "message" => "El telefono es obligatorio"];
            }

            $clienteBusq = Ct_Clientes::find($request["id_cliente"]);

            if (!is_null($clienteBusq)) {
                return ["status" => "error", "message" => "El cliente ya existe"];
            }

            Ct_Clientes::create([
                'nombre'                  => strtoupper($request['nombre']),
                'tipo'                    => 4,
                'identificacion'          => $request['id_cliente'],
                'nombre_representante'    => $request['nombre'],
                'cedula_representante'    => $request['id_cliente'],
                'ciudad_representante'    => $request['ciudad_prospecto'],
                'direccion_representante' => $request['direccion_prospecto'],
                'telefono1_representante' => $request['telefono'],
                'email_representante'     => $request['correo'],
                'estado'                  => '1',
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'prospecto'               => 1,
            ]);
            DB::commit();
            return ["status" => "success", "message" => "Guardado correctamente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "message" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function viewActividad($id)
    {
        $actividad = ActividadComercial::find($id);

        return view('actividad_comercial.viewActividad', ["actividad" => $actividad]);
    }

    public function storeEstado(Request $request)
    {

        // DD($request->estado_transaccion);

        if (is_null($request->estado_transaccion)) {
            return ["status" => "error", "message" => "El estado es obligatorio"];
        }

        if ($request->estado_transaccion != 1) {
            if (is_null($request->justificacion) or trim($request->justificacion) == "") {
                return ["status" => "error", "message" => "La justificacion es obligatoria"];
            }
        }
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $id_usuario = Auth::user()->id;

            $actividad = ActividadComercial::find($request->id_actividad);
            $actividad->estado_transaccion = $request->estado_transaccion;
            $actividad->justificacion = $request->justificacion;
            $actividad->id_usuariomod = $id_usuario;
            $actividad->ip_modificacion = $ip_cliente;
            $actividad->save();
            DB::commit();
            return ["status" => "success", "message" => "Guardado correctamente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "message" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function deleteActividad(Request $request)
    {
        $actividad = ActividadComercial::find($request->id_actividad);
        if (!is_null($actividad->estado_transaccion)) {
            return ["status" => "error", "message" => "No se puede eliminar una actividad que ya fue procesada"];
        }
        DB::beginTransaction();
        try {
            $actividad->estado = 0;
            $actividad->id_usuariomod = Auth::user()->id;
            $actividad->ip_modificacion = $_SERVER['REMOTE_ADDR'];
            $actividad->save();
            DB::commit();
            return ["status" => "success", "message" => "Eliminado correctamente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "message" => "Error al eliminar", "exp" => $e->getMessage()];
        }
    }

    public function excelactividad(Request $request)
    {

        //dd($request->all());
        $fecha_desde = $request->input('fdesde');
        $fecha_hasta = $request->input('fhasta');

        $actividad = ActividadComercial::whereBetween(
            'fecha_desde',
            [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']
        )->where('estado', 1)->get();
        //dd($actividad);

        $titulos = array("Fecha Desde", "Fecha Hasta", "Cliente", "Direccion", "Actividad", "otros", "observacion", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('Actividad comercial', function ($excel) use ($titulos, $posicion, $actividad) {
            $excel->sheet('actividad comercial', function ($sheet) use ($titulos, $posicion, $actividad) {
                $sheet->mergeCells('C2:E2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Actividad Comercial');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A5:H5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $comienzo = 6;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 7;
                foreach ($actividad as $soporte) {

                    $texto_cliente = ($soporte->id_actividad == 0) ? 'otro' : $soporte->id_actividad;

                    $datos_excel = [];
                    array_push($datos_excel, $soporte->fecha_desde, $soporte->fecha_hasta, $soporte->cliente->nombre, $soporte->cliente->direccion_representante, $texto_cliente, $soporte->otros, $soporte->observacion, $soporte->estado);


                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }






    public function excelvendedor(Request $request)
    {

        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $fecha_desde = $request->input('fdesde');
        $fecha_hasta = $request->input('fhasta');

        $actividad = ActividadComercial::where('id_usuariocrea', $id_usuario)->whereBetween(
            'fecha_desde',
            [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']
        )->where('estado', 1)->get();
        //dd($actividad);

        $titulos = array("Fecha Desde", "Fecha Hasta", "Cliente", "Direccion", "Actividad", "otros", "observacion", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('Actividad comercial', function ($excel) use ($titulos, $posicion, $actividad) {
            $excel->sheet('actividad comercial', function ($sheet) use ($titulos, $posicion, $actividad) {
                $sheet->mergeCells('C2:E2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Actividad Comercial');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A5:H5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $comienzo = 6;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 7;
                foreach ($actividad as $soporte) {

                    $texto_cliente = ($soporte->id_actividad == 0) ? 'otro' : $soporte->id_actividad;

                    $datos_excel = [];
                    array_push($datos_excel, $soporte->fecha_desde, $soporte->fecha_hasta, $soporte->cliente->nombre, $soporte->cliente->direccion_representante, $texto_cliente, $soporte->otros, $soporte->observacion, $soporte->estado);


                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }


    public static function buscador($request)
    {
        $fecha_desde = isset($request['fecha_desde']) ? $request['fecha_desde'] : null;
        $fecha_hasta = isset($request['fecha_hasta']) ? $request['fecha_hasta'] : null;
        $id_cliente  = isset($request['id_cliente']) ? $request['id_cliente'] : null;
        $id_vendedor = isset($request['vendedor']) ? $request['vendedor'] : null;
        $resultados = ActividadComercial::where("id_empresa", Session::get("id_empresa"));
        if (!is_null($fecha_desde)) {
            $resultados = $resultados->where("fecha_desde", ">=", $fecha_desde . " 00:00:00");
        }
        if (!is_null($fecha_hasta)) {
            $resultados = $resultados->where("fecha_desde", "<=", $fecha_hasta . " 23:59:59");
        }
        if (!is_null($id_cliente)) {
            $resultados = $resultados->where("id_cliente", $id_cliente);
        }
        if (Auth::user()->id_tipo_usuario == 17) {
            $resultados = $resultados->where("id_usuariocrea", Auth::user()->id);
        } else {
            if (!is_null($id_vendedor)) {
                $resultados = $resultados->where("id_usuariocrea", $id_vendedor);
            }
        }
        $resultados = $resultados->orderBy("id", "desc")->paginate(15);
        return $resultados;
    }



    public function buscador_prospecto(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');
        $datos = ActividadComercial::where('id_usuariocrea', $id_usuario)->whereBetween('fecha_desde', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->where('estado', 1)
            ->paginate(15);
        return view('actividad_comercial.index', ["datos" => $datos, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }




    public function excel_diferencia(Request $request)
    {

        //dd($request->all());
        $fecha_desde = $request->input('fdesde');
        $fecha_hasta = $request->input('fhasta');




        $actividades = ActividadComercial::whereBetween(
            'fecha_desde',
            [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']
        )->where('estado', 1)
            ->orderBy('created_at', 'asc')
            ->groupBy('id_cliente')
            ->get();




        $titulos = array("Nombre", "Fecha Desde", "Fecha Hasta", "Creacion Prospecto ", "Creacion cliente", "Diferencia");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('REPORTE', function ($excel) use ($titulos, $posicion, $actividades) {
            $excel->sheet('REPORTE', function ($sheet) use ($titulos, $posicion, $actividades) {
                $sheet->mergeCells('B2:D2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Reporte');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A5:F5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $comienzo = 6;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 7;
                foreach ($actividades as $soporte) {
                    $cliente = null;
                    if (!is_null($soporte->id_cliente)) {
                        $cliente = Ct_ventas::where('id_cliente', $soporte->id_cliente)->orderBy('created_at', 'asc')->select('id_cliente', 'created_at')->first();
                    }



                    $diferenciaDias = null;
                    if ($cliente && $soporte->created_at && $cliente->created_at) {
                        $fechaSoporte = Carbon::parse($soporte->created_at);
                        $fechaCliente = Carbon::parse($cliente->created_at);
                        $diferenciaDias = $fechaSoporte->diffInDays($fechaCliente);
                    }

                    $datos_excel = [];
                    array_push(
                        $datos_excel,
                        $soporte->cliente->nombre,
                        date("Y-m-d", strtotime($soporte->fecha_desde)),
                        date("Y-m-d", strtotime($soporte->fecha_hasta)),
                        date("Y-m-d", strtotime($soporte->created_at)),
                        $cliente ?  date("Y-m-d", strtotime($cliente->created_at)) : null,
                        ($cliente && $cliente->created_at) ? $diferenciaDias . ' ' . ' DIAS' : null
                    );


                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }



    public function excel_diferencia_vendedor(Request $request)
    {

        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $fecha_desde = $request->input('fdesde');
        $fecha_hasta = $request->input('fhasta');




        $actividades = ActividadComercial::where('id_usuariocrea', $id_usuario)
            ->whereBetween(
                'fecha_desde',
                [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']
            )->where('estado', 1)
            ->orderBy('created_at', 'asc')
            ->groupBy('id_cliente')
            ->get();




        $titulos = array("Nombre", "Fecha Desde", "Fecha Hasta", "Creacion Prospecto ", "Creacion cliente", "Diferencia");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('REPORTE', function ($excel) use ($titulos, $posicion, $actividades) {
            $excel->sheet('REPORTE', function ($sheet) use ($titulos, $posicion, $actividades) {
                $sheet->mergeCells('B2:D2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Reporte');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A5:F5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $comienzo = 6;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 7;
                foreach ($actividades as $soporte) {
                    $cliente = null;
                    if (!is_null($soporte->id_cliente)) {
                        $cliente = Ct_ventas::where('id_cliente', $soporte->id_cliente)->orderBy('created_at', 'asc')->select('id_cliente', 'created_at')->first();
                    }



                    $diferenciaDias = null;
                    if ($cliente && $soporte->created_at && $cliente->created_at) {
                        $fechaSoporte = Carbon::parse($soporte->created_at);
                        $fechaCliente = Carbon::parse($cliente->created_at);
                        $diferenciaDias = $fechaSoporte->diffInDays($fechaCliente);
                    }

                    $datos_excel = [];
                    array_push(
                        $datos_excel,
                        $soporte->cliente->nombre,
                        $soporte->fecha_desde,
                        $soporte->fecha_hasta,
                        $soporte->created_at,
                        $cliente ? $cliente->created_at : null,
                        ($cliente && $cliente->created_at) ? $diferenciaDias . ' ' . ' DIAS' : null
                    );


                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }








    public function actividad_estadistico(Request $request)
    {
        $anio = $request->input('anio');


        $registros = ActividadComercial::where('estado', 1)
            ->whereYear('fecha_desde', $anio)
            ->get();

        $registrosData = $registros->map(function ($registro) {
            return [
                'fecha' => $registro->fecha_desde,

            ];
        });

        return view('actividad_comercial.estadistico', compact('registrosData', 'anio', 'request', 'registros'));
    }

    public function estadistico_com()
    {

        $registros = ActividadComercial::where('estado', 1)->get();
        $arrayClientes = array_fill_keys(
            [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ],
            ['month' => '', 'count' => 0]
        );

        $arrayProspecto = array_fill_keys(
            [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ],
            ['month' => '', 'count' => 0]
        );

        $arrayVisitasUsuario = array_fill_keys(
            [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ],
            ['month' => '', 'count' => 0]
        );

        foreach ($registros as $val) {
            setlocale(LC_TIME, 'es_ES');
            $mes = strtolower(strftime('%B', strtotime($val->fecha_desde)));

            if ($val->prospecto == 1) {
                $arrayProspecto[$mes]['month'] = $mes;
                $arrayProspecto[$mes]['count']++;
            } else {
                $arrayClientes[$mes]['month'] = $mes;
                $arrayClientes[$mes]['count']++;
            }
            $arrayVisitasUsuario[$mes]['month'] = $mes;
            $arrayVisitasUsuario[$mes]['count']++;
        }

        return response()->json([$arrayClientes, $arrayProspecto, $arrayVisitasUsuario]);
    }


    public function buscador_anio(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');

        $registros = ActividadComercial::where('estado', 1)
            ->whereBetween('fecha_desde', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->get();

        // Inicializar los arrays de meses con los meses dentro del rango de fechas
        $arrayClientes = $this->inicializarArrayMeses($fecha_desde, $fecha_hasta);
        $arrayProspecto = $this->inicializarArrayMeses($fecha_desde, $fecha_hasta);
        $arrayVisitasUsuario = $this->inicializarArrayMeses($fecha_desde, $fecha_hasta);


        $arrayTotal = [
            "prospecto" => 0,
            "clientes" => 0,
            "vista"=>0,
        ];
       

        foreach ($registros as $val) {
            $mes = date('F', strtotime($val->fecha_desde));

          
            if ($val->prospecto == 1) {
                $arrayTotal["prospecto"] ++;
                $arrayProspecto[$mes]['count']++;
            } else {
                $arrayTotal["clientes"] ++;
                $arrayClientes[$mes]['count']++;
            }
            $arrayTotal["vista"] ++;
            $arrayVisitasUsuario[$mes]['count']++;
        }

       

        return response()->json([$arrayClientes, $arrayProspecto, $arrayVisitasUsuario,$arrayTotal]);
    }

    private function inicializarArrayMeses($fecha_desde, $fecha_hasta)
    {
        $meses = [];
        $inicio = new DateTime($fecha_desde);
        $fin = new DateTime($fecha_hasta);


        while ($inicio < $fin) {
            $mes = $inicio->format('F');
            $meses[$mes] = ['month' => $mes, 'count' => 0];
            $inicio->modify('+1 month');
        }

        return $meses;
    }
}
