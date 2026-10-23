<?php

namespace Sis_medico\Http\Controllers\hospital;

use  Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Cama;
use Sis_medico\Paciente;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaPacienteLog;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Producto;
use Sis_medico\Imagen;
use Sis_medico\Evolucion_Habitacion;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Nota_Enfermeria;
use Sis_medico\Piso;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\User;
use Sis_medico\Tipo_Habitacion;
use Sis_medico\Hospital_Refrigerio;
use Sis_medico\Log_Agenda;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;

class CuartoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cuartos()
    {

        $muestra = Habitacion::all();
        $muestra2 = Cama::all();
        $muestra3 = CamaTransaccion::all();
        $pisos = Piso::all();
        $simples = Habitacion::where('id_tipo', '=', 1);
        $enespera = Ho_Traspaso_Sala008::where('estado', 1)->get();
        //dd($enespera);
        return view('hospital/habitacion/cuartos', ['muestra' => $muestra, 'muestra2' => $muestra2, 'muestra3' => $muestra3, 'pisos' => $pisos, 'simples' => $simples, 'enespera' => $enespera]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cuartos_habitacion()
    {

        $muestra = Habitacion::all();
        $muestra2 = Cama::all();
        $muestra3 = CamaTransaccion::all();
        $piso = Piso::all();
        $simples = Habitacion::where('id_tipo', '=', 1);

        $enhabitacion = Ho_Traspaso_Sala008::where('estado', 2)->get();
        //dd($enespera);
        return view('hospital/habitacion/cuartos_habitacion', ['muestra' => $muestra, 'muestra2' => $muestra2, 'muestra3' => $muestra3, 'piso' => $piso, 'simples' => $simples, 'enhabitacion' => $enhabitacion]);
    }

    public function autocompletarmodal(Request $request)
    {

        $codigo = $request['term'];
        $data      = array();

        //print_r($request)
        $medicinas = DB::table('hospital_producto')->where('nombre', 'like', '%' . $codigo . '%')->get();
        foreach ($medicinas as $value) {
            $data[] = array('value' => $value->nombre);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function agregar_descripcion(Request $request)
    {
        //return $request['id_pedido'];
        $medicamento    = $request['medicamento'];
        $data      = null;
        $productos = DB::table('hospital_producto')->where('nombre', $medicamento)->first();

        if ($productos != '[]') {

            $data = $productos->descripcion;
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function autocompletarmodal2(Request $request)
    { {
            $codigo    = $request['codigo'];
            $data      = null;
            $productos = DB::table('hospital_producto')->where('id', $codigo)->first();
            if (!is_null($productos)) {
                return ['value' => $productos->nombre];
            } else {
                return ['value' => 'no'];
            }
        }
    }
    public function admcuarto($id, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $pacienteid = Paciente::find($id);
        $log = CamaPaciente::where('id_cama', '=', $id)->where('id_paciente', $id_paciente)->get();
        $fecha = CamaPaciente::where('id_cama', '=', $id)->where('id_paciente', $id_paciente)->first();
        $cama = Cama::where('id', $id)->first();
        $enfermero = nota_enfermeria::where('id_paciente', '=', $id_paciente)->get();

        // ->rightJoin('nota_enfermeria', 'evolucion_habitacion.id_evolucion', '=', 'nota_enfermeria.id_evolucion')

        //->join('evolucion_habitacion','evolucion_habitacion.id_paciente','=','nota_enfermeria.id_evolucion')
        //->join('evolucion_habitacion', 'nota_enfermeria.id_evolucion', '=', 'evolucion_habitacion.id_paciente')
        //->select('nota_enfermeria.*', 'evolucion_habitacion.id_paciente')

        //hacer esto en la vista con la variable pacientito
        $habitacion = Habitacion::where('id', $cama->id_habitacion)->first();
        //dd($log);
        $id_habitacion = $habitacion->id_tipo;
        $id_cama = $id;
        $pacientito = Evolucion_Habitacion::where('id_paciente', '=', $id_paciente)->get();

        return view('hospital/habitacion/admcuarto', ['pacientito' => $pacientito, 'log' => $log, 'fecha' => $fecha, 'id_cama' => $id_cama, 'id_habitacion' => $id_habitacion, 'pacienteid' => $pacienteid, 'enfermero' => $enfermero]);
    }

    public function admasigncuarto($id, $idc, $ids)
    {

        $habitacion = Habitacion::find($id);
        $cama = Cama::where('id_habitacion', '=', $id)->get();
        $tipo = $habitacion->tipo->nombre;
        $id_tipo = $habitacion->tipo->id;
        $camatransaccion = CamaTransaccion::where('id_cama', '=', $ids)->get();
        $nombre = Imagen::where('id', '=', $idc)->get();
        foreach ($nombre as $value) {
            $url = $value->url_img;
        }
        foreach ($cama as $value) {
            $codigo = $value->codigo;
        }
        $id_cama = $ids;
        $otracama = Cama::where('id', '=', $ids)->get();
        foreach ($otracama as $value) {
            $estado = $value->estado;
        }
        return view('hospital/habitacion/admasigncuarto', ['tipo' => $tipo, 'url' => $url, 'id_tipo' => $id_tipo, 'codigo' => $codigo, 'id_cama' => $id_cama, 'estado' => $estado]);
    }
    public function provincias()
    {
        $evolucion = evolucion_producto::all();
        $evolucion_producto = evolucion::with('evolucion_producto')->get();
        return view('hospital/habitacion/admasigncuarto', ['evolucion' => $evolucion, 'evolucion_producto' => $evolucion_producto]);
    }

    //AUTOCOMPLETA LOS DATOS PRINCIPALES_DEL_PACIENTE
    public function auto(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }

    //AUTOCOMPLETA LOS DATOS DE FILIACION
    public function auto2(Request $request)
    {
        $nombre_encargado = $request['nombre'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, telefono1, telefono2, id_seguro, sexo, id, estadocivil,cedulafamiliar,religion,fecha_nacimiento,trabajo,lugar_nacimiento,alergias,ciudad,gruposanguineo,direccion,antecedentes_pat, antecedentes_fam
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array(
                'value' => $product->completo,
                'fecha' => $product->fecha_nacimiento, 'telefono1' => $product->telefono1, 'telefono2' => $product->telefono2,
                'seguro' => $product->id_seguro, 'sexo' => $product->sexo, 'id' => $product->id, 'estadoc' => $product->estadocivil, 'cedula' => $product->cedulafamiliar,
                'religion' => $product->religion, 'alergia' => $product->alergias, 'lugar_nacimiento' => $product->lugar_nacimiento, 'ciudad' => $product->ciudad,
                'grupos' => $product->gruposanguineo, 'direccion' => $product->direccion, 'antp' => $product->antecedentes_pat, 'antf' => $product->antecedentes_fam
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $data;
    }

    //ASIGNACION_PACIENTE_A_UNA_CAMA
    public function cuartog(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_paciente     = $request['id_paciente'];
        $observaciones   = $request['observacion'];
        $id_tipos        = $request['id_tipo'];
        $id_tipo         = (int)$id_tipos;
        $id_cama         = $request['id_cama'];
        $id_camita       = (int) $id_paciente;
        $camalogvalidate = CamaPaciente::where('id_paciente', '=', $id_camita)->get();
        $validarpaciente = 1;
        if (($camalogvalidate) == "[]") {
            $validarpaciente = 1;
        } else {
            foreach ($camalogvalidate as $value) {
                $validarpaciente = $value->id_paciente;
            }
        }

        $estado        = $request['estado'];
        $estadoint     = (int)$estado;
        $idmejorado    = (int)$id_cama;
        $idusuario     = Auth::user()->id;
        $input = [
            'id_paciente' => $id_paciente,
            'observacion' => $observaciones,
            'estado' => '1',
            'id_usuario' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_cama' => $id_cama,
            'observaciones' => '//SE REGISTRÓ CAMA AL PACIENTE DESDE SISTEMA INICIO//'
        ];
        $input2 = [
            'id_paciente' => $id_paciente,
            'observacion' => $observaciones,
            'estado' => '1',
            'id_usuario' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_cama' => $id_cama,
            'observaciones' => '//SE REGISTRÓ CAMA AL PACIENTE DESDE SISTEMA INICIO//'
        ];

        $input_cama = [

            'estado' => '3',
            'id_usuario' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        if ($id_paciente != null) {
            if ($validarpaciente != $id_paciente) {
                $cama = Cama::find($idmejorado);
                $transaccion = CamaTransaccion::where('id_cama', '=', $idmejorado)->get();
                $actualizarcama = $cama->update($input_cama);
                foreach ($transaccion as $value) {
                    $camatransaccion = [
                        'id_paciente' => $id_paciente,
                        'id_imagen' => '3',
                    ];
                    $comofue = $value->update($camatransaccion);

                    if ($id_tipo == 3) {
                        $camatransaccion = [
                            'id_paciente' => $id_paciente,
                            'id_imagen' => '7',
                        ];
                        $comofue = $value->update($camatransaccion);
                    } elseif ($id_tipo == 5) {
                        $camatransaccion = [
                            'id_paciente' => $id_paciente,
                            'id_imagen' => '11',
                        ];
                        $comofue = $value->update($camatransaccion);
                    }
                }
                CamaPaciente::insertGetId($input);
                CamaPacienteLog::insertGetId($input2);
                return "EL PACIENTE FUÉ REGISTRADO EN LA CAMA";
            }
            return "EL PACIENTE YA SE ENCUENTRA REGISTRADO REINGRESE DE NUEVO";
        }
        return "DATOS VACIOS POR FAVOR REINGRESE DE NUEVO";
    }
    //LIBERAR CAMA DE LA HABITACION
    public function liberar(Request $request)
    {
        dd($request->all());
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        $id_paciente     = $request['indetificacion'];
        $id_tipos        = $request['id_tipo'];
        $id_tipo         = (int)$id_tipos;
        $id_cama         = $request['id_cama'];
        $idmejorado      = (int)$id_cama;
        $id_hospitalizacion     = $request->id_hospitalizacion;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $hospital = Ho_Hospitalizacion::find($id_hospitalizacion);
        $hospital->update([
            'estado'          => '2', //DESOCUPA
            'ip_modificacion' => $ip_cliente,
        ]);


        $cama = Cama::find($idmejorado);
        $transaccion     = CamaTransaccion::where('id_cama', '=', $idmejorado)->get();
        $idusuario       = Auth::user()->id;
        $input = [
            'id_paciente' => $id_paciente,
            'observacion' => '',
            'estado' => '3',
            'id_usuario' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $idusuario,
            'id_cama' => $id_cama,
            'observaciones' => '//SE DESOCUPO LA CAMA//'
        ];
        $input_cama = [
            'estado' => '2',
            'id_usuario' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $idusuario,
            'updated_at' => null
        ];

        $actualizarcama = $cama->update($input_cama);
        foreach ($transaccion as $value) {
            $camatransaccion = [
                'id_imagen' => '2',
                'updated_at' => null
            ];
            $comofue = $value->update($camatransaccion);

            if ($id_tipo == 3) {
                $camatransaccion = [
                    'id_imagen' => '6',
                    'updated_at' => null
                ];
                $comofue = $value->update($camatransaccion);
            } elseif ($id_tipo == 5) {
                $camatransaccion = [
                    'id_imagen' => '10',
                    'updated_at' => null
                ];
                $comofue = $value->update($camatransaccion);
            }
        }
        //dd("aa");
        //CamaPaciente::insertGetId($input);
        return redirect()->route('hospital.gcuartos');
    }
    public function freehabitation(Request $request)
    {
        //free all habitation for module hospital at 12 ago 2021
        $cama = Cama::where('estado', '<>', '0')->get();
        $idusuario       = Auth::user()->id;
        foreach ($cama as $c) {
            $id_paciente     = $request['id_paciente'];
            $id_tipo =      $c->habitacion->tipo->id;
            $id_cama         = $c->id;
            $idmejorado      = (int)$id_cama;
            $cama = Cama::find($c->id);
            $transaccion     = CamaTransaccion::where('id_cama', $idmejorado)->get();
            $input = [
                'id_paciente' => $id_paciente,
                'observacion' => '',
                'estado' => '3',
                'id_usuario' => $idusuario,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $idusuario,
                'id_cama' => $id_cama,
                'observaciones' => '//SE DESOCUPO LA CAMA//'
            ];
            $input_cama = [
                'estado' => '1',
                'id_usuario' => $idusuario,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $idusuario,
                'updated_at' => null
            ];
            $actualizarcama = $cama->update($input_cama);
            foreach ($transaccion as $value) {
                $camatransaccion = [
                    'id_imagen' => '2',
                    'updated_at' => null
                ];
                $s = $value->update($camatransaccion);

                if ($id_tipo == 3) {
                    $camatransaccion = [
                        'id_imagen' => '6',
                        'updated_at' => null
                    ];
                    $s = $value->update($camatransaccion);
                } elseif ($id_tipo == 5) {
                    $camatransaccion = [
                        'id_imagen' => '10',
                        'updated_at' => null
                    ];
                    $s = $value->update($camatransaccion);
                }
            }
            CamaPaciente::insertGetId($input);
        }

        return redirect()->route('hospital.gcuartos');
    }
    //MODAL PRESCRIPCION DOCTOR
    public function modalprescripcion($id, Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request->all();
        $examenes = hospital_producto::all();
        $pacienteid = Paciente::find($id);
        return view('hospital/habitacion/modalprescripcion', ['pacienteid' => $pacienteid, 'examenes' => $examenes]);
    }
    //Vista Costo generados por paciente
    public function costo($id, $id_cama)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        //dd($request->all());
        //$users  = CamaPaciente::where('id_paciente','=',$id)->get();
        $id_paciente = $id;
        //dd($id_paciente);
        $tipo = DB::table('tipo_habitacion')->get();
        $categoria_habitacion = DB::table('cama as c')
            ->join('cama_paciente as cp', 'cp.id_cama',  'c.id')
            ->join('paciente as p', 'p.id', 'cp.id_paciente')
            ->where('cp.id_cama', $id_cama)
            ->where('cp.id_paciente', $id)
            ->select('c.codigo', 'cp.id_paciente', 'p.nombre1', 'p.apellido1', 'cp.updated_at')->get();
        //dd($categoria_habitacion);
        $nombres = Hospital_Refrigerio::where('id_paciente', '=', $id)->get();
        //dd($nombres);
        return view('hospital/habitacion/costo', ['categoria_habitacion' => $categoria_habitacion, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'nombres' => $nombres]);
    }
    //Formulario de costo generado por paciente
    public function costos_generados($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/habitacion/costo', ['users ' => $users]);
    }
    //AGSINAR MEDICAMENTO POR PARTE DEL DOCTOR Y GUARDAR EN LA BD
    public function guardarm(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        //dd($request->all());
        Evolucion_Habitacion::create([
            'evolucion_dr'      => $request['evolucion_dr'],
            'medicamento'       => $request['medicamento'],
            'prescripcion_dr'   => $request['descripcion'],
            'id_paciente'       => $request['id_paciente'],

        ]);

        return back();
    }
    public function paciente()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
        }
        return view('hospital/habitacion/paciente');
    }
    //MODAL ENFERMERIA VISTA
    public function modalenfermeria($id, Request $request, $id_evolucion)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $pacienteid = Paciente::find($id);
        $infe = Evolucion_Habitacion::all();
        //$tipo_habitacion=Evolucion_Habitacion::where('id_cama','=',$id)->get();
        $prescri = Evolucion_Habitacion::where('id_paciente', '=', $id)->get();
        return view('hospital/habitacion/modalenfermeria', ['prescri' => $prescri, 'infe' => $infe, 'pacienteid' => $pacienteid, 'id_evolucion' => $id_evolucion]);
    }
    //MEDICAMENTO SUMINISTRADO POR ENFERMERIA
    public function suministar(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        Nota_Enfermeria::create([
            'cantidad_suministrada'  => $request['cantidad_suministrada'],
            'evolucion_enf'          => $request['evolucion_enf'],
            'id_evolucion'           => $request['id_evu'],
            'id_paciente'            => $request['id_paciente']
        ]);

        return back();
    }
    public function modalservicio(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $servicio = Paciente::find($id);
        return view('hospital/habitacion/modalservicios', ['id' => $id, 'servicio' => $servicio]);
    }
    public function salvar(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        Hospital_Refrigerio::create([
            'id_paciente'       => $request['pacienterefri'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,
            'desayuno' => $request['desayunos'],
            'cant_desayuno' => $request['cantidad_desayuno'],
            'precio_desayuno' => $request['precio_desayuno'],
            'almuerzo' => $request['almuerzo'],
            'cant_almuerzo' => $request['cant_almuerzo'],
            'precio_almuerzo' => $request['precio_almuerzo'],
            'cena' => $request['cena'],
            'cant_cena' => $request['cantidad_cena'],
            'precio_cena' => $request['precio_cena'],
            'bebidas' => $request['bebidas'],
            'cant_bebidas' => $request['cantidad_bebi'],
            'precio_bebidas' => $request['precio_bebi'],


        ]);
        return back();
    }
    public function getSources(Request $request)
    {
        //dd($request->all());
        $id = $request['id'];
        $status = $request['status'];
        if ($status == '1') {
            $o = Ho_Traspaso_Sala008::find($id);
            if (is_null($o)) {
                return response()->json('error');
            }
            $arraySource['paciente'] = $o->paciente->apellido1 . ' ' . $o->paciente->apellido2 . ' ' . $o->paciente->nombre1;
            $arraySource['fecha'] = date('d/m/Y H:s', strtotime($o->fecha));
            $arraySource['id'] = $o->paciente->id;
            return view('hospital.habitacion.seleccion_paciente', ['paciente' => $arraySource['paciente'], 'fecha' => $arraySource['fecha'], 'id' => $arraySource['id'], 'traspaso' => $o]);
            //return response()->json($arraySource);
        } else { //ASIGNA CMA A HOSPITALIZACION 
            //dd("aqui");
            $s = Ho_Hospitalizacion::where('id_cama', $id)->whereDate('fecha', date('Y-m-d'))->first();
            if (is_null($s)) {
                return response()->json(['state' => 'success']);
            }
            //dd($s);
            $o = Ho_Traspaso_Sala008::find($s->id_traspaso);
            if (!is_null($o)) {
                if ($o->estado == 1) {
                    $o->estado = 2;
                    $o->save();
                    return response()->json(['state' => 'success']);
                } else {

                    return response()->json(['state' => 'error']);
                }
            } else {
                return response()->json(['state' => 'error']);
            }
        }
    }
    public function modal_paciente(Request $request)
    {
        $id = $request['id'];
        $cama = Cama::find($id);
        //dd($cama);
        $enespera = Ho_Traspaso_Sala008::where('estado', '<>', '0')->where('id_tipo', '<>', '5')->get();
        return view('hospital.habitacion.modal_paciente', ['enespera' => $enespera, 'cama' => $cama]);
    }
    public function asignar_paciente($id, $y)
    {
        $cama = Cama::find($id);
        $traspaso = Ho_Traspaso_Sala008::find($y);
        $paciente = Paciente::find($traspaso->id_paciente);
        return view('hospital.habitacion.asignar_paciente', ['cama' => $cama, 'paciente' => $paciente, 'traspaso' => $traspaso]);
    }
    public function save_paciente(Request $request)
    {
        //dd($request->all());
        $p = $request['id_paciente'];
        $traspasos = $request['traspaso'];
        $s = $request['seguro'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        if (!is_null($p)) {
            $paciente = Paciente::find($request['id_paciente']);
            if (!is_null($paciente)) {
                $hospitalizacion = Ho_Hospitalizacion::where('id_paciente', $p)->whereDate('fecha', date('Y-m-d'))->first();
                if (!is_null($hospitalizacion)) {

                    return response()->json(['state' => 'El paciente ya se encuentra asignado']);
                } else {
                    $cama = Cama::find($request['id_cama']);
                    //$cama->estado = 2;
                    //$cama->save();
                    $traspaso = Ho_Traspaso_Sala008::find($traspasos);
                    $solicitud = $traspaso->solicitud;
                    $agenda    = $solicitud->agenda;
                    $agenda->update([
                        'id_sala' => $traspaso->id_sala,
                    ]);
                    $traspaso->estado = 2;
                    $traspaso->save();
                    $transaccion = CamaTransaccion::where('id_cama', '=', $cama->id)->first();
                    if (is_null($transaccion)) {
                        $id_transaccion = CamaTransaccion::insertGetId([

                            'estado_cama'       => 'ocupada',
                            'id_imagen'         => '3',
                            //'id_usuario'        => '1',
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_cama'           => $request['id_cama'],
                            'id_paciente'       => $paciente->id,
                            'fecha_ingreso'     => date('Y-m-d H:s'),
                        ]);
                    } else {

                        $camatransaccion = [
                            'id_paciente' => $paciente->id,
                            'id_imagen'   => '3',
                            'estado_cama' => 'ocupada',
                        ];
                        $transaccion->update($camatransaccion);

                        /*if ($cama->habitacion->tipo->id == 3) {
                            $camatransaccion = [
                                'id_paciente' => $paciente->id,
                                'id_imagen' => '7',
                            ];
                            $actualizar = $value->update($camatransaccion);
                        } elseif ($cama->habitacion->tipo->id == 5) {
                            $camatransaccion = [
                                'id_paciente' => $paciente->id,
                                'id_imagen' => '11',
                            ];
                            $actualizar = $value->update($camatransaccion);
                        }*/
                    }
                    Ho_Hospitalizacion::create([
                        'id_paciente' => $paciente->id,
                        'fecha' => date('Y-m-d H:s'),
                        'id_habitacion' => $request['id_habitacion'],
                        'detalle' => $request['observacion'],
                        'id_cama' => $request['id_cama'],
                        'id_traspaso' => $traspasos,
                        'id_usuariomod' => $idusuario,
                        'id_usuariocrea' => $idusuario,
                        'ip_modificacion' => $idusuario,

                    ]);
                    return response()->json(['state' => 'Guardado Correctamente']);
                }
            }
            return response()->json(['state' => 'error', 'request' => $request->all()]);
        }
        return response()->json(['state' => 'error', 'request' => $request->all()]);
    }
    public function show_cama($id, $id_traspaso, Request $request)
    {
        $cama = Cama::find($id);

        $hospitalizacion = Ho_Hospitalizacion::find($id_traspaso);
        $paciente = Paciente::find($hospitalizacion->id_paciente);

        return view('hospital.habitacion.detalle', ['cama' => $cama, 'hospitalizacion' => $hospitalizacion, 'paciente' => $paciente]);
    }
    public function modal_quirofano(Request $request)
    {
        $sala = Sala::where('estado', '1')->get();
        $id_tipo = $request->id_tipo;
        $id_cama = $request->id_cama;
        $id_hospitalizacion = $request->id_hospitalizacion;
        $id_paciente = $request->id_paciente;
        $users = User::where('id_tipo_usuario', 3)->get();
        $procedimientos = Procedimiento::all();
        return view('hospital.habitacion.modal_cirugia', ['sala' => $sala, 'id_tipo' => $id_tipo, 'id_cama' => $id_cama, 'id_paciente' => $id_paciente, 'id_hospitalizacion' => $id_hospitalizacion, 'users' => $users, 'procedimientos' => $procedimientos]);
    }
    public function cirugia(Request $request)
    {
        //aqui guardo en la modal de habitacion
        //dd($request->all());
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        //$this->agenda($request);
        $this->crear_procedimiento($request);
        $this->liberar($request);
        $hospitalizacion = Ho_Hospitalizacion::find($request['id_hospitalizacion']);
        $traspaso = Ho_Traspaso_Sala008::find($hospitalizacion->id_traspaso);
        $traspaso->update(['estado' => '3']);
        //dd($traspaso);
        $solicitud = Ho_Solicitud::find($traspaso->id_solicitud);
        $sol_log = [
            'id_ho_solicitud'       => $solicitud->id,
            'estado_paso'           => '3',
            'id_agenda'             => $solicitud->id_agenda,
            'fecha_ingreso'         => date('Y-m-d'),
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
        ];
        $hospitalizacion->estado = 2;
        $hospitalizacion->id_usuariomod = $idusuario;
        $hospitalizacion->save();

        //$log_soli = Ho_Log_Solicitud::create($sol_log);

        $solicitud->estado_paso = $request->paso;
        $solicitud->id_usuariomod = $idusuario;
        $solicitud->save();

        return redirect()->route('hospital.gcuartos');
    }
    public function agenda(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_doctor    = Auth::user()->id;
        if (!is_null($request['id_doctor'])) {
        }
        $id_paciente = $request['id_paciente'];
        $paciente = paciente::find($id_paciente);
        $user = User::find($id_paciente);
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $input_agenda = [
            'fechaini'        => $request['fecha'],
            'fechafin'        => $request['fechafin'],
            'id_paciente'     => $id_paciente,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '1',
            'estado_cita'     => '1',
            'id_empresa'      => '1391707460001',
            'espid'           => $espid,
            'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);


        $consulta_crear_new = [
            'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($consulta_crear_new);

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '1',
            'estado_cita'     => '1',
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'estado'          => '4',
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];

        Log_Agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_seguro'       => $paciente->id_seguro,
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => '0',
            'fecha_ingreso'       => ' ',
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,

        ];
        $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);

        $input_child_pugh = [
            'id_hc_evolucion'       => $id_evolucion,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
        ];

        $id_child = hc_child_pugh::create($input_child_pugh);

        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);
    }
    public function crear_procedimiento(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->id_paciente);
        $sala           = \Sis_medico\Sala::find($request['sala']);

        $hospitalizacion = Ho_Hospitalizacion::find($request['id_hospitalizacion']);
        $traspaso = Ho_Traspaso_Sala008::find($hospitalizacion->id_traspaso);
        $solicitud = Ho_Solicitud::find($traspaso->id_solicitud);

        $procedimiento_crear_new = [
            'anterior'        => 'CIRUJIA HOSPITAL',
            'nuevo'           => 'CIRUJIA HOSPITAL',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini'         => $request['fecha'],
                'fechafin'         => $request['fechafin'],
                'id_paciente'      => $paciente->id,
                'id_doctor1'       => $request->doctor,
                'proc_consul'      => '1',
                'estado_cita'      => '0',
                'id_empresa'       => '1391707460001',
                'espid'            => '4',
                'observaciones'    => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_seguro'        => $paciente->id_seguro,
                'estado'           => '1',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $id_doctor,
                'id_usuariomod'    => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala'          => $sala->id,
                'ho_tipo'          => $request->ho_tipo,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);
            //return $id_agenda;

            $txt_pro = '';
            foreach ($procedimientos as $value) {

                if ($procedimientop != $value) {
                    $txt_pro = $txt_pro . '+' . $value;
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $id_doctor,
                        'id_usuariomod'    => $id_doctor,
                    ]);
                }
            }

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'CIRUGIA HOSPITAL',
                'id_doctor1'      => $request->doctor,
                'descripcion'     => 'CIRUGIA HOSPITAL',
                'campos_ant'      => 'PRO: ' . $procedimientop . $txt_pro,

                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            $idusuario = $id_doctor;
            Log_Agenda::create($input_log);
            $input_historia = [

                'parentesco'      => $paciente->parentesco,
                'id_usuario'      => $paciente->id_usuario,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $paciente->id,
                'id_seguro'       => $paciente->id_seguro,

                'id_doctor1'      => $request->doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_pentax = [
                'id_agenda'       => $id_agenda,
                'hcid'            => $id_historia,
                'id_sala'         => $sala->id,
                'id_doctor1'      => $request->doctor,
                'id_seguro'       => $paciente->id_seguro,
                'observacion'     => "HOSPITAL CIRUGIA",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];

            $id_pentax = Pentax::insertGetId($input_pentax);

            $list_proc = '';
            foreach ($procedimientos as $value) {
                $input_pentax_pro2 = [
                    'id_pentax'        => $id_pentax,
                    'id_procedimiento' => $value,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro2);
                $list_proc = $list_proc . "+" . $value;
            }

            $input_log_px = [
                'id_pentax'       => $id_pentax,
                'tipo_cambio'     => "HOSPITAL CIRUGIA",
                'descripcion'     => "EN ESPERA",
                'estado_pentax'   => '0',
                'procedimientos'  => $list_proc,
                'id_doctor1'      => $request->doctor,
                'observacion'     => "HOSPITAL CIRUGIA",
                'id_sala'         => $sala->id,
                'id_seguro'       => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc'                 => $id_historia,
                'id_seguro'             => $paciente->id_seguro,
                'id_doctor_examinador'  => $idusuario,
                'id_doctor_examinador2' => $idusuario,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $input_hc_protocolo = [
                'fecha'                => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio'          => date('H:i:s'),
                'hora_fin'             => date('H:i:s'),
                'estado_final'         => ' ',
                'ip_modificacion'      => $ip_cliente,
                'hcid'                 => $id_historia,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'tipo_procedimiento'   => '3',
            ];
            hc_protocolo::insert($input_hc_protocolo);

            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
            $arr_solicitud = [
                'id_paciente'           => $paciente->id,
                'id_agenda'             => $id_agenda,
                'id_seguro'             => $paciente->id_seguro,
                'fecha_ingreso'         => date('Y-m-d H:i:s'),
                'estado_paso'           => '4',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $solicitud->update($arr_solicitud);

            $solicitud_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => '4',
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $log = Ho_Log_solicitud::create($solicitud_log);

            return "ok";
        }

        return "Ingrese el Procedimiento";
    }
    public function modal_imagenes(Request $request)
    {
        return view('hospital.habitacion.modal_imagenes', ['users' => User::where('id_tipo_usuario', 3)->get(), 'procedimientos' => Procedimiento::where('id_grupo_procedimiento', 20)->get(), 'sala' => Sala::where('estado', '1')->get(), 'id_tipo' => $request['id_tipo'], 'id_cama' => $request['id_cama'], 'id_hospitalizacion' => $request['id_hospitalizacion'], 'indetificacion' => $request['indetificacion']]);
    }

    public function guardar_imagenes(Request $request)
    {
        //dd($request->all());
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        //$this->agenda($request);
        $this->crear_imagen($request);
        $this->liberar($request);
        $hospitalizacion = Ho_Hospitalizacion::find($request['id_hospitalizacion']);
        $traspaso = Ho_Traspaso_Sala008::find($hospitalizacion->id_traspaso);
        $traspaso->update(['estado' => '3']);
        //dd($traspaso);
        $solicitud = Ho_Solicitud::find($traspaso->id_solicitud);
        $sol_log = [
            'id_ho_solicitud'       => $solicitud->id,
            'estado_paso'           => '3',
            'id_agenda'             => $solicitud->id_agenda,
            'fecha_ingreso'         => date('Y-m-d'),
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
        ];
        $hospitalizacion->estado = 2;
        $hospitalizacion->id_usuariomod = $idusuario;
        $hospitalizacion->save();

        //$log_soli = Ho_Log_Solicitud::create($sol_log);

        $solicitud->estado_paso = $request->paso;
        $solicitud->id_usuariomod = $idusuario;
        $solicitud->save();
        return redirect()->route('hospital.gcuartos');
    }

    public function crear_imagen(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->id_paciente);
        $sala           = \Sis_medico\Sala::find($request['sala']);

        $hospitalizacion = Ho_Hospitalizacion::find($request['id_hospitalizacion']);
        $traspaso = Ho_Traspaso_Sala008::find($hospitalizacion->id_traspaso);
        $solicitud = Ho_Solicitud::find($traspaso->id_solicitud);

        $procedimiento_crear_new = [
            'anterior'        => 'IMAGEN HOSPITAL',
            'nuevo'           => 'IMAGEN HOSPITAL',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini'         => $request['fecha'],
                'fechafin'         => $request['fechafin'],
                'id_paciente'      => $paciente->id,
                'id_doctor1'       => $request->doctor,
                'proc_consul'      => '1',
                'estado_cita'      => '0',
                'id_empresa'       => '1391707460001',
                'espid'            => '7',
                'observaciones'    => 'IMAGEN CREADO POR EL DOCTOR',
                'id_seguro'        => $paciente->id_seguro,
                'estado'           => '1',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $id_doctor,
                'id_usuariomod'    => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala'          => $sala->id,
                'ho_tipo'          => $request->ho_tipo,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);
            //return $id_agenda;

            $txt_pro = '';
            foreach ($procedimientos as $value) {

                if ($procedimientop != $value) {
                    $txt_pro = $txt_pro . '+' . $value;
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $id_doctor,
                        'id_usuariomod'    => $id_doctor,
                    ]);
                }
            }

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'IMAGEN HOSPITAL',
                'id_doctor1'      => $request->doctor,
                'descripcion'     => 'IMAGEN HOSPITAL',
                'campos_ant'      => 'PRO: ' . $procedimientop . $txt_pro,

                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            $idusuario = $id_doctor;
            Log_Agenda::create($input_log);
            $input_historia = [

                'parentesco'      => $paciente->parentesco,
                'id_usuario'      => $paciente->id_usuario,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $paciente->id,
                'id_seguro'       => $paciente->id_seguro,

                'id_doctor1'      => $request->doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_pentax = [
                'id_agenda'       => $id_agenda,
                'hcid'            => $id_historia,
                'id_sala'         => $sala->id,
                'id_doctor1'      => $request->doctor,
                'id_seguro'       => $paciente->id_seguro,
                'observacion'     => "HOSPITAL IMAGEN",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];

            $id_pentax = Pentax::insertGetId($input_pentax);

            $list_proc = '';
            foreach ($procedimientos as $value) {
                $input_pentax_pro2 = [
                    'id_pentax'        => $id_pentax,
                    'id_procedimiento' => $value,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro2);
                $list_proc = $list_proc . "+" . $value;
            }

            $input_log_px = [
                'id_pentax'       => $id_pentax,
                'tipo_cambio'     => "HOSPITAL IMAGEN",
                'descripcion'     => "EN ESPERA",
                'estado_pentax'   => '0',
                'procedimientos'  => $list_proc,
                'id_doctor1'      => $request->doctor,
                'observacion'     => "HOSPITAL IMAGEN",
                'id_sala'         => $sala->id,
                'id_seguro'       => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc'                 => $id_historia,
                'id_seguro'             => $paciente->id_seguro,
                'id_doctor_examinador'  => $idusuario,
                'id_doctor_examinador2' => $idusuario,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $input_hc_protocolo = [
                'fecha'                => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio'          => date('H:i:s'),
                'hora_fin'             => date('H:i:s'),
                'estado_final'         => ' ',
                'ip_modificacion'      => $ip_cliente,
                'hcid'                 => $id_historia,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'tipo_procedimiento'   => '2',
            ];
            hc_protocolo::insert($input_hc_protocolo);

            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
            $arr_solicitud = [
                'id_paciente'           => $paciente->id,
                'id_agenda'             => $id_agenda,
                'id_seguro'             => $paciente->id_seguro,
                'fecha_ingreso'         => date('Y-m-d H:i:s'),
                'estado_paso'           => '4',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $solicitud->update($arr_solicitud);

            $solicitud_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => '4',
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $log = Ho_Log_solicitud::create($solicitud_log);

            return "ok";
        }

        return "Ingrese LA Imagen";
    }

    public function agenda_hospital(Request $request, $id_sala)
    {

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        //dd($fecha_hasta);
        if ($fecha_desde == null) {
            $fecha_desde = date('Y-m-d');
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $agendas = Agenda::where('estado', '>', 0)->where('id_sala', $id_sala)
            ->whereBetween('fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();


        $sala = Sala::find($id_sala);

        return view('hospital.master.index', ['agendas' => $agendas, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_sala' => $id_sala, 'sala' => $sala]);
    }
}
