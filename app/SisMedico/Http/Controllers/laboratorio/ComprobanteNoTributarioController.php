<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Empresa;
use Sis_medico\Paciente;
use Sis_medico\User;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;

class ComprobanteNoTributarioController  extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }
    public function pdf($id)
    {
        
        $examen_orden = Examen_Orden::where('id', $id)->whereNotNull('fecha_envio')->first();
        $empresa = Empresa::find('0993075000001');
       // dd($examen_orden);
        if(!is_null($examen_orden)){
            $paciente = Paciente::find($examen_orden->id_paciente);
            $grupo_familiar = Labs_Grupo_Familiar::find($examen_orden->id_paciente);
            if (!is_null($grupo_familiar)) {
                //BUSCA SI EL CORREO EXISTE EN OTRO USUARIO
                $usuario_mail = User::find($grupo_familiar->id_usuario);
            } else {
                //BUSCA SI EXISTE EL CORREO
                $usuario_mail = User::where('id', $paciente->id_usuario)->first();

            }

        }
        
        if(!is_null($examen_orden)){
      
            $vistaurl = "laboratorio.compnotributario.pdf_trbutario";
            $view     = \View::make($vistaurl, compact('examen_orden','empresa', 'usuario_mail'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $customPaper = array(0,0,283.80,700.00);
            $pdf->setPaper($customPaper, 'portrait');
            return $pdf->stream('Pdf Tributario ' . '.pdf');
        }else{
            return "No existe comprobante";
        }
    }

    public function pdf_cotizacion($id)
    {
        $orden      = Examen_Orden::find($id);
        $detalles   = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //$empresa = Empresa::find('0993075000001');
        $empresa = Empresa::where('prioridad_labs','1')->first();
        
        if(!is_null($orden)){
            $paciente = Paciente::find($orden->id_paciente);
            $grupo_familiar = Labs_Grupo_Familiar::find($orden->id_paciente);
            if (!is_null($grupo_familiar)) {
                //BUSCA SI EL CORREO EXISTE EN OTRO USUARIO
                $usuario_mail = User::find($grupo_familiar->id_usuario);
            } else {
                //BUSCA SI EXISTE EL CORREO
                $usuario_mail = User::where('id', $paciente->id_usuario)->first();

            }

        }
        $vistaurl = "laboratorio.compnotributario.pdf_cotizacion";
        $view     = \View::make($vistaurl, compact('orden','empresa', 'detalles', 'age', 'forma_pago', 'usuario_mail'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $customPaper = array(0,0,283.80,700.00);
        $pdf->setPaper($customPaper, 'portrait');
        return $pdf->stream('Pdf Cotizacion' . '.pdf');
    }
}