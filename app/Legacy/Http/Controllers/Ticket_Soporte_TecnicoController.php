<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Ct_Nomina;
use Sis_medico\Paciente;
use Sis_medico\Permisos_Tecnicos;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Image;
use Mail;

use Sis_medico\Ticket_Soporte_Tecnico;



class Ticket_Soporte_TecnicoController extends Controller
{

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($rolUsuario == 2) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha_desde = '';
        $fecha_hasta = '';

        return view('ticket_soporte_tecnico/index', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function create(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $usuarios = User::where('id_tipo_usuario', '<>', 2)->get();
        $id_auth    = Auth::user()->id;
        $usuario_tabla = Permisos_Tecnicos::where('cedula',$id_auth)->first();
        return view('ticket_soporte_tecnico/create', ['usuarios' => $usuarios, 'empresa' => $empresa,'usuario_tabla'=>$usuario_tabla]);
    }
    public function guardar(Request $request)
    {
        //sdd($request->all());
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];


        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $variable =   Ticket_Soporte_Tecnico::create([

            'area' => $request['area'],
            'requerimientos'  => $request->requerimientos,
            'observacion' => $request['observacion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'usuario_solicitante' => $request['usuario'],
        ]);

        $correo = ['infraestructuratecnologica@ieced.ec', 'asist_infraestructura@ieced.ec'];
        $datosCompleto = Ticket_Soporte_Tecnico::find($variable->id);
        $nombresoficina = "";
        if (isset($datosCompleto->nombre)) {
            $nombresoficina = $datosCompleto->nombre->nombre1 . ' ' . $datosCompleto->nombre->apellido1;
        }

        foreach ($correo as $val) {
            $arrayDatos = array("fecha" => $datosCompleto->created_at, "nombrePersona" => $nombresoficina, "requerimiento" => $datosCompleto->requerimientos, "area" => $datosCompleto->area);
            Mail::send('ticket_soporte_tecnico.correo_tipo', $arrayDatos, function ($msj) use ($val) {
                $msj->subject('Requerimientos');
                $msj->to($val);
                $msj->bcc('torbi10@hotmail.com');
            });
        }

        return json_encode("ok");
    }

    public function control_req($id)
    {
        $requerimiento = Ticket_Soporte_Tecnico::find($id);
        //dd($requerimiento);
        return view('ticket_soporte_tecnico/admin_control', ['requerimiento' => $requerimiento]);
    }

    public function admin_control(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $usuarios = User::where('estado', '1')->get();
        $idreq = $request['idrequerimiento'];
        $requerimiento = Ticket_Soporte_Tecnico::find($idreq);
        try {
            $requerimiento->update([

                'requerimientos'  => $request['requerimientos'],
                'area' => $request['area'],
                'estado' => $request['estado'],
                'observacion' => $request['observacion'],
                'responsable' => $request['responsable'],

            ]);
        } catch (\Throwable $th) {
            return json_encode(strval($th));
        }

        return json_encode('ok');
    }
    public function excel_soporte_tecnico($desde, $hasta, $request)
    {
        $todos = Ticket_Soporte_Tecnico::all();
        $fecha_desde = $desde;
        $fecha_hasta = $hasta;
        $detalles = 7;
        $id_empresa = $request->session()->get('id_empresa');
        $titulos = array("N° DE SOLICITUD:", "FECHA DE SOLICITUD:", "MEDIO DE SOLICITUD:", "ÁREA:", " SOLICITANTE:", "PRIORIDAD:", "FECHA DE FINALIZACION:", "RESPONSABLE:");
        $empresa = Empresa::where('id', $id_empresa)->first();
        $control = Ticket_Soporte_Tecnico::wherebetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S");
        Excel::create('Reporte Ticket_Soporte_Tecnico', function ($excel) use ($titulos, $posicion, $empresa, $todos, $detalles, $control) {
            $excel->sheet('Reporte Master', function ($sheet) use ($titulos, $posicion, $empresa, $todos, $detalles, $control) {
                $comienzo = 8; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel

                $m      = 12;
                $t      = 8;
                $sei    = 12;
                $sette  = 15;
                $otto   = 8;
                $undici = 11;
                $fecha_soli = 9;
                //$h_inicial = 10;
                $medio  = 10;
                $area   = 11;
                $soli   = 12;
                $priori = 13;
                $hora   = 14;
                //$f_fin  = 15;
                $resp   = 15;

                foreach ($control as $key => $value) {

                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[0] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $comienzo++;
                    }

                    $comienzo++;

                    $sheet->cell('C' . $fecha_soli, function ($cell) use ($i, $value) {
                        $cell->setValue($value->requerimientos);
                        $cell->setAlignment('center');
                    });

                    $sheet->cell('B' . $t, function ($cell) use ($i, $value) {
                        $cell->setValue($value->id);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $fecha_soli, function ($cell) use ($i, $value) {
                        $cell->setValue(substr($value->created_at, 0, 10));
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // $sheet->cell('B' . $h_inicial, function ($cell) use ($i, $value) {
                    //     $cell->setValue(substr($value->created_at, -8));
                    //     $cell->setFontWeight('bold');
                    //     $cell->setAlignment('center');
                    //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // });

                    $sheet->cell('B' . $medio, function ($cell) use ($value, $medio) {
                        //dd($medio);
                        $cell->setValue('SISTEMA SIAM');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $area, function ($cell) use ($i, $value) {
                        $cell->setValue($value->area);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $soli, function ($cell) use ($i, $value) {
                        if (isset($value->nombre)) {
                            $cell->setValue($value->nombre->nombre1 . ' ' . $value->nombre->nombre2 . '' . $value->nombre->apellido1 . ' ' . $value->nombre->apellido2);
                        } else {
                            $cell->setValue("");
                        }
                        //$cell->setValue($value->nombre->nombre1 . ' ' . $value->nombre->nombre2 . '' . $value->nombre->apellido1 . ' ' . $value->nombre->apellido2);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $priori, function ($cell) use ($i, $value) {
                        if ($value->estado == 0) {
                            $cell->setValue('Inicial');
                            $cell->setBackground('#2ECC71');
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                        if ($value->estado == 1) {
                            $cell->setValue('Proceso');
                            $cell->setBackground('#FEE34A');
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                        if ($value->estado == 2) {
                            $cell->setValue('Finalizado');
                            $cell->setBackground('#fe1707');
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('B' . $hora, function ($cell) use ($i, $value) {
                        $cell->setValue(substr($value->updated_at, 0, 10));
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // $sheet->cell('B' . $f_fin, function ($cell) use ($i, $value) {
                    //     $cell->setValue(substr($value->updated_at, -8));
                    //     $cell->setFontWeight('bold');
                    //     $cell->setAlignment('center');
                    //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // });
                    $sheet->cell('B' . $resp, function ($cell) use ($i, $value) {
                        if (isset($value->responsable)) {
                            $cell->setValue($value->u_responsable->nombre1 . ' ' . $value->u_responsable->nombre2 . ' ' . $value->u_responsable->apellido1 . ' ' . $value->u_responsable->apellido2);
                        } else {
                            $cell->setValue("");
                        }
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $t, function ($cell) {
                        $cell->setValue('REQUERIMIENTO O PROBLEMA:');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cells("C$otto:C$undici", function ($cell) {
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $m, function ($cell) {
                        $cell->setValue('OBSERVACIONES:');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('C' . $priori, function ($cell) use ($value) {
                        $cell->setValue($value->observacion);
                    });
                    $sheet->cells("C$sei:C$sette", function ($cell) {

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $m      += 9;
                    $t      += 9;
                    $sei    += 9;
                    $sette  += 9;
                    $otto   += 9;
                    $undici += 9;
                    $fecha_soli += 9;
                    //$h_inicial += 9;
                    $medio  += 9;
                    $area   += 9;
                    $soli   += 9;
                    $priori += 9;
                    $hora   += 9;
                    //$f_fin  += 9;
                    $resp   += 9;
                }


                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B' . $detalles, function ($cell) {
                    $cell->setValue('DETALLE DE SOLICITUDES:');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $detalles += 9;
                $sheet->cell('A6', function ($cell) {
                    $cell->setValue('FECHA:');

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C8', function ($cell) {
                    $cell->setValue('REQUERIMIENTO O PROBLEMA:');
                });
                $sheet->cells('C8:C11', function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C12', function ($cell) {
                    $cell->setValue('OBSERVACIONES:');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('REGISTRO DE SOLICITUDES');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B2', function ($cell) {
                    $cell->setValue('DE SOPORTE TÉCNICO:');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cells('C1:C5', function ($cell) {

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('B6:C6', function ($cell) {
                    //$cell->setValue($control);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('B1:B5', function ($cell) {

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A7:C7', function ($cell) {
                    $cell->setBackground('#F8CBAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A7:A5', function ($cell) use ($empresa, $sheet) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(80);
                    $objDrawing->setWidth(160);
                    $cell->setAlignment('center');
                    $objDrawing->setWorksheet($sheet);
                });
                $sheet->setWidth(array(
                    'A' => 40,
                    'C' => 40,
                    'B' => 40,
                ));
            });
        })->export('xlsx');
    }

    public function autocompletar(Request $request)
    {

        $nuevo_nombre = explode(' ', $request['data']);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $cambio =  User::where(DB::raw('CONCAT(nombre1, nombre2)'), 'like', $seteo)->limit(5)->get();
        if (!is_null($cambio)) {
            return json_encode($cambio);
        } else {
            return json_encode('No se encontraron registros concidentes');
        }
    }


    public function autocompletar_apellido(Request $request)
    {

        $nuevo_nombre = explode(' ', $request['data']);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $cambio =  User::where(DB::raw('CONCAT(apellido1, apellido2)'), 'like', $seteo)->limit(5)->get();
        if (!is_null($cambio)) {
            return json_encode($cambio);
        } else {
            return json_encode('No se encontraron registros concidentes');
        }
    }


    public function buscador(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $this->validate($request, [
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d' . ($request->input('desde') ? '|after_or_equal:desde' : ''),
            'estado' => 'nullable|in:0,1,2',
        ]);
        return view('ticket_soporte_tecnico/index', [
            'fecha_desde' => $request->input('desde', ''),
            'fecha_hasta' => $request->input('hasta', ''),
        ]);
    }
}
