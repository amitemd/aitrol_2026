<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Agenda;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Biopsia;
use Image;
use Sis_medico\User;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 5, 7)) == false) {
            return true;
        }
    }

    public function mostrar($id_protocolo, $agenda_ori, $ruta)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $paciente  = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda    = Agenda::find($protocolo->historiaclinica->id_agenda);

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->get();
        //$imagenes2 = hc_imagenes_protocolo::all();
        $imagenes2 = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  hc_protocolo hc_proto,  historiaclinica hc, paciente p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $protocolo->historiaclinica->id_paciente . "'
                ORDER BY id desc;");
        $cvideo = hc_imagenes_protocolo::where('nombre', 'LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        $cimagenes = hc_imagenes_protocolo::where('nombre', 'NOT LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        return view('hc_admision/video/video', ['protocolo' => $protocolo, 'paciente' => $paciente, 'imagenes' => $imagenes, 'agenda' => $agenda, 'id' => $id_protocolo, 'imagenes2' => $imagenes2, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta, 'cvideo' => $cvideo, 'cimagenes' => $cimagenes]);
    }
    public function mostrar_documento($id_protocolo, $agenda_ori, $ruta)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $paciente  = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda    = Agenda::find($protocolo->historiaclinica->id_agenda);

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '2')->get();

        return view('hc_admision/video/documento', ['protocolo' => $protocolo, 'paciente' => $paciente, 'imagenes' => $imagenes, 'agenda' => $agenda, 'id' => $id_protocolo, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta]);
    }

    public function mostrar_estudios($id_protocolo, $agenda_ori, $ruta)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $paciente  = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda    = Agenda::find($protocolo->historiaclinica->id_agenda);

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '3')->get();

        return view('hc_admision/video/estudios', ['protocolo' => $protocolo, 'paciente' => $paciente, 'imagenes' => $imagenes, 'agenda' => $agenda, 'id' => $id_protocolo, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta]);
    }

    public function mostrar_biopsias($id_protocolo, $agenda_ori, $ruta)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $paciente  = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda    = Agenda::find($protocolo->historiaclinica->id_agenda);

        $imagenes  = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '4')->get();
        $imagenes2 = Paciente_Biopsia::where('id_paciente', $paciente->id)->where('estado', '0')->get();
        //dd($paciente->id);

        return view('hc_admision/video/biopsias', ['protocolo' => $protocolo, 'paciente' => $paciente, 'imagenes' => $imagenes, 'agenda' => $agenda, 'id' => $id_protocolo, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta, 'imagenes2' => $imagenes2]);
    }

    public function guardado_foto(Request $request, $id_protocolo)
    {
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $idhc        = $id_protocolo;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;

        //return "hola";
        $path          = public_path() . '/app/hc/';
        $input_archivo = [
            'id_hc_protocolo' => $idhc,
            'estado'          => 1,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
        ];
        //sacar la extensio
        $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
        //nuevo nombre del archivo
        $fileName = 'hc_ima_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.png';

        //ingresar la foto
        //$file->save('storage/app/hc/bar.jpg', 60);
        $image = $request->imgBase64;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        \File::put(storage_path() . '/app/hc_ima/' . $fileName, base64_decode($image));
        //Storage::disk('hc')->put($fileName,  \File::get($file));
        //ACTUALIZAR LOS DATOS
        $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

        $archivo_historico->nombre          = $fileName;
        $archivo_historico->ip_modificacion = $ip_cliente;
        $archivo_historico->id_usuariomod   = $idusuario;
        $archivo_historico->save();

        $enviar["id"]      = $id_archivo;
        $enviar["archivo"] = $fileName;
        return $enviar;
    }

    public function guardado_foto2(Request $request)
    {

        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 1,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_ima_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            if ($extension == 'dcm') {
                $ubicacion = public_path() . '/storage/app/hc_ima/';
                $fileName2 = 'hc_ima_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.jpg';
                $original  = base64_encode($fileName);
                $final     = base64_encode($fileName2);
                $restante  = json_decode(file_get_contents(asset('../storage/app/hc_ima/dicom.php?original=') . $original . '&final=' . $final), true);
                $fileName  = $fileName2;
            }
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
    }

    public function guardado_foto2_documento(Request $request)
    {
        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 2,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_doc_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $nombre_anterior                    = $file->getClientOriginalName();
            $archivo_historico->nombre_anterior = $nombre_anterior;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
    }

    public function guardado_foto2_estudios(Request $request)
    {
        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 3,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_estudios_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $nombre_anterior                    = $file->getClientOriginalName();
            $archivo_historico->nombre_anterior = $nombre_anterior;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
    }

    public function guardado_foto2_biopsias(Request $request)
    {
        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 4,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension

            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_biopsias_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $nombre_anterior                    = $file->getClientOriginalName();
            $archivo_historico->nombre_anterior = $nombre_anterior;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
    }
    public function guardado_foto3_biopsias(Request $request)
    {
        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $i          = 1;

        foreach ($files as $file) {

            $cedula_paciente  = $file->getClientOriginalName();
            $extension        = $file->getClientOriginalExtension();
            $cantidad_extraer = strlen($extension) + 1;
            $negativo         = $cantidad_extraer * (-1);
            $cedula_paciente  = substr($cedula_paciente, 0, $negativo);
            $input_archivo    = [
                'id_paciente'     => $cedula_paciente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension

            $paciente = Paciente::findOrFail($cedula_paciente);
            if (!is_null($paciente)) {
                $id_archivo = paciente_biopsia::insertGetId($input_archivo);
                //nuevo nombre del archivo
                $fileName = 'hc_biopsias_' . $cedula_paciente . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
                //ingresar la foto
                Storage::disk('hc_ima')->put($fileName, \File::get($file));
                //ACTUALIZAR LOS DATOS
                $archivo_historico                  = paciente_biopsia::find($id_archivo);
                $archivo_historico->nombre          = $fileName;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $r2                                 = $archivo_historico->save();
                $i                                  = $i + 1;
            }
        }
    }
    public function guardar_antiguas2(Request $request)
    {
        $arreglos        = $request['image'];
        $id_hc_protocolo = $request['id_hc_protocolo'];
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        foreach ($arreglos as $value) {
            $archivo       = paciente_biopsia::find($value);
            $nombre        = $archivo->nombre;
            $input_archivo = [
                'id_hc_protocolo' => $id_hc_protocolo,
                'nombre'          => $nombre,
                'estado'          => 4,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            hc_imagenes_protocolo::insert($input_archivo);
            $archivo->delete();
        }
        return back();
    }

    public function mostrar_foto($id)
    {
        $imagen = hc_imagenes_protocolo::find($id);
        return view('hc_admision/video/modal', ['imagen' => $imagen]);
    }

    public function eliminar_foto($id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'estado'          => '0',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_imagenes_protocolo::where('id', $id)
            ->update($input1);
        return "Archivo eliminado correctamente";
    }

    public function eliminar_fotos(Request $request)
    {


        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        foreach ($request['image'] as $value) {
            $input1 = [
                'estado'          => $value,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            hc_imagenes_protocolo::where('id', $value)
                ->update($input1);
        }

        return back();
    }

    public function guardar_antiguas(Request $request)
    {
        $arreglos = $request['image'];
        //return $arreglos;
        $id_hc_protocolo = $request['id_hc_protocolo'];
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        if (!is_null($arreglos)) {
            foreach ($arreglos as $value) {
                $archivo       = hc_imagenes_protocolo::find($value);
                $nombre        = $archivo->nombre;
                $input_archivo = [
                    'id_hc_protocolo' => $id_hc_protocolo,
                    'nombre'          => $nombre,
                    'estado'          => 1,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                ];
                hc_imagenes_protocolo::insert($input_archivo);
            }
        }

        return back();
    }

    public function regreso($id, $agenda_ori, $ruta)
    {

        if ($ruta == '0') {
            return redirect()->route("agenda.detalle", ['id' => $agenda_ori]);
        } else {
            return redirect()->route("estudio.editar", ['id' => $id, 'agenda' => $agenda_ori]);
        }
        //dd($id,$agenda,$ruta);

    }

    public function guardar_video(Request $request)
    {

        header("Access-Control-Allow-Origin: *");
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $file     = $request->file('video-filename');
        $filename = "prueba.mp4";
        Storage::disk('hc_ima')->put($filename, file_get_contents($file));

        return 'success';
    }

    public function fecha_convenios(Request $request)
    {
        ///return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id        = $request['id'];

        $protocolo = hc_protocolo::find($id);
        $input_ex1 = [
            'fecha'           => $request['fecha'],
            'referido_por'    => $request['referido'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);

        $input_ex2 = [
            'id_doctor_examinador2' => $request['id_doctor_examinador2'],
            'id_doctor_responsable' => $request['id_doctor_responsable'],
        ];

        $procedimiento->update($input_ex2);
        $protocolo->update($input_ex1);
        return "ok";
    }

    public function examenes_externos($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = paciente_biopsia::where('id_paciente', $id_paciente)->where('estado', '1')->get();
        return view('hc_admision/video/laboratorio_externo_ingreso', ['paciente' => $paciente, 'historico' => $historico]);
    }

    public function cobro_hc($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = paciente_biopsia::where('id_paciente', $id_paciente)->where('estado', '4')->get();
        return view('hc_admision/video/comp_cobro_hc', ['paciente' => $paciente, 'historico' => $historico]);
    }

    public function guardar_examenes_externos(Request $request)
    {
        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cedula_paciente = $request['id_paciente'];
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();
            $input_archivo = [
                'id_paciente'     => $cedula_paciente,
                'estado'          => '1',
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension

            $paciente = Paciente::findOrFail($cedula_paciente);
            if (!is_null($paciente)) {
                $id_archivo = paciente_biopsia::insertGetId($input_archivo);
                //nuevo nombre del archivo
                $fileName = 'hc_biopsias_' . $cedula_paciente . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
                //ingresar la foto
                Storage::disk('hc_ima')->put($fileName, \File::get($file));
                //ACTUALIZAR LOS DATOS
                $archivo_historico                  = paciente_biopsia::find($id_archivo);
                $archivo_historico->nombre          = $fileName;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $r2                                 = $archivo_historico->save();
                $i                                  = $i + 1;
            }

        }
    }

    public function guardar_cobro_hc(Request $request)
    {
        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cedula_paciente = $request['id_paciente'];
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();
            $input_archivo = [
                'id_paciente'     => $cedula_paciente,
                'estado'          => '4',
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension

            $paciente = Paciente::findOrFail($cedula_paciente);
            if (!is_null($paciente)) {
                $id_archivo = paciente_biopsia::insertGetId($input_archivo);
                //nuevo nombre del archivo
                $fileName = 'cobros_hc_' . $cedula_paciente . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
                //ingresar la foto
                Storage::disk('hc_ima')->put($fileName, \File::get($file));
                //ACTUALIZAR LOS DATOS
                $archivo_historico                  = paciente_biopsia::find($id_archivo);
                $archivo_historico->nombre          = $fileName;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $r2                                 = $archivo_historico->save();
                $i                                  = $i + 1;
            }

        }
    }

    public function mostrar_lab_externo($id)
    {
        $imagen = paciente_biopsia::find($id);
        return view('hc_admision/video/modal_externo', ['imagen' => $imagen]);
    }

    public function ver_archivo_biopsia($id_archivo)
    {
        if (!is_null($id_archivo)) {
            $pathtoFile = storage_path() . '/app/hc_ima/' . $id_archivo;
            //dd($pathtoFile);
            if (file_exists($pathtoFile)) {
                return response()->file($pathtoFile);
            }
        }
        return "no cargado";
    }

    public function descarga_externo($name)
    {
        $imagen          = paciente_biopsia::find($name);
        $paciente        = paciente::find($imagen->id_paciente);
        $nombre_paciente = null;
        $nombre_paciente = $paciente->apellido1;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre;
        $nombre_paciente = $nombre_paciente . '_' . $paciente->nombre1;
        //dd($path);

        if ($nombre_paciente == null) {
            $nombre_archivo = $imagen->nombre;
        } else {
            $nombre_temporal = $imagen->nombre;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_paciente . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre;
                }
            } else {
                $nombre_archivo = $imagen->nombre;
            }
        }
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function examenes_biopsias($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = paciente_biopsia::where('id_paciente', $id_paciente)->where('estado', '0')->get();

        //dd($id_paciente);
        $historico2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();
        return view('hc_admision/video/biopsias_ingreso', ['paciente' => $paciente, 'historico' => $historico, 'historico2' => $historico2]);
    }
    public function guardar_biopsias_nuevo(Request $request)
    {
        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cedula_paciente = $request['id_paciente'];
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();
            $input_archivo = [
                'id_paciente'     => $cedula_paciente,
                'estado'          => '0',
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension

            $paciente = Paciente::findOrFail($cedula_paciente);
            if (!is_null($paciente)) {
                $id_archivo = paciente_biopsia::insertGetId($input_archivo);
                //nuevo nombre del archivo
                $fileName = 'hc_biopsias_' . $cedula_paciente . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
                //ingresar la foto
                Storage::disk('hc_ima')->put($fileName, \File::get($file));
                //ACTUALIZAR LOS DATOS
                $archivo_historico                  = paciente_biopsia::find($id_archivo);
                $archivo_historico->nombre          = $fileName;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $r2                                 = $archivo_historico->save();
                $i                                  = $i + 1;
            }
        }
    }

    public function recortar_todas($id_protocolo)
    {
        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->get();
        foreach ($imagenes as $value) {
            $imagen                         = hc_imagenes_protocolo::find($value->id);
            $imagen->seleccionado_recortada = 1;
            $imagen->save();
        }
        return 1;
    }

    public function descargar_zip(Request $request)
    {

        $arreglos = $request['image'];
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        if (!is_null($arreglos)) {
            $paciente = paciente::find($request['id_paciente']);
            $path     = public_path("zip/" . $paciente->apellido1 . "-" . $paciente->nombre1 . "-" . $paciente->id . ".zip");
            if (is_file($path)) {
                unlink($path);
            }
            $zipper = new \Chumper\Zipper\Zipper;
            $zipper->make($path);

            $cont = 0;
            foreach ($arreglos as $value) {
                $cont++;
                $archivo = hc_imagenes_protocolo::find($value);
                //dd($value,$archivo);
                $nombre = $archivo->nombre;

                $explotar  = explode('.', $nombre);
                $extension = end($explotar);
                if ($extension == 'mp4') {

                    $zipper->add(public_path('uploads') . "/{$nombre}");
                } else {

                    $zipper->add(storage_path('app/hc_ima') . "/{$nombre}");
                }
            }

            $zipper->close();
            return response()->download($path);
        } else {
            return back();
        }
        //return back();
    }

    public function eliminar_biopsia($id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'estado'          => '2',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        paciente_biopsia::where('id', $id)
            ->update($input1);
        return "Archivo eliminado correctamente";
    }

    public function thumbs($filename)
    {

        // Ruta a la imagen original
        $originalImagePath = storage_path('/app/hc_ima/') . $filename;

        if (file_exists($originalImagePath)) {
            // Abrir la imagen original
            $img = Image::make($originalImagePath);
            // Redimensionar la imagen a un tamaño específico (por ejemplo, 200KB)
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Guardar la imagen reducida en una ruta temporal
            $tempImagePath = storage_path('app/') . $filename;
            $img->save($tempImagePath);

            // Descargar la imagen reducida
            return response()->download($tempImagePath)->deleteFileAfterSend(true);
        }
    }
}
