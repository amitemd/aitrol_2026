<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\hospital_formulario053;
use Sis_medico\Hosp_dato_paciente;
use Sis_medico\hospital053_hallazgos;
use Sis_medico\formulario053_diagnostico;
use Sis_medico\formulario053_tratamiento;
use Sis_medico\formulario053_referencia;
use Sis_medico\CamaPaciente;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;


class Formulario053Controller extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    public function formulario053($id_paciente, $id_cama)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $datos = DB::table('paciente')
            ->join('hosp_dato_paciente', 'paciente.id', '=', 'hosp_dato_paciente.id_paciente')
            ->where('paciente.id', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.estadocivil', 'paciente.trabajo', 'paciente.id_seguro', 'hosp_dato_paciente.canto', 'paciente.ciudad', 'hosp_dato_paciente.parroquia', 'paciente.historia_clinica', 'hosp_dato_paciente.edad', 'paciente.sexo', 'paciente.id', 'paciente.created_at')
            ->get();
        $log = CamaPaciente::where('id_cama', '=', $id_cama)->where('id_paciente', $id_paciente)->get();
        //dd($log);

        return view('hospital/habitacion/formulario053', ['id_paciente' => $id_paciente, 'id_cama' => $id_cama, 'datos' => $datos, 'log' => $log]);
    }

    public function guardar_informacion(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        hospital_formulario053::create([
            'notas_doctor' => $request['cuadro_clinico'],
            'id_paciente' => $request['id_paciente'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);

        return redirect()->back()->with('success', 'Guardado Doctor');
    }

    public function formulario053_hallazgos(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        hospital053_hallazgos::create([
            'cuadro_procedimiento' => $request['cuadro_procedimiento'],
            'id_paciente' => $request['id_paciente'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);

        return redirect()->back()->with('dato', 'Guardado Hallazgo');
    }

    public function formulario053_diagnostico(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request->all());
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->cie) > 0) {
            foreach ($request->cie as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'cuadro_procedimiento' => $request->cuadro_procedimiento[$item],
                    'cie' => $request->cie[$item],
                    'pre' => $request->pre[$item],
                    'def' => $request->def[$item],
                );
                formulario053_diagnostico::insert($data2);
            }
        }
        return redirect()->back()->with('ok', 'Guardado Diagnostico');
    }

    public function formulario053_tratamiento(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        formulario053_tratamiento::create([
            'notas_doctor' => $request['cuadro_tratamiento'],
            'id_paciente' => $request['id_paciente'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);

        return redirect()->back()->with('guardado', 'Guardado Hallazgo');
    }


    public function formulario053_autucompletar(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', id, descripcion) as completo
                  FROM `Cie_10_3`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $query1 = "SELECT CONCAT_WS(' ', id, descripcion) as completo1
                  FROM `Cie_10_4`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        $nombres1 = DB::select($query1);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        foreach ($nombres1 as $product) {
            $data[] = array('value' => $product->completo1);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function formulario053_referencia(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        formulario053_referencia::create([
            'diagnostico' => $request['cuadro_diagnostico'],
            'id_paciente' => $request['id_paciente'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);

        return redirect()->back()->with('referencia', 'Guardado Referencia');
    }

    public function formulario053_resultado(Request $request, $id_paciente, $id_cama)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $log = CamaPaciente::where('id_cama', '=', $id_cama)->where('id_paciente', $id_paciente)->get();
        $prescri = hospital_formulario053::where('id_paciente', '=', $id_paciente)->paginate(5);
        $users = DB::table('paciente')
            ->join('hosp_dato_paciente', 'paciente.id', '=', 'hosp_dato_paciente.id_paciente')
            ->where('paciente.id', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        $fecha     = $request['fecha'];
        $fechafin  = $request['fechafin'];

        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }

        $variable1 = hospital_formulario053::whereBetween('created_at', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->paginate(5);
        //dd($variable1);

        return view('hospital/habitacion/formulario053_resultado', ['prescri' => $prescri, 'users' => $users, 'log' => $log, 'id_cama' => $id_cama, 'fecha' => $fecha, 'fechafin' => $fechafin, 'variable1' => $variable1]);
    }
}
