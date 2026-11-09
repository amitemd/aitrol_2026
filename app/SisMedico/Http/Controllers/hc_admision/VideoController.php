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
use Sis_medico\OdontProcedimiento;
use Sis_medico\User;
use Sis_medico\Services\DicomConverter;

class VideoController extends Controller
{
    private $dicomConverter;

    public function __construct(DicomConverter $dicomConverter)
    {
        $this->dicomConverter = $dicomConverter;
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 5, 7,20)) == false) {
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
        $cvideo = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')
            ->where(function ($query) {
                $query->where('nombre', 'LIKE', '%.mp4')->orWhere('nombre', 'LIKE', '%.webm');
            })->count();

        $cimagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')
            ->where('nombre', 'NOT LIKE', '%.mp4')->where('nombre', 'NOT LIKE', '%.webm')->count();

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
                $fileName2 = 'hc_ima_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.jpg';
                $this->dicomConverter->convert($fileName, $fileName2);
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

    public function guardado_video_protocolo(Request $request, $id_protocolo)
    {
        if ($this->rol()) {
            abort(403, 'No tiene permisos para guardar videos clínicos.');
        }

        $protocolo = hc_protocolo::findOrFail($id_protocolo);
        $idPaciente = $protocolo->historiaclinica->id_paciente;
        $this->assertPatientInActiveCompany($idPaciente);

        $request->validate([
            'video' => 'required|file|max:512000',
        ]);

        $video = $request->file('video');
        $mime = strtolower((string) $video->getMimeType());
        $mimesPermitidos = array('video/mp4', 'video/webm', 'application/octet-stream');
        if (!in_array($mime, $mimesPermitidos, true)) {
            return response()->json(['message' => 'El archivo recibido no es un video válido.'], 422);
        }

        $idUsuario = Auth::user()->id;
        $ipCliente = $request->ip();
        $archivo = null;

        DB::beginTransaction();
        try {
            $idArchivo = hc_imagenes_protocolo::insertGetId([
                'id_hc_protocolo' => $id_protocolo,
                'estado' => 1,
                'id_usuariocrea' => $idUsuario,
                'ip_modificacion' => $ipCliente,
                'id_usuariomod' => $idUsuario,
                'ip_creacion' => $ipCliente,
            ]);

            // Se conserva MP4 y el disco "videos" porque las vistas históricas
            // reproducen estos archivos desde /uploads.
            $archivo = 'hc_ima_' . $idPaciente . '_' . $id_protocolo . '_' . date('Ymd') . '_' . $idArchivo . '.mp4';
            $stream = fopen($video->getRealPath(), 'r');
            if ($stream === false) {
                throw new \RuntimeException('No fue posible leer el video recibido.');
            }

            try {
                $guardado = Storage::disk('videos')->put($archivo, $stream);
            } finally {
                fclose($stream);
            }

            if (!$guardado) {
                throw new \RuntimeException('No fue posible almacenar el video.');
            }

            $registro = hc_imagenes_protocolo::findOrFail($idArchivo);
            $registro->nombre = $archivo;
            $registro->ip_modificacion = $ipCliente;
            $registro->id_usuariomod = $idUsuario;
            $registro->save();

            DB::commit();
            return response()->json(['id' => $idArchivo, 'archivo' => $archivo]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($archivo) {
                Storage::disk('videos')->delete($archivo);
            }
            report($e);
            return response()->json(['message' => 'No se pudo guardar el video.'], 500);
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
        $imagen = hc_imagenes_protocolo::findOrFail($id);
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
        if ($ruta === 'odontologia') {
            $procedimiento = OdontProcedimiento::where('id', $agenda_ori)
                ->where('id_hc_protocolo', $id)
                ->where('estado', 1)
                ->first();
            if (!$procedimiento) {
                return response()->view('errors.404', array(), 404);
            }
            return redirect()->route('odontologia.inicio.buscador', array(
                'id_paciente' => $procedimiento->id_paciente,
                'id_agenda' => $procedimiento->id_agenda,
            ))->with('odontologia_procedimiento_abrir', $procedimiento->id);
        }

        if ($ruta == '0') {
            return redirect()->route("agenda.detalle", ['id' => $agenda_ori]);
        } else {
            return redirect()->route("estudio.editar", ['id' => $id, 'agenda' => $agenda_ori]);
        }
        //dd($id,$agenda,$ruta);

    }

    public function guardar_video(Request $request)
    {
        if ($this->rol()) {
            abort(403, 'No tiene permisos para guardar videos clinicos.');
        }

        $protocolo = hc_protocolo::find($request->get('id_hc_protocolo'));
        $archivo = $request->file('video-blob');
        if (!$protocolo || !$archivo || !$archivo->isValid()) {
            return response('No se pudo guardar el video.', 422);
        }
        $this->assertPatientInActiveCompany($protocolo->historiaclinica->id_paciente);

        $totalPartes = (int) $request->get('video_total_partes', 0);
        if ($totalPartes > 0) {
            $numeroParte = (int) $request->get('video_numero_parte', -1);
            $idCarga = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $request->get('video_id_carga'));
            if ($idCarga === '' || $numeroParte < 0 || $numeroParte >= $totalPartes) {
                return response('Datos de la grabacion incompletos.', 422);
            }

            $directorioPartes = storage_path('app/video_chunks');
            if (!is_dir($directorioPartes) && !mkdir($directorioPartes, 0775, true) && !is_dir($directorioPartes)) {
                return response('No se pudo preparar el almacenamiento del video.', 500);
            }
            $rutaTemporal = $directorioPartes.DIRECTORY_SEPARATOR
                .Auth::id().'_'.$protocolo->id.'_'.$idCarga.'.part';
            if ($numeroParte === 0 && file_exists($rutaTemporal)) {
                unlink($rutaTemporal);
            }

            $origen = fopen($archivo->getRealPath(), 'rb');
            $destino = fopen($rutaTemporal, $numeroParte === 0 ? 'wb' : 'ab');
            if ($origen === false || $destino === false) {
                if (is_resource($origen)) { fclose($origen); }
                if (is_resource($destino)) { fclose($destino); }
                return response('No se pudo reconstruir el video.', 500);
            }
            stream_copy_to_stream($origen, $destino);
            fclose($origen);
            fclose($destino);

            if ($numeroParte < $totalPartes - 1) {
                return response('parte guardada', 200)->header('Content-Type', 'text/plain');
            }
            return $this->almacenarVideoGrabado(
                $protocolo,
                $rutaTemporal,
                strtolower((string) $request->get('video_mime', 'video/webm')),
                true
            );
        }

        return $this->almacenarVideoGrabado(
            $protocolo,
            $archivo->getRealPath(),
            strtolower((string) $archivo->getClientMimeType()),
            false
        );
    }

    private function almacenarVideoGrabado($protocolo, $rutaOrigen, $mime, $eliminarOrigen)
    {
        $extension = strpos($mime, 'webm') !== false ? 'webm' : 'mp4';
        $idUsuario = Auth::id();
        $ip = request()->ip();
        $nombre = null;

        DB::beginTransaction();
        try {
            $idArchivo = hc_imagenes_protocolo::insertGetId(array(
                'id_hc_protocolo' => $protocolo->id,
                'estado' => 1,
                'id_usuariocrea' => $idUsuario,
                'id_usuariomod' => $idUsuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));
            $nombre = 'hc_video_'.$protocolo->historiaclinica->id_paciente.'_'.$protocolo->id.'_'.date('Ymd').'_'.$idArchivo.'.'.$extension;
            $stream = fopen($rutaOrigen, 'rb');
            if ($stream === false) {
                throw new \RuntimeException('No se pudo leer el video.');
            }
            try {
                if (!Storage::disk('videos')->put($nombre, $stream)) {
                    throw new \RuntimeException('No se pudo escribir el video.');
                }
            } finally {
                fclose($stream);
            }
            hc_imagenes_protocolo::where('id', $idArchivo)->update(array(
                'nombre' => $nombre,
                'id_usuariomod' => $idUsuario,
                'ip_modificacion' => $ip,
            ));
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            if ($nombre) { Storage::disk('videos')->delete($nombre); }
            report($exception);
            return response('No se pudo almacenar el video.', 500);
        } finally {
            if ($eliminarOrigen && file_exists($rutaOrigen)) { unlink($rutaOrigen); }
        }

        return response((string) $idArchivo, 200)->header('Content-Type', 'text/plain');
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
             'id_anestesiologo'=> $request['anestesiologonew'],
             'instrumento'     => $request['instrumento'],
             'id_ayudante' =>$request['ayudantenew'],
             'premedicacion'=> $request['premedicacion'], 
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
        $this->assertPatientInActiveCompany($id_paciente);
        $paciente  = paciente::findOrFail($id_paciente);
        $historico = paciente_biopsia::where('id_paciente', $id_paciente)->where('id_empresa', session('id_empresa'))->where('estado', '1')->get();
        return view('hc_admision/video/laboratorio_externo_ingreso', ['paciente' => $paciente, 'historico' => $historico]);
    }

    public function guardar_examenes_externos(Request $request)
    {
        $patientId = trim((string) $request->input('id_paciente'));
        if ($patientId === '') {
            return response()->json(array('message' => 'No se recibió la identificación del paciente.'), 422);
        }

        $this->assertPatientInActiveCompany($patientId);
        Paciente::findOrFail($patientId);

        // Dropzone envía un archivo por petición. Se conserva compatibilidad
        // con las pantallas antiguas que todavía usan foto[] o file.
        $files = $request->file('foto', $request->file('file'));
        if ($files && !is_array($files)) {
            $files = array($files);
        }
        if (empty($files)) {
            return response()->json(array('message' => 'No se recibió ningún archivo para cargar.'), 422);
        }

        $allowedExtensions = array('pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif');
        $created = array();

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                return response()->json(array('message' => 'El archivo no pudo recibirse correctamente. Inténtalo nuevamente.'), 422);
            }

            $extension = strtolower((string) $file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions, true)) {
                return response()->json(array('message' => 'Formato no permitido. Usa PDF, Word, Excel, TXT, JPG, PNG o GIF.'), 422);
            }
            if ((int) $file->getSize() > 20 * 1024 * 1024) {
                return response()->json(array('message' => 'El archivo supera el tamaño máximo permitido de 20 MB.'), 422);
            }

            $fileName = null;
            DB::beginTransaction();
            try {
                $idArchivo = paciente_biopsia::insertGetId(array(
                    'id_paciente' => $patientId,
                    'estado' => '1',
                    'id_empresa' => session('id_empresa'),
                    'nombre_original' => basename((string) $file->getClientOriginalName()),
                    'id_usuariocrea' => Auth::id(),
                    'ip_modificacion' => $request->ip(),
                    'id_usuariomod' => Auth::id(),
                    'ip_creacion' => $request->ip(),
                ));

                $fileName = 'lab_externo_' . $patientId . '_' . date('Ymd') . '_' . $idArchivo . '.' . $extension;
                if (!Storage::disk('hc_ima')->put($fileName, \File::get($file))) {
                    throw new \RuntimeException('No fue posible almacenar el archivo.');
                }

                paciente_biopsia::where('id', $idArchivo)->update(array(
                    'nombre' => $fileName,
                    'ip_modificacion' => $request->ip(),
                    'id_usuariomod' => Auth::id(),
                ));
                DB::commit();
                $created[] = $idArchivo;
            } catch (\Exception $exception) {
                DB::rollBack();
                if ($fileName) {
                    Storage::disk('hc_ima')->delete($fileName);
                }
                report($exception);
                return response()->json(array('message' => 'No se pudo guardar el archivo. Inténtalo nuevamente.'), 500);
            }
        }

        $html = '';
        foreach ($created as $idArchivo) {
            $record = paciente_biopsia::where('id', $idArchivo)
                ->where('id_paciente', $patientId)
                ->where('id_empresa', session('id_empresa'))
                ->where('estado', '1')
                ->first();
            if ($record) {
                $html .= view('hc_admision.video._laboratorio_externo_archivo', array('imagen' => $record))->render();
            }
        }

        return response()->json(array(
            'message' => count($created) === 1 ? 'Archivo cargado correctamente.' : 'Archivos cargados correctamente.',
            'ids' => $created,
            'html' => $html,
        ), 201);
    }

    public function mostrar_lab_externo($id)
    {
        $imagen = $this->externalResultForActiveCompany($id);
        return view('hc_admision/video/modal_externo', ['imagen' => $imagen]);
    }

    public function descarga_externo($name)
    {
        $imagen = $this->externalResultForActiveCompany($name, '1');
        $storedName = basename((string) $imagen->nombre);
        $path = storage_path('app/hc_ima/' . $storedName);
        $originalName = trim((string)($imagen->nombre_original ?: $imagen->nombre_anterior));
        $downloadName = $originalName !== ''
            ? basename($originalName)
            : preg_replace('/^hc_biopsias_/i', 'lab_externo_', $storedName);
        $downloadName = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|', "\r", "\n"), '_', $downloadName);

        if (file_exists($path)) {
            return Response::download($path, $downloadName !== '' ? $downloadName : 'resultado_laboratorio');
        }
        abort(404, 'El archivo ya no se encuentra disponible.');
    }

    public function validar_descarga_externo($id)
    {
        try {
            $imagen = $this->externalResultForActiveCompany($id, '1');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception) {
            return response()->json(array(
                'disponible' => false,
                'mensaje' => $exception->getMessage() ?: 'El resultado externo ya no se encuentra disponible.',
            ), $exception->getStatusCode());
        }

        $storedName = basename((string) $imagen->nombre);
        $path = storage_path('app/hc_ima/' . $storedName);

        if ($storedName === '' || !is_file($path) || !is_readable($path)) {
            return response()->json(array(
                'disponible' => false,
                'mensaje' => 'El archivo de este resultado externo ya no se encuentra disponible. Comuníquese con Sistemas para recuperarlo.',
            ), 404);
        }

        return response()->json(array(
            'disponible' => true,
            'url_descarga' => url('laboratorio_externo_descarga/' . $imagen->id),
        ));
    }

    public function archivo_externo($id)
    {
        $imagen = $this->externalResultForActiveCompany($id, '1');
        $path = storage_path('app/hc_ima/' . basename($imagen->nombre));
        abort_unless(is_file($path), 404, 'El archivo ya no se encuentra disponible.');
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
        $originalName = trim((string)($imagen->nombre_original ?: $imagen->nombre_anterior));
        $inlineName = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($originalName !== '' ? $originalName : $imagen->nombre));
        return response()->file($path, array(
            'Content-Type' => $mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$inlineName.'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ));
    }

    public function eliminar_externo(Request $request, $id)
    {
        $imagen = $this->externalResultForActiveCompany($id, '1');
        $imagen->estado = '2';
        $imagen->id_usuariomod = Auth::id();
        $imagen->ip_modificacion = $request->ip();
        $imagen->save();
        return response()->json(array('ok' => true, 'message' => 'El examen externo se eliminó correctamente.'));
    }

    public function examenes_biopsias($id_paciente)
    {
        $this->assertPatientInActiveCompany($id_paciente);
        $paciente  = paciente::findOrFail($id_paciente);
        $historico = paciente_biopsia::where('id_paciente', $id_paciente)->where('id_empresa', session('id_empresa'))->where('estado', '0')->get();

        //dd($id_paciente);
        $historico2 = DB::table('historiaclinica')->join('agenda as a', 'a.id', '=', 'historiaclinica.id_agenda')
            ->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('historiaclinica.id_paciente', $id_paciente)->where('a.id_empresa', session('id_empresa'))
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
        $this->assertPatientInActiveCompany($cedula_paciente);
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();
            $input_archivo = [
                'id_paciente'     => $cedula_paciente,
                'estado'          => '0',
                'id_empresa'      => session('id_empresa'),
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

    private function assertPatientInActiveCompany($idPaciente)
    {
        if (!\Sis_medico\Agenda::where('id_paciente', $idPaciente)->where('id_empresa', session('id_empresa'))->exists()) {
            abort(404, 'El paciente no tiene agendas en la empresa activa.');
        }
    }

    private function externalResultForActiveCompany($id, $estado = null)
    {
        $idEmpresa = trim((string) session('id_empresa'));
        abort_unless($idEmpresa, 404, 'No existe una empresa activa.');

        // Se obtiene primero el registro y luego se valida su pertenencia. Así
        // los identificadores de empresa históricos con espacios no generan un
        // ModelNotFoundException, sin relajar el aislamiento entre empresas.
        $imagen = paciente_biopsia::where('id', (int) $id)->first();
        abort_unless($imagen, 404, 'El examen externo solicitado ya no existe.');

        $idEmpresaArchivo = trim((string) $imagen->id_empresa);
        abort_unless($idEmpresaArchivo === '' || hash_equals($idEmpresa, $idEmpresaArchivo), 404, 'El examen externo no pertenece a la empresa activa.');
        if (!is_null($estado)) {
            abort_unless((string) $imagen->estado === (string) $estado, 404, 'El examen externo ya no está disponible.');
        }

        // Los resultados actuales ya quedan aislados por su id_empresa. Los
        // registros históricos sin empresa conservan la validación adicional
        // por agenda para evitar exponerlos entre empresas.
        if ($idEmpresaArchivo === '') {
            $this->assertPatientInActiveCompany($imagen->id_paciente);
        }

        return $imagen;
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
            $zipper = app('madzipper');
            $zipper->make($path);

            $cont = 0;
            foreach ($arreglos as $value) {
                $cont++;
                $archivo = hc_imagenes_protocolo::find($value);
                //dd($value,$archivo);
                $nombre = $archivo->nombre;

                $explotar  = explode('.', $nombre);
                $extension = end($explotar);
                if (in_array(strtolower($extension), array('mp4', 'webm'), true)) {

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
}
