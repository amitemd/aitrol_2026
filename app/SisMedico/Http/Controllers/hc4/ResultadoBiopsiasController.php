<?php
namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\User; 
use Sis_medico\Opcion_Usuario;
use Sis_medico\Biopsias;
use Sis_medico\Biopsias_Result;
use Sis_medico\Firma_Usuario;
use Sis_medico\Paciente;
use Response;


class ResultadoBiopsiasController extends Controller
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

    


    public function obtener_resultado($id_paciente){

      $opcion = '2';
      if($this->rol_new($opcion)){
        return redirect('/');
      }

      $paciente = Paciente::find($id_paciente);

      //$tipo_usuario = Auth::user()->id_tipo_usuario;

      $tipo_usuario = 15;
      $group_biopsias = Biopsias::where('id_tipo_usuario', $tipo_usuario)
                                 ->where('id_paciente', $id_paciente)
                                 ->groupBy("hc_id_procedimiento")
                                 ->OrderBy('hc_id_procedimiento', 'desc')->get();

      return view('hc4/biopsias/resultados_biopsias',['paciente' => $paciente,'group_biopsias' => $group_biopsias, 'tipo_usuario' => $tipo_usuario]);

    }

    public function imprimir_resultado_frasco($id){



        $detalle_frascos = Biopsias::where('id', $id)->first();

        $biopsia_resultado = Biopsias_Result::where('id_hc_biopsia', $id)->first();

        if (!is_null($detalle_frascos->id_doctor)) {
                $firma = Firma_Usuario::where('id_usuario',$detalle_frascos->id_doctor)->first();
        }


        $paciente = Paciente::find($detalle_frascos->id_paciente);
   
        /*if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }*/
    

        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$detalle_frascos->id_doctor)
                              ->first();


        $vistaurl="hc4.biopsias.pdf_resultado_frasco_biopsias";
        $view =  \View::make($vistaurl, compact('detalle_frascos','biopsia_resultado','paciente','doctor_solicitante','firma'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('Resultado-Frasco'.$id.'.pdf');

        


    }


}
    