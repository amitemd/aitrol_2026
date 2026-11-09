<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\AgendaScan;
use Sis_medico\Agenda_consentimiento;
use Sis_medico\Agenda_Preparacion;
use Sis_medico\Archivo_historico;
use Sis_medico\Bodega;
use Sis_medico\Documento;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Orden;
use Sis_medico\Firma_Usuario;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Hc_Cie10;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Orden;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\tipousuario;
use Sis_medico\User;

class ControlDocController extends Controller
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
        if (in_array($rolUsuario, array(1, 4, 5, 3, 6, 11, 7,20)) == false) {
            return true;
        }
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proc_consul = '0';
        return $this->index_sql($proc_consul);
    }

    private function index_sql($proc_consul)
    {

        $documentos = DB::table('documento')->leftjoin('seguros', 'seguros.id', 'documento.id_seguro')->leftjoin('subseguro', 'subseguro.id', 'documento.id_subseguro')->leftjoin('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->where('proc_consul', $proc_consul)->select('documento.*', 'seguros.nombre as snombre', 'subseguro.nombre as subnombre', 'tipousuario.nombre as tnombre')->orderby('documento.secuencia', 'asc')
            ->paginate(20);

        return view('hc_admision/controldoc/index', ['documentos' => $documentos, 'proc_consul' => $proc_consul]);
    }

    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguros      = Seguro::all();
        $subseguros   = SubSeguro::all();
        $tiposusuario = tipousuario::all();

        return view('hc_admision/controldoc/create', ['seguros' => $seguros, 'subseguros' => $subseguros, 'tiposusuario' => $tiposusuario]);
    }
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Documento::create([
            'nombre'          => strtoupper($request['nombre']),
            'codigo'          => strtoupper($request['codigo']),
            'proc_consul'     => $request['proc_consul'],
            'tipo_seguro'     => $request['tipo_seguro'],
            'id_seguro'       => $request['id_seguro'],
            'id_subseguro'    => $request['id_subseguro'],
            'id_tipo_usuario' => $request['id_tipo_usuario'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        return $this->index_sql($request['proc_consul']);
    }
    public function validateInput_Bodega(Request $request)
    {
        $reglas = [
            'nombre'      => 'required|unique:bodega',
            'id_hospital' => 'required',
        ];

        $mensajes = [
            'nombre.required' => 'Ingrese un nombre',
            'nombre.unique'   => 'Bodega ya existe',
        ];

        $this->validate($request, $reglas, $mensajes);
    }
    public function edit($id)
    {
    }

    public function act_sec($id, $value)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $documento  = Documento::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [

            'secuencia'       => $value,

            'ip_modificacion' => $ip_cliente,

            'id_usuariomod'   => $idusuario,
        ];

        $documento->update($input);

        $proc_consul = $documento->proc_consul;

        return $this->index_sql($proc_consul);
    }

    public function update2(Request $request)
    {

        $id                = $request['ahid'];
        $archivo_historico = Archivo_historico::findOrFail($id);
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $documento = Documento::find($request->id_doc);
        if ($documento->msp == '4') {
            $reglas   = ['archivo' => 'required|mimes:jpeg,jpg,pdf|max:900'];
            $mensajes = [
                'archivo.required' => 'Agrega una foto.',
                'archivo.mimes'    => 'Los archivos permitidos son: jpeg, jpg, pdf.',
                'archivo.max'      => 'El peso de la foto no puede ser mayor a :max KB.',
            ];
        } else {
            $reglas   = ['archivo' => 'required|mimes:pdf|max:10000'];
            $mensajes = [

                'archivo.required' => 'Agrega un archivo.',
                'archivo.mimes'    => 'El archivo a seleccionar debe ser *.pdf.',
                'archivo.max'      => 'El peso del archivo no puede ser mayor a :max KB.',
            ];
        }

        $this->validate($request, $reglas, $mensajes);

        //$historia = Historiaclinica::find($archivo_historico->id_historia);
        $historia        = $this->carga_hc($archivo_historico->id_historia);
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        if ($documento->msp != '4') {
            $nuevo_nombre = $historia->id_paciente . "_" . $archivo_historico->tipo_documento . "_" . $archivo_historico->id_historia . "_" . $id . "." . $extension;
            $r1           = Storage::disk('hc')->put($nuevo_nombre, \File::get($request['archivo']));
        } else {
            $nuevo_nombre = "copia_" . $historia->id_paciente . "." . $extension;
            $r1           = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
            //dd($r1);
        }

        if ($r1) {
            if ($documento->msp != '4') {
                $archivo_historico->archivo         = $nuevo_nombre;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $archivo_historico->ruta            = "hc/";
                $r2                                 = $archivo_historico->save();
            } else {
                $copia_ced = Paciente_Biopsia::where('id_paciente', $historia->id_paciente)->where('estado', '3')->first();

                if (is_null($copia_ced)) {
                    Paciente_Biopsia::create([
                        'nombre'          => $nuevo_nombre,
                        'id_paciente'     => $historia->id_paciente,
                        'estado'          => '3',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'id_usuariocrea'  => $idusuario,
                    ]);
                } else {
                    $copia_ced->update([
                        'nombre'          => $nuevo_nombre,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }

                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "ACTUALIZA COPIA CEDULA",
                    'dato_ant1'   => $historia->id_paciente,
                    'dato1'       => "ACTUALIZA COPIA CEDULA",

                ]);
            }
        }

        $proc_consul = $historia->proc_consul;
        $tipo        = $historia->tipo;

        return "ok";

        //return $this->control_tb ($historia->hcid, $proc_consul, $tipo);
        //return $this->control_doc($historia->hcid, $request);
    }

    public function update_scan(Request $request)
    {

        $id         = $request['id_agenda'];
        $agenda     = Agenda::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       // dd($idusuario);
        date_default_timezone_set('America/Guayaquil');
        $reglas   = ['archivo' => 'required|mimes:jpeg,jpg,pdf'];
        $mensajes = [
            'archivo.required' => 'Agrega una foto.',
        ];

        $this->validate($request, $reglas, $mensajes);

        //$historia = Historiaclinica::find($archivo_historico->id_historia);
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "scan_" . $id . "." . $extension;
        $r1              = Storage::disk('agenda_scan')->put($nuevo_nombre, \File::get($request['archivo']));

        if ($r1) {

            $copia_ced = AgendaScan::where('id_agenda', $id)->first();
            if (is_null($copia_ced)) {
                AgendaScan::create([
                    'id_agenda'       => $id,
                    'archivo'         => $nuevo_nombre,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ]);
            } else {
                $copia_ced->update([
                    'archivo'         => $nuevo_nombre,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return "ok";
    }

    public function descarga_scan2($id_agenda)
    {
        $agenda = AgendaScan::where('id_agenda', $id_agenda)->first();
        $name   = $agenda->archivo;
        $path   = storage_path() . '/app/agenda_scan/' . $name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proc_consul = $request['proc_consul'];
        return $this->index_sql($proc_consul);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = Bodega::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        $query = $query->join('hospital', 'hospital.id', 'bodega.id_hospital')->select('bodega.*', 'hospital.nombre_hospital');

        return $query->paginate(40);
    }

    public function imprimirpdf2($hcid)
    {
        return "hola";
    }
    private function carga_hc($hcid)
    {

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'agenda.id_sala as idsala')->first();

        return $historiaclinica;
    }

    public function control_doc(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['hcid'])) {
            $hcid = $request['hcid'];
        } else {
            $hcid = Historiaclinica::where('id_agenda', $request['cita'])->first()->hcid;
        }

        $url_doctor = $request['url_doctor'];
        $unix       = $request['unix'];
        $protocolo  = $request['protocolo'];

        $historiaclinica = $this->carga_hc($hcid);

        $procs = [];
        if ($historiaclinica->proc_consul == '1') {

            $procs = Procedimiento::find($historiaclinica->id_procedimiento)->observacion;

            $procs_ag = AgendaProcedimiento::where('id_agenda', $historiaclinica->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }
        }

        $hcid        = $historiaclinica->hcid;
        $proc_consul = $historiaclinica->proc_consul;
        $tipo        = $historiaclinica->tipo;
        $id_doctor1  = $historiaclinica->id_doctor1;

        //$hc_protocolo = hc_protocolo::where('id',$protocolo)->first();

        $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'hp.id as id_hcproc', 'hp.id_seguro', 'hp.id_empresa')->orderBy('p.created_at', 'desc')->first();
        //dd($protocolo);
        $empresas = Empresa::where('estado', '1')->where('prioridad', '1')->get();
        $seguros  = Seguro::where('inactivo', '1')->get();

        $documentos = $this->carga_documentos_union($hcid, $proc_consul, $tipo);

        return view('hc_admision/admision/index', ['historia' => $historiaclinica, 'procs' => $procs, 'hcid' => $hcid, 'proc_consul' => $proc_consul, 'tipo' => $tipo, 'url_doctor' => $url_doctor, 'unix' => $unix, 'id_doctor1' => $id_doctor1, 'documentos' => $documentos, 'protocolo' => $protocolo, 'empresas' => $empresas, 'seguros' => $seguros]);
    }

    public function agenda_scan($id_agenda)
    {
        return view('hc_admision/admision/sube_scan', ['id_agenda' => $id_agenda]);
    }

    private function carga_documentos($tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro)
    {

        $historia = Historiaclinica::find($hcid);
        $paciente = $historia->paciente;
        $age      = 0;
        if ($paciente->fecha_nacimiento != null) {
            $age = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $principal = '1';
        if ($historia->id_subseguro != null) {
            $principal = Subseguro::find($historia->id_subseguro)->principal;
        }

        $fecha = date('Y-m-d', strtotime($historia->created_at)) . ' 00:00:00';

        if ($tipo === null) {
            $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', null)->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre')->where('principal', null)->orwhere('principal', $principal);

            //dd("IN",$tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro,$documentos->get());

        }

        if ($id_seguro === null) {

            if ($age >= '45') {
                $documentos = DB::table('documento')
                    ->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                    ->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)->whereNull('principal')
                    ->orWhere(function ($query) use ($proc_consul, $tipo, $fecha, $principal) {
                        $query->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                            ->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)->where('principal', $principal);
                    })
                    ->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')
                    ->select('documento.*', 'tipousuario.nombre as tnombre');

                //dd("IN",$tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro,$documentos->get());
            } else {
                $documentos = DB::table('documento')
                    ->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                    ->where('tipo_documento', '<>', 'CARDIO')->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)
                    ->whereNull('principal')
                    ->orWhere(function ($query) use ($proc_consul, $tipo, $fecha, $principal) {
                        $query->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                            ->where('tipo_documento', '<>', 'CARDIO')->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)
                            ->where('principal', $principal);
                    })
                    ->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')
                    ->select('documento.*', 'tipousuario.nombre as tnombre');

                //dd("IN", $tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro, $principal, $documentos->get());
            }
        } else {

            if ($id_subseguro != null) {
                $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', $id_seguro)->where('documento.id_subseguro', $id_subseguro)->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre');
            } else {
                $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', $id_seguro)->whereNull('documento.id_subseguro')->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre');
                if ($proc_consul == '1') {
                    //dd($proc_consul, $tipo, $hcid, $id_seguro, $id_subseguro,$documentos->get());
                }
            }

            //dd($documentos->get());

        }

        //dd($documentos->get());

        return $documentos;
    }

    private function carga_documentos_rpt($id_seguro)
    {

        $documentos1 = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', null);

        $documentos2 = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', '0')->where('documento.id_seguro', null);

        $documentos3 = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', '0')->where('documento.id_seguro', $id_seguro)->where('documento.rpt_prin', '1');

        $documentos = $documentos1->union($documentos2)->union($documentos3)->orderby('secuencia');

        return $documentos;
    }

    //CAMBIOS EN TABLA DE DOCUMENTOS-SE AGREGO CAMPO msp- Y TABLA DE SUBSEGUROS

    public function carga_documentos_union($hcid, $proc_consul, $tipo)
    {

        $historia = Historiaclinica::find($hcid);
        $fecha    = date('Y-m-d', strtotime($historia->created_at)) . ' 00:00:00';

        // <TIPO> <PROC_CONSUL> <HCID> <SEGURO> <SUBSEGURO>

        $documentos1 = $this->carga_documentos($tipo, 2, $hcid, null, null);
        //dd($documentos1->get());

        $documentos2 = $this->carga_documentos($tipo, $proc_consul, $hcid, null, null);
        //dd($documentos2->get());

        $documentos3 = $this->carga_documentos($tipo, 2, $hcid, $historia->id_seguro, $historia->id_subseguro);
        //dd($documentos3->get());

        $documentos4 = $this->carga_documentos($tipo, $proc_consul, $hcid, $historia->id_seguro, $historia->id_subseguro);
        //dd($documentos4->get());

        $documentos5 = $this->carga_documentos(null, $proc_consul, $hcid, null, null);
        //dd($documentos5->get());

        $documentos6 = $this->carga_documentos($tipo, $proc_consul, $hcid, $historia->id_seguro, null);
        //dd($documentos6->get());

        $documentos = $documentos1->union($documentos2)->union($documentos3)->union($documentos4)->union($documentos5)->union($documentos6)->orderby('secuencia')->get();

        //dd($documentos);
        return $documentos;
    }

    public function control_tb($hcid, $proc_consul, $tipo)
    {

        $documentos = $this->carga_documentos_union($hcid, $proc_consul, $tipo);

        $historiaclinica = $this->carga_hc($hcid);

        return view('hc_admision/admision/index_tb', ['documentos' => $documentos, 'hcid' => $hcid, 'proc_consul' => $proc_consul, 'tipo' => $tipo, 'historia' => $historiaclinica]);
    }

    public function continua($hcid, $url_doctor, $unix)
    {

        //dd($url_doctor);
        $historia = Historiaclinica::find($hcid);
        if ($url_doctor === 0) {
            //dd(" 0");
            return redirect()->route('preagenda.pentax', ['fecha' => $unix]);
        } else {

            //dd("diferente de 0");
            return redirect()->route('agenda.fecha', ['id' => $historia->id_doctor1, 'i' => $unix]);
        }
    }

    public function sube_archivo($ahid, $id_doc)
    {

        $archivo_historico = Archivo_historico::find($ahid);

        return view('hc_admision/admision/sube_archivo', ['ahid' => $ahid, 'id_doc' => $id_doc, 'descripcion' => $archivo_historico->descripcion]);
    }

    public function show($id)
    {
        //

    }

    public function imprimirpdf($ahid)
    {

        $archivo_historico = Archivo_historico::find($ahid);
        $documento         = Documento::find($archivo_historico->id_documento);
        $historia          = $this->carga_hc($archivo_historico->id_historia);
        $agenda            = Agenda::find($historia->id_agenda);
        $seguro            = Seguro::find($historia->id_seguro);
        $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
        $paciente          = Paciente::find($historia->id_paciente);
        $empresaxdoc       = Empresa::find($documento->id_empresa);
        $doctor            = User::find($historia->id_doctor1);

        //nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
        $procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento', $agenda->id_procedimiento)->first();

        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $paper_size = array(0, 0, 595, 920);
        $data       = $historia;
        //$date = $historia->created_at;
        $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historia->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

        if (is_null($protocolo)) {
            $date = $agenda->fechaini;
        } elseif (!is_null($protocolo)) {

            if (!is_null($protocolo->fecha)) {
                $date = $protocolo->fecha;

            } else {
                $date = $agenda->fechaini;
            }
        }
        //$date = $agenda->fechaini;
        //return view('hc_admision/formato/'.$documento->formato);
        $view = \View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        if ($documento->formato == 'contrareferencia') {

            $pdf->setOptions(['dpi' => 96]);
            $paper_size = array(0, 0, 1100, 1650);
            $pdf->setpaper($paper_size);
            $pdf->loadHTML($view);
        } else {
            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
        }

        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        //return $pdf->download($historia->id_paciente.'_'.$documento->tipo_documento.'_'.$archivo_historico->id_historia.'_'.$archivo_historico->id.'.pdf');
        return $pdf->stream($historia->id_paciente . '_' . $documento->tipo_documento . '_' . $archivo_historico->id_historia . '_' . $archivo_historico->id . '.pdf');
    }

    public function imprimirdatos_paciente($id_paciente)
    {
        $paciente = paciente::find($id_paciente);
        //dd($paciente);
        $view = \View::make('hc_admision.formato.datos_paciente', compact('paciente'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        return $pdf->stream('datos_paciente' . $id_paciente . '.pdf');
    }
    //PASAR 08082018
    public function imprimirpdf_resumen($id)
    {
        $agenda   = Agenda::find($id);
        //dd($agenda);
        $historia = Historiaclinica::where('id_agenda', $id)->first();

        //$historia = Historiaclinica::find($historia[0]->hcid);
        if (!is_null($historia)) {
            $seguro      = Seguro::find($historia->id_seguro);
            $paciente    = Paciente::find($historia->id_paciente);
            $doctor      = User::find($historia->id_doctor1);
            $responsable = User::find($historia->id_usuariocrea);
            if ($agenda->proc_consul == '1') {
                $pentax             = Pentax::where('id_agenda', $agenda->id)->first();
                $procedimientos     = PentaxProc::where('id_pentax', $pentax->id)->get();
                $procedimientos_txt = '';
                foreach ($procedimientos as $value) {
                    if ($procedimientos_txt == '') {
                        $procedimientos_txt = procedimiento::find($value->id_procedimiento)->nombre;
                    } else {
                        $procedimientos_txt = $procedimientos_txt . '+' . procedimiento::find($value->id_procedimiento)->nombre;
                    }
                }
            } else {
                $procedimientos_txt = 'CONSULTA';
            }
            $ControlDocController = new ControlDocController;
            $documentos           = $ControlDocController->carga_documentos_union($historia->hcid, $agenda->proc_consul, $seguro->tipo);

            $data = $historia;
            $date = $historia->created_at;
        } else {
            $seguro      = Seguro::find($agenda->id_seguro);
            $paciente    = Paciente::find($agenda->id_paciente);
            $doctor      = User::find($agenda->id_doctor1);
            $responsable = User::find($agenda->id_usuariocrea);
            if ($agenda->proc_consul == '1') {
                $procedimientos_txt = procedimiento::find($agenda->id_procedimiento)->nombre;
                $procedimientos     = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();
                foreach ($procedimientos as $value) {

                    $procedimientos_txt = $procedimientos_txt . '+' . procedimiento::find($value->id_procedimiento)->nombre;
                }
            } else {
                $procedimientos_txt = 'CONSULTA';
            }

            $documentos = null;

            $data = $agenda;
            $date = $agenda->created_at;
        }

        $log_agenda = Log_Agenda::where('id_agenda', $agenda->id)->get();

        $empresa = Empresa::where('id', $agenda->id_empresa)->first();
        if (is_null($empresa)) {
            abort(422, 'La agenda no tiene una empresa válida asociada.');
        }

        //$empresaxdoc = Empresa::find($agenda->id_empresa);

        //nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
        //$procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento',$agenda->id_procedimiento)->first();

        //dd($empresa);

        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $paper_size = array(0, 0, 595, 920);

        //return view('hc_admision/formato/'.$documento->formato);
        $view = \View::make('hc_admision.formato.resumen', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'seguro', 'responsable', 'procedimientos_txt', 'documentos', 'log_agenda', 'agenda'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        if (!is_null($historia)) {
            return $pdf->download($historia->id_paciente . '_resumen_' . $historia->hcid . '.pdf');
        } else {
            return $pdf->download($agenda->id_paciente . '_resumenAG_' . $agenda->id . '.pdf');
        }
    }

    public function form_cert($id)
    {

        $agenda = Agenda::find($id);
        if ($agenda->proc_consul == '0') {
            $tipo = "ATENCIÓN MÉDICA";
        } elseif ($agenda->proc_consul == '1') {
            $pentax = Pentax::where('id_agenda', $agenda->id)->first();
            if (!is_null($pentax)) {
                $procedimientos = PentaxProc::where('id_pentax', $pentax->id)->get();
                $bandera        = '0';
                foreach ($procedimientos as $procedimiento) {
                    $txt = Procedimiento::find($procedimiento->id_procedimiento)->nombre;
                    if ($bandera == '0') {
                        $tipo    = $txt;
                        $bandera = '1';
                    } else {
                        $tipo = $tipo . " + " . $txt;
                    }
                }
            }
        } else {
            $tipo = 'ATENCIÓN MÉDICA';
        }

        $historia = DB::table('historiaclinica as h')->where('h.id_agenda', $id)->join('agenda as a', 'a.id', 'h.id_agenda')->select('h.*', 'a.fechaini')->first();
        if (is_null($historia)) {
            $historia = Agenda::find($id);
            $tipo     = $agenda->proc_consul == 1 ? "PROCEDIMIENTO MÉDICO" : "ATENCIÓN MÉDICA";
        }
        //dd($historia);
        $users = User::join('usuario_empresa as ue', 'ue.id_usuario', '=', 'users.id')
            ->where('ue.id_empresa', $agenda->id_empresa)->where('users.estado', 1)->where('users.id_tipo_usuario', 3)
            ->select('users.*')->distinct()->orderby('users.apellido1')->get();

        $diagnostico = [];

        if (!is_null($historia)) {

            if ($agenda->proc_consul == '1') {
                //$diagnostico = Hc_Cie10::where('hcid',$historia->hcid)->where('presuntivo_definitivo','DEFINITIVO')->get();   ANTES 02/08/2019
                /*$diagnostico = Hc_Cie10::where('hcid',$historia->hcid)->where('presuntivo_definitivo','DEFINITIVO')->orwhere('hcid',$historia->hcid)->where('ingreso_egreso','EGRESO')->select('cie10','ingreso_egreso','presuntivo_definitivo')->groupby('cie10')->get();*/
                $diagnostico = Hc_Cie10::where('hcid', $historia->hcid)->select('cie10', 'ingreso_egreso', 'presuntivo_definitivo')->groupby('cie10')->get();
            } else {
                $diagnostico = Hc_Cie10::where('hcid', $historia->hcid)->get();
            }
        }

        $paciente = Paciente::find($agenda->id_paciente);

        return view('hc_admision.admision.certificado', ['id' => $id, 'tipo' => $tipo, 'historia' => $historia, 'users' => $users, 'diagnostico' => $diagnostico, 'paciente' => $paciente]);
    }

    public function generar_cert(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $descanso   = $request['descanso'];
        $date       = Date('Y-m-d');

        $tipo        = $request['observacion'];
        $agenda      = Agenda::find($request['id']);
        $paciente    = Paciente::find($agenda->id_paciente);
        $id_doctor1  = $request['id_doctor1'];
        $doctor      = User::find($id_doctor1);
        $cfecha      = $request['cfecha'];
        $diagnostico = $request['diagnostico'];
        $institucion = $request['institucion'];

        $firma        = Firma_Usuario::where('id_usuario', $id_doctor1)->first();
        $especialidad = Especialidad::find($agenda->espid);
        $desde        = $request->idesde;
        $hasta        = $request->ihasta;
        $familiar     = $request->familiar;
        /*if($agenda->proc_consul=='0'){
        //$tipo = "ATENCIÓN MÉDICA";
        }elseif($agenda->proc_consul=='1'){
        $pentax = Pentax::where('id_agenda',$agenda->id)->first();
        $procedimientos = PentaxProc::where('id_pentax',$pentax->id)->get();
        $bandera = '0';
        foreach ($procedimientos as $procedimiento) {
        $txt = Procedimiento::find($procedimiento->id_procedimiento)->nombre;
        if($bandera=='0'){
        $tipo = $txt;
        $bandera = '1';
        }else{
        $tipo = $tipo." + ".$txt;
        }

        }

        }*/
        //dd($doctor,$cfecha);
        //dd($request->all());
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA CERTIFICADO MEDICO",
            'dato_ant1'   => $agenda->id_paciente,
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato_ant2'   => "DESCANSO: " . $descanso . " OBSERVACION: " . $tipo,
            'dato2'       => $agenda->id,
        ]);

        $descanso2 = $descanso - 1;
        $letras    = \NumeroALetras::convertir($descanso);

        //dd($agenda);

        $fecha_hasta = Date('Y-m-d', strtotime('+' . $descanso2 . ' days', strtotime($cfecha)));

        //dd($agenda->fechaini,$fecha_hasta);
        //return view('hc_admision/formato/'.$documento->formato);
        $view = \View::make('hc_admision.formato.certificado', compact('agenda', 'tipo', 'date', 'paciente', 'descanso', 'letras', 'fecha_hasta', 'cfecha', 'doctor', 'id_doctor1', 'diagnostico', 'firma', 'especialidad', 'desde', 'hasta', 'familiar', 'institucion'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->setOptions(['dpi' => 96]);
        $paper_size = array(0, 0, 595.28, 850);
        $pdf->setpaper($paper_size);
        //$pdf->loadHTML($view);

        $pdf->loadHTML($view)->setPaper('a4', 'portrait');

        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        //return $pdf->download('certificado.pdf');
        return $pdf->stream('Certificado_' . $agenda->id_paciente . '.pdf');
    }

    public function reporte_doc(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalDocumentReportExportService::class)->export($request);
    }

    public function reporte_doc_seguros(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalInsuranceDocumentReportExportService::class)->export($request);
    }

    public function reporte_documentos_seguros2(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalConsolidatedDocumentReportExportService::class)->export($request);
    }

    public function crea_doc($hcid, $id_doc)
    {
        //dd($hcid);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $archivo  = Archivo_historico::where('id_documento', $id_doc)->where('id_historia', $hcid)->first();
        $historia = Historiaclinica::find($hcid);
        //dd($archivo);
        //si no existe lo crea
        if (is_null($archivo)) {
            $documento = Documento::find($id_doc);

            $input = [
                'id_historia'        => $hcid,
                'id_documento'       => $id_doc,
                'tipo_documento'     => $documento->tipo_documento,
                'descripcion'        => $documento->nombre,
                'id_usuario_entrega' => $historia->id_paciente,
                'id_usuario_recibe'  => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'fecha_entrega'      => date('Y-m-d H:i:s'),
            ];

            $archivo2 = Archivo_historico::where('id_documento', $id_doc)->where('id_historia', $hcid)->first();

            if (is_null($archivo2)) {

                Archivo_historico::create($input);
            }
        }
        //si existe y esta activo lo inactiva
        if (!is_null($archivo)) {
            if ($archivo->estado == '1') {
                $inputh = [
                    'estado'             => '0',
                    'fecha_entrega'      => null,
                    'id_usuario_entrega' => null,
                    'id_usuario_recibe'  => null,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }
            if ($archivo->estado == '0') {
                $inputh = [
                    'estado'             => '1',
                    'fecha_entrega'      => date('Y-m-d H:i:s'),
                    'id_usuario_entrega' => $historia->id_paciente,
                    'id_usuario_recibe'  => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }

            //$archivo->update($inputh);

        }
        $historiaclinica = $this->carga_hc($hcid);

        $procs = [];
        if ($historiaclinica->proc_consul == '1') {

            $procs = Procedimiento::find($historiaclinica->id_procedimiento)->observacion;

            $procs_ag = AgendaProcedimiento::where('id_agenda', $historiaclinica->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }
        }

        $hcid        = $historiaclinica->hcid;
        $proc_consul = $historiaclinica->proc_consul;
        $tipo        = $historiaclinica->tipo;
        $id_doctor1  = $historiaclinica->id_doctor1;

        $documentos = $this->carga_documentos_union($hcid, $proc_consul, $tipo);

        return $this->control_tb($hcid, $proc_consul, $tipo);
    }
    public function actu_doc($hcid, $id_doc, $tipo)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $archivo  = Archivo_historico::where('id_documento', $id_doc)->where('id_historia', $hcid)->first();
        $historia = Historiaclinica::find($hcid);
        //dd($archivo);
        //si no existe lo crea
        if (is_null($archivo)) {
            $documento = Documento::find($id_doc);

            $input = [
                'id_historia'        => $hcid,
                'id_documento'       => $id_doc,
                'tipo_documento'     => $documento->tipo_documento,
                'descripcion'        => $documento->nombre,
                'id_usuario_entrega' => $historia->id_paciente,
                'id_usuario_recibe'  => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'fecha_entrega'      => date('Y-m-d H:i:s'),
            ];

            $archivo2 = Archivo_historico::where('id_documento', $id_doc)->where('id_historia', $hcid)->first();

            if (is_null($archivo2)) {

                //Archivo_historico::create($input);

            }
        }
        //si existe y esta activo lo inactiva
        if (!is_null($archivo)) {
            if ($archivo->estado == '1') {
                $inputh = [
                    'estado'             => '0',
                    'fecha_entrega'      => null,
                    'id_usuario_entrega' => null,
                    'id_usuario_recibe'  => null,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }
            if ($archivo->estado == '0') {
                $inputh = [
                    'estado'             => '1',
                    'fecha_entrega'      => date('Y-m-d H:i:s'),
                    'id_usuario_entrega' => $historia->id_paciente,
                    'id_usuario_recibe'  => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }

            $archivo->update($inputh);
        }
        $historiaclinica = $this->carga_hc($hcid);

        $procs = [];
        if ($historiaclinica->proc_consul == '1') {

            $procs = Procedimiento::find($historiaclinica->id_procedimiento)->observacion;

            $procs_ag = AgendaProcedimiento::where('id_agenda', $historiaclinica->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }
        }

        $hcid        = $historiaclinica->hcid;
        $proc_consul = $historiaclinica->proc_consul;
        $tipo        = $historiaclinica->tipo;
        $id_doctor1  = $historiaclinica->id_doctor1;

        $documentos = $this->carga_documentos_union($hcid, $proc_consul, $tipo);

        return $this->control_tb($hcid, $proc_consul, $tipo);
    }
    public function imprime_actadeentrega($id)
    {
        $agenda    = Agenda::find($id);
        $historia  = Historiaclinica::where('id_agenda', $agenda->id)->first();
        $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historia->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();
        if (is_null($protocolo)) {
            $date = $agenda->fechaini;
        } elseif (!is_null($protocolo)) {

            if (!is_null($protocolo->fecha)) {
                $date = $protocolo->fecha;

            } else {
                $date = $agenda->fechaini;
            }
        }
        $view = \View::make('hc_admision.formato.actadeentrega2', compact('agenda', 'date'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');

        return $pdf->stream('actadeentrega' . $agenda->id_paciente . '.pdf');
    }

    public function documentos_pdf(Request $request, $id_paciente, $id_empresa, $id_agenda = null)
    {
        $activeCompany = trim((string) $request->session()->get('id_empresa'));
        if ($activeCompany === '' || (string) $activeCompany !== (string) $id_empresa) abort(403, 'La empresa solicitada no está activa.');
        $empresa = Empresa::where('id', $id_empresa)->first();
        if (!$empresa) abort(404, 'Empresa no encontrada.');
        $paciente = paciente::find($id_paciente);
        if (!$paciente) abort(404, 'Paciente no encontrado.');
        $agenda = null;
        if ($id_agenda !== null) {
            $agenda = Agenda::where('id', $id_agenda)->where('id_paciente', $id_paciente)->where('id_empresa', $id_empresa)->first();
            if (!$agenda) abort(404, 'La agenda no pertenece al paciente y empresa activos.');
        }
        $consentimiento = Formato_consentimiento::where('estado', 1)->where(function ($query) use ($id_empresa) {
            $query->where('id_empresa', $id_empresa)->orWhereNull('id_empresa');
        })->get();
        return view('agenda/documentos_pdf', ['paciente' => $paciente, 'consentimiento' => $consentimiento, 'empresa' => $empresa, 'agenda' => $agenda]);
    }
    public function preaparacion_modal($id_paciente, $id_empresa, $fecha_ini)
    {
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $paciente       = paciente::find($id_paciente);
        $consentimiento = Formato_consentimiento::where('estado', 1)->get();
        $fecha          = Agenda::where('fechaini', $fecha_ini)->first();
        return view('agenda/preparacion_modal', ['fecha' => $fecha, 'paciente' => $paciente, 'consentimiento' => $consentimiento, 'empresa' => $empresa]);
    }
    public function descargar_pdf(Request $request, $id_empresa)
    {

        $variable   = $request->id;
        $formatoid  = $request->nombre_procedimiento;
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $date       = Date('Y-m-d');
        $paciente   = Paciente::where('id', $variable)->first();
        //dd($paciente);
        $agenda = $request->id_agenda
            ? Agenda::where('id', $request->id_agenda)->where('id_paciente', $variable)->where('id_empresa', $id_empresa)->first()
            : Agenda::where('id_paciente', $variable)->where('id_empresa', $id_empresa)->orderBy('fechaini', 'desc')->first();
        if (!$agenda) abort(404, 'No se encontró una agenda del paciente en la empresa activa.');
        //dd($agenda);
        Agenda_consentimiento::create([
            'id_paciente'    => $request['cedula'],
            'cie_10'         => $request['nombre_diagnostico'],
            'formato'        => $request['nombre_procedimiento'],
            'ip_creacion'    => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
        ]);
        $consentimiento = Agenda_consentimiento::where('id_paciente', $variable)->first();
        //dd($consentimiento);
        $nombre = DB::table('formato_consentimiento')
            ->join('agenda_consentimiento', 'agenda_consentimiento.formato', '=', 'formato_consentimiento.id')
            ->where('agenda_consentimiento.formato', $formatoid)
            ->select('formato_consentimiento.nombre')
            ->first();
        //dd($nombre);
        if (!is_null($formatoid)) {
            $formatoDinamico = Formato_consentimiento::where('id', $formatoid)->where('estado', 1)
                ->where(function ($query) use ($id_empresa) { $query->whereNull('id_empresa')->orWhere('id_empresa', $id_empresa); })->firstOrFail();
            if ($formatoDinamico->auditoria === 'V3-GENERICO' || !empty($formatoDinamico->consiste)) {
                $view = \View::make('hc_admision.consentimientos.generico_v3', array('date'=>$date,'paciente'=>$paciente,
                    'consentimiento'=>$consentimiento,'agenda'=>$agenda,'nombre'=>$nombre,'empresa'=>$empresa,'formato'=>$formatoDinamico))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('consentimiento_informado.pdf');
            }
            if ($formatoid == '1') {
                $view = \View::make('hc_admision.consentimientos.balon-colocacion', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Balon_colocacion' . '.pdf');
            } elseif ($formatoid == '2') {
                $view = \View::make('hc_admision.consentimientos.balon-retiro', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Balon_retiro' . '.pdf');
            } elseif ($formatoid == '3') {
                $view = \View::make('hc_admision.consentimientos.colono', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Colono' . '.pdf');
            } elseif ($formatoid == '4') {
                $view = \View::make('hc_admision.consentimientos.cpre', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Cpre' . '.pdf');
            } elseif ($formatoid == '5') {
                $view = \View::make('hc_admision.consentimientos.eco_anorectal', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eco_anorectal' . '.pdf');
            } elseif ($formatoid == '6') {
                $view = \View::make('hc_admision.consentimientos.eco', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eco' . '.pdf');
            } elseif ($formatoid == '7') {
                $view = \View::make('hc_admision.consentimientos.eda', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eda' . '.pdf');
            } elseif ($formatoid == '8') {
                $view = \View::make('hc_admision.consentimientos.enteroscopia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Enteroscopia' . '.pdf');
            } elseif ($formatoid == '9') {
                $view = \View::make('hc_admision.consentimientos.enterescopia_retrogada', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Enterescopia_retrogada' . '.pdf');
            } elseif ($formatoid == '10') {
                $view = \View::make('hc_admision.consentimientos.gastrostomia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Gastrostomia' . '.pdf');
            } elseif ($formatoid == '11') {
                $view = \View::make('hc_admision.consentimientos.cpm', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Cpm' . '.pdf');
            } elseif ($formatoid == '12') {
                $view = \View::make('hc_admision.consentimientos.autorizacion_imagen', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Autorizacion_de_uso_de_imagen_e_informacion_endoscopica' . '.pdf');
            } elseif ($formatoid == '13') {
                $view = \View::make('hc_admision.consentimientos.manometria_ano_rectal', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('manometria_ano_rectal' . '.pdf');
            } elseif ($formatoid == '14') {
                $view = \View::make('hc_admision.consentimientos.anestesia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anestesia' . '.pdf');
            } elseif ($formatoid == '15') {
                $view = \View::make('hc_admision.consentimientos.manometria_esofagica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('manometria_esofagica' . '.pdf');
            } elseif ($formatoid == '16') {
                $view = \View::make('hc_admision.consentimientos.ph_esofagica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('ph_esofagica' . '.pdf');
            } elseif ($formatoid == '17') {
                $view = \View::make('hc_admision.consentimientos.capsula_endoscopica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('capsula_endoscopica' . '.pdf');
            } elseif ($formatoid == '18') {
                $view = \View::make('hc_admision.consentimientos.phmetria_cap', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('phmetria_cap' . '.pdf');
            } elseif ($formatoid == '19') {
                $view = \View::make('hc_admision.consentimientos.consentimiento_encuesta', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('consentimiento_encuesta' . '.pdf');
            } elseif ($formatoid == '20') {
                $view = \View::make('hc_admision.consentimientos.anexo_informativo', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anexo_informativo' . '.pdf');
            } elseif ($formatoid == '21') {
                $view = \View::make('hc_admision.consentimientos.anexo_anastesico', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anexo_anastesico' . '.pdf');
            } elseif ($formatoid == '22') {
                $view = \View::make('hc_admision.consentimientos.endoscopica_percutanea', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('endoscopica_percutanea' . '.pdf');
            } elseif ($formatoid == '23') {
                $view = \View::make('hc_admision.consentimientos.broncoscopia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('broncoscopia' . '.pdf');
            }
        }
    }
    public function descargar(Request $request, $id_empresa)
    {

        $variable   = $request->id;
        $formatoid  = $request->nombre_procedimiento;
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $date       = Date('Y-m-d');
        $paciente   = Paciente::where('id', $variable)->first();
        //dd($paciente);
        $agenda = Agenda::where('id_paciente', $variable)->first();
        //dd($agenda);
        Agenda_Preparacion::create([
            'id_paciente'    => $request['cedula'],
            /*'cie_10'         => $request['nombre_diagnostico'],*/
            'formato'        => $request['nombre_procedimiento'],
            'ip_creacion'    => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
        ]);
        $consentimiento = Agenda_Preparacion::where('id_paciente', $variable)->first();
        //dd($consentimiento);
        $nombre = DB::table('formato_consentimiento')
            ->join('agenda_preparacion', 'agenda_preparacion.formato', '=', 'formato_consentimiento.id')
            ->where('agenda_Preparacion.formato', $formatoid)
            ->select('formato_consentimiento.nombre')
            ->first();
        //dd($nombre);
        if (!is_null($formatoid)) {
            if ($formatoid == '1') {
                $view = \View::make('hc_admision.consentimientos.balon-colocacion', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Balon_colocacion' . '.pdf');
            } elseif ($formatoid == '2') {
                $view = \View::make('hc_admision.consentimientos.balon-retiro', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Balon_retiro' . '.pdf');
            } elseif ($formatoid == '3') {
                $view = \View::make('hc_admision.consentimientos.colono', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Colono' . '.pdf');
            } elseif ($formatoid == '4') {
                $view = \View::make('hc_admision.consentimientos.cpre', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Cpre' . '.pdf');
            } elseif ($formatoid == '5') {
                $view = \View::make('hc_admision.consentimientos.eco_anorectal', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eco_anorectal' . '.pdf');
            } elseif ($formatoid == '6') {
                $view = \View::make('hc_admision.consentimientos.eco', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eco' . '.pdf');
            } elseif ($formatoid == '7') {
                $view = \View::make('hc_admision.consentimientos.eda', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Eda' . '.pdf');
            } elseif ($formatoid == '8') {
                $view = \View::make('hc_admision.consentimientos.enteroscopia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Enteroscopia' . '.pdf');
            } elseif ($formatoid == '9') {
                $view = \View::make('hc_admision.consentimientos.enterescopia_retrogada', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Enterescopia_retrogada' . '.pdf');
            } elseif ($formatoid == '10') {
                $view = \View::make('hc_admision.consentimientos.gastrostomia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Gastrostomia' . '.pdf');
            } elseif ($formatoid == '11') {
                $view = \View::make('hc_admision.consentimientos.cpm', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Cpm' . '.pdf');
            } elseif ($formatoid == '12') {
                $view = \View::make('hc_admision.consentimientos.autorizacion_imagen', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Autorizacion_de_uso_de_imagen_e_informacion_endoscopica' . '.pdf');
            } elseif ($formatoid == '13') {
                $view = \View::make('hc_admision.consentimientos.manometria_ano_rectal', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('manometria_ano_rectal' . '.pdf');
            } elseif ($formatoid == '14') {
                $view = \View::make('hc_admision.consentimientos.anestesia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anestesia' . '.pdf');
            } elseif ($formatoid == '15') {
                $view = \View::make('hc_admision.consentimientos.manometria_esofagica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('manometria_esofagica' . '.pdf');
            } elseif ($formatoid == '16') {
                $view = \View::make('hc_admision.consentimientos.ph_esofagica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('ph_esofagica' . '.pdf');
            } elseif ($formatoid == '17') {
                $view = \View::make('hc_admision.consentimientos.capsula_endoscopica', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('capsula_endoscopica' . '.pdf');
            } elseif ($formatoid == '18') {
                $view = \View::make('hc_admision.consentimientos.phmetria_cap', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('phmetria_cap' . '.pdf');
            } elseif ($formatoid == '19') {
                $view = \View::make('hc_admision.consentimientos.consentimiento_encuesta', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('consentimiento_encuesta' . '.pdf');
            } elseif ($formatoid == '20') {
                $view = \View::make('hc_admision.consentimientos.anexo_informativo', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anexo_informativo' . '.pdf');
            } elseif ($formatoid == '21') {
                $view = \View::make('hc_admision.consentimientos.anexo_anastesico', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('anexo_anastesico' . '.pdf');
            } elseif ($formatoid == '22') {
                $view = \View::make('hc_admision.consentimientos.endoscopica_percutanea', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('endoscopica_percutanea' . '.pdf');
            } elseif ($formatoid == '23') {
                $view = \View::make('hc_admision.consentimientos.broncoscopia', compact('date', 'paciente', 'consentimiento', 'agenda', 'nombre', 'empresa'))->render();
                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('broncoscopia' . '.pdf');
            }
        }
    }

    public function seguro_empresa(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id_hcproc = $request['id_hcproc'];

        $procedimiento = hc_procedimientos::find($id_hcproc);

        $input_ex2 = [
            'id_empresa' => $request['id_empresa'],
            'id_seguro'  => $request['id_seguro'],
        ];

        $procedimiento->update($input_ex2);
        return "ok";
    }
}
