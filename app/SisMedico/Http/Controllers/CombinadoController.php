<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Paciente;
use Sis_medico\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Principio_Activo;

class CombinadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 6)) == false){
          return true;
        }
    }

    public function ingreso(Request $request) //admision_datos.doctor
    {
    	$idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id= $request["id_paciente"];
        //return $request->all();
        $paciente = Paciente::find($id);

        $rule = [
            'telefono1' => 'required',
            'mail' => 'required|email',
        ];
        //'required|email|unique:users,email,'.$id,
        $msn = [
            'telefono1.required' => 'Ingrese el telefono',
            'mail.required' => 'Ingrese el mail',
            'mail.unique' => 'Mail registrado en otro paciente',
            'mail.email' => 'Mail ingresado con formato incorrecto',
        ];

         
        $this->validate($request,$rule,$msn);

        $input_u =  [
            'email' => $request['mail'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 

        ];



        if($paciente->parentesco=='Principal'){

            User::find($paciente->id)->update($input_u);
        }else{
            User::find($paciente->id_usuario)->update($input_u);
        }

        $alergiasxpac = Paciente_Alergia::where('id_paciente',$id)->get();

        foreach ($alergiasxpac as $apac) {
            $apac->delete();    
        } 

        $alergia_txt = "";
        $ale_flag=true;
        //return $request->ale_list;
        if($request->ale_list!=null){
            foreach ($request->ale_list as $ale) {

                if(is_numeric($ale)){
                    $generico = Principio_Activo::find($ale);
                }else{
                    $input_principio = [
                        'nombre' => substr(strtoupper($ale), 0,-5),
                        'descripcion' => substr(strtoupper($ale), 0,-5),
                        'estado' => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario, 
                    ];
                    $ale = Principio_Activo::insertGetId($input_principio);
                    $generico = Principio_Activo::find($ale);
                }
                $generico = Principio_Activo::find($ale);
                $pac_ale = [

                        'id_paciente' => $id,
                        'id_principio_activo' => $ale,
                        'ip_creacion' => $ip_cliente,   
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'id_usuariocrea' => $idusuario,


                ];
                Paciente_Alergia::create($pac_ale);
                if($ale_flag){
                    $alergia_txt = $generico->nombre;
                    $ale_flag=false;    
                }else{
                    $alergia_txt = $alergia_txt.'+'.$generico->nombre;    
                }
                
            }    
        }
        

        $input1 = [                                   
            'sexo' => $request["sexo"],
            'referido' => strtoupper($request["referido"]),
            'alcohol' => strtoupper($request["alcohol"]),
            'ciudad' => strtoupper($request["ciudad"]),
            'estadocivil' => $request["estadocivil"],
            'direccion' => strtoupper($request["direccion"]),
            //'alergias' => $request["alergias"],
            'alergias' => $alergia_txt,
            'lugar_nacimiento' => strtoupper($request["lugar_nacimiento"]),
            'telefono1' => $request["telefono1"],
            'telefono2' => $request["telefono2"],
            
            'ocupacion' => strtoupper($request["ocupacion"]),
            'trabajo' => strtoupper($request["trabajo"]),
            
            'transfusion' => $request["transfusion"],
            'observacion' => $request["observacion"],
            'vacuna' => $request["vacuna"],
            'antecedentes_pat' => $request["antecedentes_pat"],
            'antecedentes_fam' => $request["antecedentes_fam"],
            'antecedentes_quir' => $request["antecedentes_quir"],
            'gruposanguineo' => $request["gruposanguineo"],
            'fecha_nacimiento' => $request["fecha_nacimiento"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        
        $paciente->update($input1); 
        return "exito";
    }
}
