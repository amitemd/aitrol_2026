<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Agenda;
use Sis_medico\Archivo_historico;
use Sis_medico\Empresa;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_anestesiologia_csv;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Record;
use Sis_medico\record_tecnicas_anestesicas;
use Sis_medico\Seguro;
use Sis_medico\TecnicasAnestesicas;
use Sis_medico\Tipo_Anesteciologia;
use Sis_medico\User;

class AnestesiologiaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 7, 9)) == false) {
            return true;
        }
    }

    public function mostrar($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $hc_procedimientos = hc_procedimientos::find($id);
        $id_agenda         = $hc_procedimientos->historia->id_agenda;

        $usuarios       = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->orderBy('apellido1','asc')->get(); //3=DOCTORES;
        $enfermeros     = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->orderBy('apellido1','asc')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado', '1')->orderBy('apellido1','asc')->get(); //9=ANESTESIOLOGO;
        $salas          = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id_agenda)
            ->first();

        $ced_paciente = $agenda->id_paciente;
        $paciente     = Paciente::find($ced_paciente);
        $hca          = DB::table('historiaclinica')
            ->where('id_agenda', '=', $id_agenda)
            ->get();
        $hca_seguro = $hca[0]->id_seguro;
        $hca_id     = $hca[0]->hcid;
        $seguro     = Seguro::find($hca_seguro);

        $hcp = DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = '" . $ced_paciente . "' AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id_agenda . "
                            ORDER BY a.fechaini DESC");

        $ag = Agenda::find($id_agenda);
        $pc = $ag->proc_consul;

        $records = Record::all();

        $archivo_vrf = array();
        if ($hca[0]->verificar == 1) {
            $archivo_historico = Archivo_historico::where('id_historia', $hca[0]->hcid)->where('tipo_documento', 'VRF')->get();
            $archivo_vrf       = $archivo_historico[0];
        }

        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id_agenda)->get();
        if ($pc == 1) {
            $fotos = DB::table('archivo_historico')
                ->where('id_historia', '=', $hca_id)
                ->where('tipo_documento', '<>', 'VRF')
                ->get();
            $procedimientos = DB::table('pentax_procedimiento')
                ->join('procedimiento', 'pentax_procedimiento.id_pentax', '=', 'procedimiento.id')
                ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                ->where('id_pentax', '=', $hca_id)
                ->get();
            $tipo_anesteciologia    = Tipo_Anesteciologia::all();
            $record_anestesiologico = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id)->get();
            if ($record_anestesiologico != '[]') {
                $csv   = hc_anestesiologia_csv::where('id_hc_anestesiologia', '=', $record_anestesiologico[0]->id)->get();
                $datos = record_tecnicas_anestesicas::where('id_hc_anestesiologia', '=', $record_anestesiologico[0]->id)->get();
            } else {
                $input1 = [
                    'id_hc'                => $hca_id,
                    'fecha'                => date('Y-m-d H:i:s'),
                    'id_hc_procedimientos' => $id,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];
                Hc_Anestesiologia::insert($input1);

                $record_anestesiologico = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id)->get();

                //dd('guardo');
                $csv   = array();
                $datos = array();
            }
            $general        = TecnicasAnestesicas::where('tipo_tecnica', '=', 1)->get();
            $conductiva     = TecnicasAnestesicas::where('tipo_tecnica', '=', 2)->get();
            $complicaciones = TecnicasAnestesicas::where('tipo_tecnica', '=', 3)->get();
            $image          = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id)->whereNotNull('url_imagen')->first();
            return view('hc_admision/anestesiologia/show', ['agenda' => $agenda, 'paciente' => $paciente, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'hca' => $hca, 'hcp' => $hcp, 'seguro' => $seguro, 'fotos' => $fotos, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda, 'records' => $records, 'anestesiologos' => $anestesiologos, 'record_anestesilogico' => $record_anestesiologico, 'tipo_anesteciologia' => $tipo_anesteciologia, 'csv' => $csv, 'general' => $general, 'conductiva' => $conductiva, 'datos' => $datos, 'complicaciones' => $complicaciones, 'id' => $id, 'hc_procedimientos' => $hc_procedimientos, 'image' => $image]);
        }
    }

    public function crea_actualiza(Request $request, $hc_id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $request["id_hc_procedimientos"])->get();
        $id             = "1";
        if ($anestesiologia == '[]') {
            $input1 = [
                'id_hc'                      => $hc_id,
                'id_anestesiologo'           => $request["id_anestesiologo"],
                'id_ayudante'                => $request["id_ayudante"],
                'id_instrumentista'          => $request["id_instrumentista"],
                'fecha'                      => date('Y-m-d H:i:s'),
                'diagnostico_preoperatorio'  => $request["diagnostico_preoperatorio"],
                'diagnostico_postoperatorio' => $request["diagnostico_postoperatorio"],
                'duracion_anestesia'         => $request["duracion_anestesia"],
                'duracion_operacion'         => $request["duracion_operacion"],
                'id_hc_procedimientos'       => $request["id_hc_procedimientos"],
                'id_tipoanestesiologia'      => $request["id_tipoanesteciologia"],
                'dextrosa'                   => $request["dextrosa"],
                'cloruro_sodio'              => $request["cloruro_sodio"],
                'lactato_ringer'             => $request["lactato_ringer"],
                'sangre_derivados'           => $request["sangre_derivados"],
                'expansores'                 => $request["expansores"],
                'tecnicas_especiales'        => $request["tecnicas_especiales"],
                'hora'                       => $request["hora"],
                'id_sala'                    => $request["id_sala"],
                'id_guiado'                  => $request["id_guiado"],
                'comentarios'                => $request["comentarios"],
                'sistema_circulatorio'       => $request["sistema_circulatorio"],
                'conciencia'                 => $request["conciencia"],
                'saturacion'                 => $request["saturacion"],
                'actividades'                => $request["actividades"],
                'respiracion'                => $request["respiracion"],
                'tipo_analgesia'             => $request["tipo_analgesia"],
                'd1'                         => $request["d1"],
                'd2'                         => $request["d2"],
                'd3'                         => $request["d3"],
                'd4'                         => $request["d4"],
                'd5'                         => $request["d5"],
                'd6'                         => $request["d6"],
                'd7'                         => $request["d7"],
                'd8'                         => $request["d8"],
                'd9'                         => $request["d9"],
                'd10'                        => $request["d10"],
                'd11'                        => $request["d11"],
                'operacion_propuesta'        => $request["operacion_propuesta"],
                'operacion_realizada'        => $request["operacion_realizada"],
                'total'                      => $request["total"],
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
                'tubo'                       => $request["tubo"],
            ];

            $id    = Hc_Anestesiologia::insertGetId($input1);
            $check = $request['lista'];
            if ($check != null) {
                foreach ($check as $value) {
                    record_tecnicas_anestesicas::create([
                        'id_hc_anestesiologia'    => $id,
                        'id_tecnicas_anestesicas' => $value,
                        'ip_creacion'             => $ip_cliente,
                        'ip_modificacion'         => $ip_cliente,
                        'id_usuariocrea'          => $idusuario,
                        'id_usuariomod'           => $idusuario,
                    ]);
                }
            }
        } else {
            $id     = $anestesiologia[0]->id;
            $input1 = [
                'id_hc'                      => $hc_id,
                'id_doctor'           => $request["id_doctor"],
                'id_doctor_ayudante'           => $request["id_doctor_ayudante"],
                'id_anestesiologo'           => $request["id_anestesiologo"],
                'id_ayudante'                => $request["id_ayudante"],
                'id_instrumentista'          => $request["id_instrumentista"],
                'fecha'                      => date('Y-m-d H:i:s', strtotime($request->fecha) ),
                'diagnostico_preoperatorio'  => $request["diagnostico_preoperatorio"],
                'diagnostico_postoperatorio' => $request["diagnostico_postoperatorio"],
                'id_tipoanestesiologia'      => $request["id_tipoanesteciologia"],
                'dextrosa'                   => $request["dextrosa"],
                'id_hc_procedimientos'       => $request["id_hc_procedimientos"],
                'cloruro_sodio'              => $request["cloruro_sodio"],
                'lactato_ringer'             => $request["lactato_ringer"],
                'sangre_derivados'           => $request["sangre_derivados"],
                'expansores'                 => $request["expansores"],
                'total'                      => $request["total"],
                'tecnicas_especiales'        => $request["tecnicas_especiales"],
                'hora'                       => $request["hora"],
                'duracion_anestesia'         => $request["duracion_anestesia"],
                'duracion_operacion'         => $request["duracion_operacion"],
                'id_sala'                    => $request["id_sala"],
                'id_guiado'                  => $request["id_guiado"],
                'comentarios'                => $request["comentarios"],
                'sistema_circulatorio'       => $request["sistema_circulatorio"],
                'conciencia'                 => $request["conciencia"],
                'saturacion'                 => $request["saturacion"],
                'actividades'                => $request["actividades"],
                'respiracion'                => $request["respiracion"],
                'tipo_analgesia'             => $request["tipo_analgesia"],
                'd1'                         => $request["d1"],
                'd2'                         => $request["d2"],
                'd3'                         => $request["d3"],
                'd4'                         => $request["d4"],
                'd5'                         => $request["d5"],
                'd6'                         => $request["d6"],
                'd7'                         => $request["d7"],
                'd8'                         => $request["d8"],
                'd9'                         => $request["d9"],
                'd10'                        => $request["d10"],
                'd11'                        => $request["d11"],
                'servicio'                   => $request["servicio"],
                'sala'                       => $request["sala"],
                'cama'                       => $request["cama"],
                'operacion_propuesta'        => $request["operacion_propuesta"],
                'operacion_realizada'        => $request["operacion_realizada"],
                'ip_modificacion'            => $ip_cliente,
                'id_usuariomod'              => $idusuario,
                'tubo'                       => $request["tubo"],
            ];
            Hc_Anestesiologia::where('id', $id)
                ->update($input1);
            $check = $request['lista'];
            record_tecnicas_anestesicas::where('id_hc_anestesiologia', $id)->delete();
            if (count($check) > 0) {
                foreach ($check as $value) {
                    record_tecnicas_anestesicas::create([
                        'id_hc_anestesiologia'    => $id,
                        'id_tecnicas_anestesicas' => $value,
                        'ip_creacion'             => $ip_cliente,
                        'ip_modificacion'         => $ip_cliente,
                        'id_usuariocrea'          => $idusuario,
                        'id_usuariomod'           => $idusuario,
                    ]);
                }
            }

        }
        /* return redirect()->intended('historiaclinica/anestesiologia/' . $request["id_hc_procedimientos"]); */
        return $id;
    }
    public function saveCanvas(Request $request)
    {
        $id                    = $request['id'];
        $verificar             = Hc_Anestesiologia::find($id);
        $extension             = 'record_' . '_' .$id. date('Ymdhis') . '_' . $request['my-file']->getClientOriginalName() ;
        $verificar->url_imagen = $extension;
        $verificar->save();
        $fileName = $extension;
        //ingresar la foto

        Storage::disk('hc_ima')->put($fileName, \File::get($request['my-file']));
        return response()->json('ok');
    }

    public function mostrarcsv($id, $agenda)
    {
        return view('hc_admision/anestesiologia/csv', ['id' => $id, 'agenda' => $agenda]);
    }

    public function creacsv(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'id_hc_anestesiologia' => $request["id_hc_anestesiologia"],
            'hora'                 => $request["hora"] . ':00',
            'presion_arterial'     => $request["presion_arterial"],
            'pulso'                => $request["pulso"],
            'respiracion'          => $request["respiracion"],
            'o2'                   => $request["o2"],
            'orina'                => $request["orina"],
            'anotaciones'          => $request["anotaciones"],
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
        ];
        hc_anestesiologia_csv::create($input1);

        return redirect()->intended('historiaclinica/anestesiologia/' . $request["mostrar"]);
    }

    public function imprime($procedimiento_id)
    {
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $procedimientos_hc = hc_procedimientos::find($procedimiento_id);
        $id                = $procedimientos_hc->id_hc;
        $historia          = Historiaclinica::find($id);
        $agenda            = Agenda::find($historia->id_agenda);
        $seguro            = Seguro::find($historia->id_seguro);
        $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
        $paciente          = Paciente::find($historia->id_paciente);
        $doctor            = User::find($historia->id_doctor1);
        $record            = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $procedimiento_id)->first();

        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $paper_size = array(0, 0, 595, 920);
        $data       = $historia;
        $date       = $historia->created_at;
        $csv        = hc_anestesiologia_csv::where('id_hc_anestesiologia', '=', $record->id)->get();
        $pentax     = Pentax::where('hcid', '=', $id)->first();

        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $pentax->id)->get();

        $record_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', '=', $record->id)->get();

        //return view('hc_admision/formato/'.$documento->formato);
        $view = \View::make('hc_admision.formato.record-anestesico', compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'record', 'historia', 'record_tecnicas', 'csv', 'procedimientos_pentax', 'procedimientos_hc'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
        $pdf->setOptions(['chroot'  => base_path('/')]);
        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        $vt_name = '';
        if($tipo_usuario == 11 ){
            $vt_name = 'C_ANESTESIA.pdf';
            return $pdf->stream($vt_name); 
        }
        return $pdf->stream('C_ANESTESIA.pdf');
    }

    public function eliminar_csv($id){

        $csv = hc_anestesiologia_csv::find($id);
        $csv->delete();

        return "delete";


    }


    public function editar_csv(Request $request)
    {

        try {
            $csv = hc_anestesiologia_csv::find($request['id']);
            $csv->hora = $request['hora'];
            $csv->save();
            return response()->json('ok');
        } catch (\Throwable $th) {
            return response()->json('no');
        }
    }
}
