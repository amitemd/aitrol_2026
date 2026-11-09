<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User; 
use Sis_medico\Bodega;
use Sis_medico\hc_procedimientos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\hc_receta;
use Sis_medico\Paciente;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Principio_Activo;
use Response;

class ResultadosExternosController extends Controller
{
    private function rol_new($opcion){ //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
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

    public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $paciente = Paciente::findOrFail($id_paciente);
        $idEmpresa = session('id_empresa');
        abort_unless($idEmpresa, 404, 'No existe una empresa activa.');

        $biopsiasQuery = Paciente_biopsia::where('id_paciente', $id_paciente)
            ->where('estado', '1')
            ->where(function ($query) use ($idEmpresa) {
                $query->where('id_empresa', $idEmpresa)->orWhereNull('id_empresa');
            });

        // Los resultados históricos pueden no tener id_empresa y el paciente
        // puede no conservar una agenda vinculada a la empresa activa. Si el
        // resultado ya pertenece al alcance visible de la empresa, debe poder
        // abrirse desde la historia clínica.
        $hasAgendaInActiveCompany = Agenda::where('id_paciente', $id_paciente)
            ->where('id_empresa', $idEmpresa)
            ->exists();
        $hasVisibleExternalResults = (clone $biopsiasQuery)->exists();

        abort_unless(
            $hasAgendaInActiveCompany || $hasVisibleExternalResults,
            404,
            'El paciente no pertenece a la empresa activa.'
        );

        $biopsias_1 = $biopsiasQuery->orderBy('created_at', 'desc')->get(); //trae todos
       // dd($biopsias_1);
       	return view('hc4/resultados_externos/index', ['biopsias_1' => $biopsias_1,'paciente' => $paciente]);
    }
}
