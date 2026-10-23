<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Sis_medico\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Empresa;
use Sis_medico\Mantenimientos_Oficinas;
use Sis_medico\Mantenimientos_Insumos_Limpieza;
use Sis_medico\Mantenimientos_Dotacion;
use Sis_medico\LimpiezaArea;
use Sis_medico\Insumos_area_nuevo;
use Sis_medico\User;
use Sis_medico\Mantenimientos_Generales;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Firma_Servicios_Generales;
use Sis_medico\Mantenimientos_Banos;
use Sis_medico\ProductosUtilizadosArea;
use Sis_medico\Http\Controllers\limpieza_pentax\LimpiezaPentaxController;

class LimpiezaAreaController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }

    public function created($id, $request)
    {

        $oficinas = Mantenimientos_Oficinas::where('id_unidad', $id)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();

        return view('servicios_generales/limpieza_area/create', ['oficinas' => $oficinas, 'productos' => $productos, 'insumos' => $insumos]);
    }

    public function save(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $subir_imagen = new LimpiezaPentaxController;
        $idusuario  = Auth::user()->id;
        $imagen = $subir_imagen->uploading($request['evidencia_antes'], $idusuario);
        try {
            $area = new LimpiezaArea;
            $area->id_oficina = $request['eleccion'];
            $area->tipo_desinfeccion = $request['tipo_desinfeccion'];
            $area->observaciones =   $request['observaciones'];
            $area->accion_limpieza =   $request['accion_limpieza'];
            $area->path_antes = $imagen['data'];
            $area->responsable =$idusuario;
            $area->fecha=date('Y-m-d H:i:s');
            $area->frecuencia_area= date('Y-m-d H:i:s');
            $area->id_usuariocrea = $idusuario;
            $area->id_usuariomod = $idusuario;
            $area->save();
            foreach ($request['product_utilizados'] as $val) {
                $sav1 = new Insumos_area_nuevo;
                $sav1->id_limpieza_area = $area->id;
                $sav1->id_insumos = $val;
                $sav1->save();
            }
            return redirect()->route('index_limpieza_area');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function index(Request $request)
    {
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        $control_limp = [];
        $id_auth      = Auth::user()->id;
        $permisos = Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
        if ($rolUsuario == 24 || $rolUsuario == 6) {
           $control_limp = LimpiezaArea::whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('id_usuariocrea', $id_auth)->orderBy('created_at', 'DESC')->paginate(20);
       }else{
           $control_limp = LimpiezaArea::where('estado', 1)->paginate(10);
       }
        return view('servicios_generales/limpieza_area/index', ['limpieza' => $control_limp,'permisos'=>$permisos]);
    }
    public function edit(Request $request)
    {

        $id = $request['id'];
        $registro = LimpiezaArea::find($id);
        $oficinas = Mantenimientos_Oficinas::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();



        return view('servicios_generales/limpieza_area/edit', ['registro' => $registro, 'oficinas' => $oficinas, 'productos' => $productos, 'insumos' => $insumos]);
    }

    public function edit_save(Request $request)
    {

        try {
            $subir_imagen = new LimpiezaPentaxController;
            $idusuario  = Auth::user()->id;
            $imagen = $subir_imagen->uploading($request['evidencia_despues'], $idusuario);
            $registro = LimpiezaArea::find($request['id']);
            $registro->path_despues =  $imagen['data'];
            $registro->save();
            return redirect()->route('index_limpieza_area');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function buscador(Request $request)
    {
        $limpieza = LimpiezaArea::where('estado', 1)->whereBetween('created_at', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59'])->paginate(10);
        return view('servicios_generales/limpieza_area/index', ['limpieza' => $limpieza]);
    }

    public function excel(Request $request)
    {


        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $tipo         = $request['tipo'];
        $fecha_desde  = $request['desde'];
        $fecha_hasta  = $request['hasta'];
        $piso = $request['piso'];
        $edificio = $request['edificio'];
        $firma = null;

        $control_area = DB::table('limpieza_area');

        if (!is_null($request['hasta']) && !is_null($request['desde'])) {
            $control_area = $control_area->whereBetween('limpieza_area.created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($request['encargados'])) {

            $control_area = $control_area->where('limpieza_area.responsable', $request['encargados']);

        }
        if (!is_null($request['id_oficina'])) {
            $control_area = $control_area->where('limpieza_area.id_oficina', $request['id_oficina']);
        }
        if (!is_null($piso)) {
            $control_area = $control_area->where('limpieza_area.id_oficina', $piso);
        }
        if(!is_null($edificio)){

            $control_area = $control_area->join('mantenimientos_oficinas','limpieza_area.id_oficina','=','mantenimientos_oficinas.id')
            ->join('mantenimientos_generales','mantenimientos_generales.id','=','mantenimientos_oficinas.id_unidad')
            ->where('mantenimientos_oficinas.id_unidad',$edificio)
            ->select('mantenimientos_generales.nombre as nombreMa','limpieza_area.created_at','mantenimientos_oficinas.nombre_oficina as nombreof');

        }

        //$control_area = $control_area->where('id_oficina', $request['id'])->get();

        $control_area = $control_area->addSelect('limpieza_area.*')->get();




        $nombre_piso  = Mantenimientos_Generales::all();

        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();

      //dd($firma);
        // dd($request->all());


        Excel::create('REGISTRO DE LIMPIEZA Y DESINFECCION DE ÁREAS Vers. 0.1', function ($excel) use ($control_area, $request, $empresa, $tipo, $insumos, $productos, $piso) {
            $excel->sheet('REPORTE LIMPIEZA', function ($sheet) use ($request, $control_area, $empresa, $tipo, $insumos, $productos,$piso) {

                if ($tipo != 0) {
                    $sheet->mergeCells('A1:B2');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(100);
                    $objDrawing->setWidth(100);
                    $objDrawing->setWorksheet($sheet);
                }else{

                    $sheet->mergeCells('A1:B2');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/logo_crmserv.png');
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(100);
                    $objDrawing->setWidth(100);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C1:J1');
                $sheet->cell('C1', function ($cell) use ($empresa, $tipo) {
                    if (!is_null($empresa)) {
                        $cell->setValue($tipo == 1 ? $empresa->razonsocial : 'CARLOS ROBLES MEDRANDA');
                    }
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('C2:J2');
                $sheet->cell('C2', function ($cell) {
                    $cell->setValue('REGISTRO DE LIMPIEZA Y DESINFECCION DE ÁREAS');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K1:K2');
                $sheet->cell('K1', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#0499DB');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:A4');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('B3:B4');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('UBICACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C3:C4');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ÁREAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('D3:D4');
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA INICIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('E3:E4');
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA FIN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('F3:G3');
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE DESINFECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('F4:F4');
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCURRENTE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('G4:G4');
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TERMINAL ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H3:H4');
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMOS ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('I3:I4');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('J3:J4');
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('K3:K4');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIONES ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                //$n= 1;
                $i = 5;

                foreach ($control_area as $value) {



                      $firma     = Firma_Servicios_Generales::where('id_usuario', $value->responsable)->first();

                    //dd($value);
                      $sheet->cell('A' . $i, function ($cell) use ($value) {
                          $cell->setValue(date('Y-m-d', strtotime($value->created_at)));
                          $cell->setBorder('thin', 'thin', 'thin', 'thin');
                      });
                      $sheet->cell('B' . $i, function ($cell) use ($value) {
                          // manipulate the cel
                          if(isset($value->nombreMa)){
                              $cell->setValue($value->nombreMa);
                          }else{
                              $cell->setValue('');
                          }

                          $cell->setBorder('thin', 'thin', 'thin', 'thin');
                      });



                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if(isset($value->nombreof)){
                            $cell->setValue($value->nombreof);
                        }else{
                            $cell->setValue('');
                        }


                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        if (is_null($value->frecuencia_area)) {
                            $cell->setValue(" ");
                        } else {
                            $cell->setValue(date('H:i', strtotime($value->frecuencia_area)));
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        if (is_null($value->updated_at)) {
                            $cell->setValue(" ");
                        } else {
                            $cell->setValue(date('H:i', strtotime($value->updated_at)));
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    /////
                    if ($value->tipo_desinfeccion == 1) {
                        $sheet->cell('F' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        $sheet->cell('F' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    if ($value->tipo_desinfeccion == 2) {
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $productos = "";
                    $produc = Insumos_area_nuevo::where('id_limpieza_area',$value->id)->get();

                    foreach($produc as $prod){
                            $productos = $productos . "-{$prod->insumos->nombre}";

                    }


                $sheet->cell('H' . $i, function ($cell) use ($productos) {
                    // manipulate the cel
                    $cell->setValue($productos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });



                    $user = User::where('id',$value->responsable)->first();

                    $sheet->cell('I' . $i, function ($cell) use ($user) {
                        // manipulate the cel
                        $cell->setValue($user->nombre1 . ' ' . $user->apellido1 . ' ' . $user->apellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('J' . $i);
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    if(!is_null($firma)){
                      $objDrawing->setPath(base_path() . '/storage/app/avatars/' . $firma->nombre);
                      $objDrawing->setCoordinates('J'.$i);
                      $objDrawing->setWidthAndHeight(130,40);
                      $objDrawing->setWorksheet($sheet);
                    }
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                  //  $n++;
                    $i++;
                }

                $sheet->mergeCells('A' . $i . ':K' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nota: Colocar una " X " al tipo de actividad que se realice.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i++;

                $sheet->mergeCells('A' . $i . ':D' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIMPIEZA MARQUE UNA ¨X¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('E' . $i . ':H' . $i);
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESINFECCIÓN MARQUE UNA ¨XX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('I' . $i . ':K' . $i);
                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIMPIEZA Y DESINFECCIÓN MARQUE UNA ¨XXX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });


                /*****FIN DE TITULOS DEL EXCEL***********/
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $excel->getActiveSheet()->getStyle('G4')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H5:H900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I5:I900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('K5:K900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('J5:J900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L5:L900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N6')->getAlignment()->setWrapText(true);


        })->export('xlsx');
    }

    public function vistareportesareas(Request $request)
    {
        //$control_area = Limpieza_Banos::where('id', $request['id'])->first();
        //$pisoBa = Mantenimientos_Banos::where('id_unidad', $control_limp->id_oficina)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $piso = Mantenimientos_Oficinas::where('estado',1)->get();
        $edificio = Mantenimientos_Generales::where('estado',1)->get();

        //$nombre_piso = Mantenimientos_Generales::where('estado', '1')->get();

        $encargados = User::where('id_tipo_usuario', '24')->get();

        return view('servicios_generales/limpieza_area/reporte_areas', [ 'encargados' => $encargados,'piso'=>$piso,'edificio'=>$edificio]);
    }

    public function modal_foto(Request $request){

      $registro = LimpiezaArea::find($request['id']);
      return view('servicios_generales/limpieza_area/modal',['tipo'=>$request['tipo'],'registro'=>$registro]);

    }
}
