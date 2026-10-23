<?php
namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Medicina;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\User;

class RecetaUsuarioController extends Controller
{

    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_paciente(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(2)) == false){
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



    //FUNCION HISTORIAL_RECETA_PACIENTE
    //MUESTRA EL HISTORIAL DE RECETAS DEL PACIENTE
    //SOLO PARA DOCTORES
    public function recetas_usuario(){

        if($this->rol_paciente()){
            return response()->view('errors.404');
        }

        // $opcion = '2';
        // if ($this->rol_new($opcion)) {
        //     return redirect('/');
        // }
        $id_paciente = Auth::user()->id;
        
        //Buscamos si el Usuario que se Loguea se encuentra Registrado en la Tabla Pacientes
        $paciente = Paciente::find($id_paciente);

        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();

        //Si no se encuentra en la Tabla Paciente no puede tener recetas asignadas
        if(!is_null($paciente)){

            $hist_recetas = DB::table('hc_receta as r')
                ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
                ->join('paciente as p','p.id','h.id_paciente')
                ->join('users as usuario','usuario.id','p.id_usuario')
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->join('seguros as s', 's.id', 'h.id_seguro')
                ->join('users as d', 'd.id', 'h.id_doctor1')
                ->where('usuario.id',$id_paciente)
                ->whereNotNull('r.prescripcion')
                ->whereNotNull('r.rp')
                ->orderBy('a.fechaini', 'desc')
                ->select('r.*', 'a.fechaini', 's.nombre as snombre', 'h.fecha_atencion','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.id as id_paciente','p.parentesco as parentesco','d.nombre1 as dnombre1', 'd.apellido1 as dapellido1','h.id_doctor1','p.fecha_nacimiento as fecha_nacimiento')
                ->get();

            return view('paciente/recetas_usuario/index', ['hist_recetas' => $hist_recetas, 'paciente' => $paciente,'usuarios' => $usuarios]);

        }    
    
    
    }

    //FUNCION IMPRIME
    //PERMITE IMPRIMIR LA RECETA EN HOJA MEMBRETADA Y SIN MENBRETAR
    //SOLO PARA DOCTORES
    public function imprime($id, $tipo){

        if($this->rol_paciente()){
            return response()->view('errors.404');
        }

        $receta   = hc_receta::find($id);
        $historia = historiaclinica::find($receta->id_hc);
        //return $historia;
        $paciente  = paciente::find($historia->id_paciente);
        $edad      = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::where('estado', 1)->get();
        $cie10     = hc_cie10::where('hcid', $receta->id_hc)->get();

        $firma = null;
        if (!is_null($historia->hc_procedimientos)) {
            $id_doctor = $historia->hc_procedimientos->id_doctor_examinador;
            $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
        }

        if ($tipo == 2) {
            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma'))->render();

        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();

        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');

        return $pdf->stream($historia->id_paciente . '_Receta_' . $id . '.pdf');

    }

}
