<?php

namespace Sis_medico\Http\Controllers\hospital;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Agenda;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Opcion_Usuario;
class ImagenesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function viewImagenes($id_solicitud){
        $ho_solicitud = Ho_Solicitud::find($id_solicitud);
        $agenda = agenda::find($ho_solicitud->id_agenda);
        $hclinica = Historiaclinica::where('id_agenda',$agenda->id)->first();
        $hc_protocolo = hc_protocolo::where('hcid',$hclinica->hcid)->first();
        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo',$hc_protocolo->id)->orderBy('id','desc')->get();
        return view('hospital/imagenes/index', ['id_solicitud' => $id_solicitud, 'imagenes' => $imagenes]);
    }

    public function storeImage(Request $request){
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $ho_solicitud = Ho_Solicitud::find($request['id_solicitud']);
        $agenda = agenda::find($ho_solicitud->id_agenda);
        $hclinica = Historiaclinica::where('id_agenda',$agenda->id)->first();
        $hc_protocolo = hc_protocolo::where('hcid',$hclinica->hcid)->first();
        /****************Leyendo la imagen y moverla al servidor*******************/
        $file= $request->file('file');
        $extension       = $request['file']->getClientOriginalExtension();

        $destino = 'public/';
        $fileName= "hc_ima"."-".time().".".$extension;
        // $fileName = 'hc_ima_' . $hclinica->id_paciente . '_' . $hc_protocolo->id . '_' .time(). " - " . $file->getClientOriginalName();
        $upload = $request->file('file')->move($destino,$fileName);
        $upload;

        $input_archivo = [
            'id_hc_protocolo' => $hc_protocolo->id,
            'estado'          => 1,
            'nombre'          => $fileName,
            'nombre_anterior' => $destino.$fileName,
            'id_usuariocrea'  => $id_usuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_cliente,
        ];

        $imageNew = hc_imagenes_protocolo::create($input_archivo);
        return ["status" => "success", "message" => "Imagen guardada con éxito", "data"=>$imageNew->nombre];



        // // 'foto'=> $destino. $fileName,

        $id_solicitud = $request->id_solicitud;
        $nombre_original = $request['file']->getClientOriginalName();

        $extension       = $request['file']->getClientOriginalExtension();
        //dd($extension);

        $nuevo_nombre    =   "hc_image" . date('YmdHis') . "." . $extension;
        // dd(Storage::disk('public'));

        $r1 = Storage::disk('hc_ima')->put($nuevo_nombre, \File::get($request['file']));

        $rutadelaimagen = base_path() . '/storage/app/hc_ima/' . $nuevo_nombre;

        if ($r1) {
            // $arr_archivo = [
            //     'id_solicitud'      => $id_solicitud,
            //     'nombre_archivo'    => $nuevo_nombre
            // ];

            // Ho_Archivos_Solicitud::create($arr_archivo);
            $input_archivo = [
                'id_hc_protocolo' => $hc_protocolo->id,
                'estado'          => 1,
                'nombre'          => $nuevo_nombre,
                'nombre_anterior' => $rutadelaimagen,
                'id_usuariocrea'  => $id_usuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
            ];
        }

        // $file= $request->file('file');
        // $destino = 'storage/';
        // $fileName= time(). " - " . $file->getClientOriginalName();
        // $upload = $request->file('file')->move($destino,$fileName);
        
        // $upload;
        // // Storage::disk('hc_ima')->put($fileName, \File::get($file));
        // $r1              = Storage::disk('public')->put($fileName, \File::get($request['file']));

        /****************Fin Leyendo la imagen y moverla al servidor*******************/

       


        $imageNew = hc_imagenes_protocolo::create($input_archivo);
        // dd($imageNew);
        // $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo',$hc_protocolo->id)->get();
        return ["status" => "success", "message" => "Imagen guardada con éxito", "data"=>$imageNew->nombre];
    }

    public function verFotosBox (Request $request){
        // dd($request->all());
        try{
            $id_solicitud = $request['id_solicitud'];
            $ho_solicitud = Ho_Solicitud::find($id_solicitud);
            $agenda = agenda::find($ho_solicitud->id_agenda);
            $hclinica = Historiaclinica::where('id_agenda',$agenda->id)->first();
            $hc_protocolo = hc_protocolo::where('hcid',$hclinica->hcid)->first();
            $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo',$hc_protocolo->id)->orderBy('id','desc')->get();
            // dd($imagenes);
            return ["status"=> "success", "imagenes"=>$imagenes];
        }catch(\Exception $e){
            return ["status"=> "error", "exp"=>$e->getMessage()];
        }   
       
    }

    public function mostrar_foto_eliminar2($id)
    {
        $imagen = hc_imagenes_protocolo::find($id);
        return view('hospital/imagenes/modal', ['imagen' => $imagen]);
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

}
