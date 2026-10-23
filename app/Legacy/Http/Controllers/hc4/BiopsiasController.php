<?php
namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\User; 
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente;
use Sis_medico\Hc_informe_biopsia;
use Response;


class BiopsiasController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

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

    //MUESTRA EL HISTORIA DE BIOPSIA_1 Y BIOPSIAS_2 DE UN PACIENTE
    //SOLO PARA DOCTORES 
    public function crear($id_paciente){
      $opcion = '2';
      if($this->rol_new($opcion)){
        return redirect('/');
      }


      $paciente = Paciente::find($id_paciente);
        
      $biopsias_1 = Paciente_biopsia::where('id_paciente', $id_paciente)
                    ->where('estado', '0')->OrderBy('created_at', 'desc')->get();
                      
      $biopsias_2 = DB::table('historiaclinica')
                    ->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
                    ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
                    ->where('id_paciente', $id_paciente)
                    ->where('hc_imagenes_protocolo.estado', '4')
                    ->OrderBy('hc_imagenes_protocolo.created_at', 'desc')->get();
      
      $biopsias_3 = Hc_informe_biopsia::where('id_paciente', $id_paciente)->OrderBy('created_at', 'desc')->get();
      
      return view('hc4/biopsias/index',['biopsias_1' => $biopsias_1,'biopsias_2' => $biopsias_2, 'biopsias_3' => $biopsias_3,'paciente' => $paciente]);
    
    }
}
    