<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Image;
use PHPExcel_Worksheet_Drawing;
use Response;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Limpieza_Banos;
use Sis_medico\Mantenimientos_Generales;
use Sis_medico\User;
use Sis_medico\Mantenimientos_Dotacion;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Mantenimientos_Insumos_Limpieza;
use Sis_medico\Insumos_utilizados_banos;
use Sis_medico\InsumosUtilizadosArea;
use Sis_medico\Mantenimientos_Banos;
use Sis_medico\Firma_Servicios_Generales;

class LimpiezaBanosController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4, 6)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        $fecha_desde  = date('Y-m-d');
        $fecha_hasta  = date('Y-m-d');
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        $id_auth      = Auth::user()->id;
        $permisos = Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();



        $control_limp = [];
        $arrayEnvio   = array(); 
        $array        = array();
        $aa           = [];
        if ($rolUsuario == 24 || $rolUsuario == 6 ) {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('responsable', $id_auth)->orderBy('fecha', 'DESC')->paginate(20);

           

            



             
        } else {

            //dd('entra');
            $control_limp = Limpieza_Banos::whereBetween('fecha', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->paginate(20);

        }

        //dd($control_limp);
        return view('servicios_generales/limpieza_banos/index', ['control_limp' => $control_limp, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function salas()
    {
        $nombre_piso = Mantenimientos_Generales::all();
        return view('servicios_generales/limpieza_banos/index_2', ['nombre_piso' => $nombre_piso]);
    }
    public function create($id_sala, Request $request)
    {
        $piso = Mantenimientos_Generales::findorfail($id_sala);
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $pisoBa = Mantenimientos_Banos::where('id_unidad', $id_sala)->get();
        return view('servicios_generales/limpieza_banos/create', ['pisoBa' => $pisoBa, 'productos' => $productos, 'insumos' => $insumos, 'id_sala' => $id_sala, 'piso' => $piso]);
    }

    public function pisos()
    {
        $pisos = Mantenimientos_Generales::all();
        return view('servicios_generales/limpieza_banos/pisos', ['pisos' => $pisos]);
    }

    public function guardar(Request $request)
    {


        $hoy        = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario  = Auth::user()->id;

        Limpieza_Banos::create([
            'fecha'           => $hoy,
            'nombre_piso'     => $request['nombre_piso'],
            'desinfectante'   => $request['desinfectante'],
            'evidencia_antes' => $request['evidencia_antes'],
           // 'evidencia_desp'  => $request['evidencia_desp'],
            'observacion'     => $request['observaciones'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'responsable'     => $idusuario,
        ]);

        return json_encode("ok");
    }
    public function buscar_fecha(Request $request)
    {
      //dd($request->all());
        $fecha_desde  = $request['desde'];
        $fecha_hasta  = $request['hasta'];

        //$id_sala = $request['id_sala'];
        //  $sala = Mantenimientos_Generales::find($id_sala);
        $control_limp = Limpieza_Banos::whereBetween('fecha', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59'])->paginate(10);
        $id_auth      = Auth::user()->id;
        $permisos = Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
        $control_limp = [];
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        if ($rolUsuario == 24) {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('responsable', $id_auth)->paginate(10);

        } else {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(10);
        }
        return view('servicios_generales/limpieza_banos/index', ['control_limp' => $control_limp, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function subir_imagen(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $nuevo_nombre  = "";
        $nuevo_nombre2 = "";
        $id            = $request['id']; // puede ser el id de la tabla en la base de datos
        if (!empty($request['evidencia_antes'])) {
            $nombre_original = $request['evidencia_antes']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension       = $request['evidencia_antes']->getClientOriginalExtension();
            $nuevo_nombre    = "limpiezaant_" . date('YmdHis') . $idusuario . "." . $extension;
            $r1              = Storage::disk('public')->put($nuevo_nombre, \File::get($request['evidencia_antes']));
            $image           = Image::make(Storage::disk('public')->get($nuevo_nombre));

            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));

            //$image = Image::make(Storage::get($path));
        }
        if (!empty($request['evidencia_desp'])) {
            $nombre_original2 = $request['evidencia_desp']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension2       = $request['evidencia_desp']->getClientOriginalExtension();

            $nuevo_nombre2 = "limpiezades_" . date('YmdHis') . $idusuario . "." . $extension2;
            $r2            = Storage::disk('public')->put($nuevo_nombre2, \File::get($request['evidencia_desp']));

            $image = Image::make(Storage::disk('public')->get($nuevo_nombre2));
            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk('public')->put($nuevo_nombre2, (string) $image->encode('jpg', 30));

            //$image = Image::make(Storage::get($path));
        }
        //aqui creamos el registro
        $ty =  Limpieza_Banos::create([
            'responsable'     => $idusuario,
            'fecha'           => date('Y-m-d H:i:s'),
            'frecuencia1'     => date('Y-m-d H:i:s'),
            'evidencia_antes' => $nuevo_nombre,
            'observacion'     => $request['observaciones'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'id_piso'  => $request['piso'],
            'limpieza'  => $request['limpieza'],
        ]);

        foreach ($request['insumos'] as $value) {
            $registro = new Insumos_utilizados_banos;
            $registro->id_limpieza_banos = $ty->id;
            $registro->id_insumos_banos = $value;
            $registro->id_usuariocrea = $idusuario;
            $registro->id_usuariomod = $idusuario;
            $registro->save();
        }
        foreach ($request['producto'] as $value) {
            $registro = new InsumosUtilizadosArea;
            $registro->id_limpieza_area = $ty->id;
            $registro->id_insumos = $value;
            $registro->save();
        }
        /*    $editar = DB::table('limpieza_banos')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]); //  *///esto es en el edit pero estamos en el create asi que no tenemos el id del formulatio
        return redirect()->route('limpieza_banos.index_2');
    }
    public function editar($id)
    {
        $control_limp = Limpieza_Banos::where('id', $id)->first();
        $nombre_piso  = Mantenimientos_Generales::all();
        $pisoBa = Mantenimientos_Banos::where('estado',1)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        //dd($pisoBa);
        return view('servicios_generales.limpieza_banos.edit', ['insumos' => $insumos, 'productos' => $productos, 'pisoBa' => $pisoBa, 'control_limp' => $control_limp, 'id' => $id, 'nombre_piso' => $nombre_piso]);
    }

    public function actualizar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id               = $request['id']; // puede ser el id de la tabla en la base de datos
        $nombre_original  = "";
        $nombre_original2 = "";
        //dd($request->all());
        $control_limp = Limpieza_Banos::where('id', $request['id'])->first();
        if (!is_null($request['evidencia_antes'])) {
            //dd($request['evidencia_antes']);
            $nombre_original = $request['evidencia_antes']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension       = $request['evidencia_antes']->getClientOriginalExtension();
            $r1              = Storage::disk('public')->put($nombre_original, \File::get($request['evidencia_antes']));
        } else {
            $nombre_original = $control_limp->evidencia_antes;
        }
        if (!is_null($request['evidencia_desp'])) {
            $nombre_original2 = $request['evidencia_desp']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension2       = $request['evidencia_desp']->getClientOriginalExtension();
            $r1               = Storage::disk('public')->put($nombre_original2, \File::get($request['evidencia_desp']));
            $rutadelaimagen   = $nombre_original; //dd($request->all());
        } else {
            $nombre_original2 = $control_limp->evidencia_desp;
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //dd($control_limp);
        $control_limp->responsable     = $idusuario;
        //$control_limp->desinfectante   = $request['desinfectante'];
        $control_limp->evidencia_desp  = $nombre_original2;
        $control_limp->ip_creacion     = $ip_cliente;
        $control_limp->ip_modificacion = $ip_cliente;
        $control_limp->id_usuariocrea  = $idusuario;
        $control_limp->id_usuariomod   = $idusuario;
        $control_limp->save();

        return redirect()->route('limpieza_banos.index');
    }
    public function agregarhoras(Request $request)
    {
        $hoy      = date("Y-m-d H:i:s");
        $horarios = Limpieza_Banos::where('id', $request['id'])->first();
        if ($horarios->frecuencia1 == null) {
            $horarios->frecuencia1 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia2 == null) {
            $horarios->frecuencia2 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia3 == null) {
            $horarios->frecuencia3 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia4 == null) {
            $horarios->frecuencia4 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } else {
            $array = array('lleno', $request['id']);
            return json_encode($array);
        }
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
        $control_limp = DB::table('limpieza_banos');
        if (!is_null($request['hasta']) && !is_null($request['desde'])) {
            $control_limp = $control_limp->whereBetween('limpieza_banos.created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($request['encargados'])) {

            $control_limp = $control_limp->where('limpieza_banos.responsable', $request['encargados']);
        }
        if (!is_null($request['id_piso'])) {
            $control_limp = $control_limp->where('limpieza_banos.id_piso', $request['id_piso']);
        }
        if (!is_null($piso)) {
            $control_limp = $control_limp->where('limpieza_banos.id_piso', $piso);
        }
        if(!is_null($edificio)){

            $control_limp = $control_limp->join('mantenimientos_banos','limpieza_banos.id_piso','=','mantenimientos_banos.id')->join('mantenimientos_generales','mantenimientos_generales.id','=','mantenimientos_banos.id_unidad')->where('mantenimientos_banos.id_unidad',$edificio)->select('mantenimientos_generales.nombre as nombreMa','limpieza_banos.created_at','mantenimientos_banos.nombre as nombreBa');
        }

        //$control_limp = $control_limp->where('id_piso', $request['id'])->get();

        $control_limp = $control_limp->addSelect('limpieza_banos.*')->get();




        $nombre_piso  = Mantenimientos_Generales::all();

        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();

      //dd($firma);
        // dd($request->all());


        Excel::create('REGISTRO DE LIMPIEZA Y DESINFECCION DE BAÑOS Vers. 0.1', function ($excel) use ($control_limp, $request, $empresa, $tipo, $insumos, $productos, $piso) {
            $excel->sheet('REPORTE LIMPIEZA', function ($sheet) use ($request, $control_limp, $empresa, $tipo, $insumos, $productos,$piso) {

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
                $sheet->mergeCells('C1:K1');
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
                $sheet->mergeCells('C2:K2');
                $sheet->cell('C2', function ($cell) {
                    $cell->setValue('REGISTRO DE LIMPIEZA Y DESINFECCION DE BAÑOS');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L1:L2');
                $sheet->cell('L1', function ($cell) {
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
                    $cell->setValue('BAÑO');
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
                    $cell->setValue('DOTACIÓN ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('J3:J4');
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('K3:K4');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('L3:L4');
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIONES ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                //$n= 1;
                $i = 5;

                foreach ($control_limp as $value) {



                      $firma     = Firma_Servicios_Generales::where('id_usuario', $value->responsable)->first();

                    //dd($value);
                      $sheet->cell('A' . $i, function ($cell) use ($value) {
                          $cell->setValue(date('Y-m-d', strtotime($value->fecha)));
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
                        if(isset($value->nombreBa)){
                            $cell->setValue($value->nombreBa);
                        }else{
                            $cell->setValue('');
                        } 


                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        if (is_null($value->frecuencia1)) {
                            $cell->setValue(" ");
                        } else {
                            $cell->setValue(date('H:i', strtotime($value->frecuencia1)));
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

                    if ($value->limpieza == 1) {
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
                    if ($value->limpieza == 2) {
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
                        $produc = InsumosUtilizadosArea::where('id_limpieza_area',$value->id)->get();


                    foreach ($produc as $prod) {
                        $productos = $productos .isset($prod->insumos_limpieza) ?  "-{$prod->insumos_limpieza->nombre}" : '';
                    }


                    $sheet->cell('H' . $i, function ($cell) use ($productos) {
                        // manipulate the cel
                        $cell->setValue($productos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });


                    $insumos = "";

                        $limBa = Insumos_utilizados_banos::where('id_limpieza_banos',$value->id)->get();
                        foreach($limBa as $insu){
                            $insumos = $insumos . "-{$insu->dotacion->nombre}";
                        }
                    $sheet->cell('I' . $i, function ($cell) use ($insumos) {
                        // manipulate the cel
                        $cell->setValue($insumos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $user = User::where('id',$value->responsable)->first();

                    $sheet->cell('J' . $i, function ($cell) use ($user) {
                        // manipulate the cel
                        $cell->setValue($user->nombre1 . ' ' . $user->apellido1 . ' ' . $user->apellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('K' . $i);
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    if(!is_null($firma)){
                      $objDrawing->setPath(base_path() . '/storage/app/avatars/' . $firma->nombre);
                      $objDrawing->setCoordinates('K'.$i);
                      $objDrawing->setWidthAndHeight(130,40);
                      $objDrawing->setWorksheet($sheet);
                    }
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->observacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                  //  $n++;
                    $i++;
                }
                /*****FIN DE TITULOS DEL EXCEL***********/
                $sheet->mergeCells('A' . $i . ':L' . $i);
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
                    $cell->setValue('TIPO DE DESINFECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('E' . $i . ':I' . $i);
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCURRENTE ¨X');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('J' . $i . ':L' . $i);
                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TERMINAL ¨X¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
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

    public function modal_foto(Request $request)
    {
        $foto = Limpieza_Banos::where('id', $request['id'])->first();
        //dd($request);
        return view('servicios_generales.limpieza_banos.modal_foto', ['foto' => $foto, 'tipo' =>$request['tipo']]);
    }
    public function vistareportes(Request $request)
    {
        //$control_limp = Limpieza_Banos::where('id', $request['id'])->first();
        //$pisoBa = Mantenimientos_Banos::where('id_unidad', $control_limp->id_piso)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $piso = Mantenimientos_Banos::where('estado',1)->get();
        $edificio = Mantenimientos_Generales::where('estado',1)->get();

        //$nombre_piso = Mantenimientos_Generales::where('estado', '1')->get();

        $encargados = User::where('id_tipo_usuario', '24')->get();

        return view('servicios_generales.limpieza_banos.nuevo_reporte', [ 'encargados' => $encargados,'piso'=>$piso,'edificio'=>$edificio ]);
    }
}
