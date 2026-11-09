<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Sis_medico\Diagnostico_005;
use Sis_medico\Evolucion_005;
use Sis_medico\Formulario005;
use Sis_medico\Hospital_Salas;
use Sis_medico\Medidas_generales;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Plan_005;
use Sis_medico\Tratamiento_005;

class ClinicalHospitalFormService
{
    private function rol_new($option)
    {
        $role = Auth::user()->id_tipo_usuario;
        return is_null(Opcion_Usuario::where('id_opcion', $option)->where('id_tipo_usuario', $role)->first());
    }

    private function validate(Request $request, array $rules, array $messages = array())
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function medicamentos(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        Formulario005::create([
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medi'],
            'cantidad_medicinas' => $request['cantidad'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
            'id_paciente' => $request['id_paciente'],
        ]);

        return back()->with('ok', 'Guardado con exito:');
    }

    public function formuarioevolucion(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        $this->validate($request, [

            'medico' => 'required|max:255',
            'examen_fisico' => 'required|max:255',
            'nota_de_evolucion' => 'required|max:255',
            'codigo' => 'required|max:255',

        ]);

        Evolucion_005::create([

            'id_usuariocrea'    =>  $idusuario1,
            'ip_creacion'       =>  $ip_clientes,
            'id_usuariomod'     =>  $idusuario1,
            'no_evolucion'      =>  $request['no_evolucion'],
            'medico'            =>  $request['medico'],
            'codigo'            =>  $request['codigo'],
            'nota_evolucion'    =>  $request['nota_de_evolucion'],
            'fecha_evolucion'   =>  $request['fecha_evolucion'],
            'examen_fisico'     =>  $request['examen_fisico'],
            'id_paciente'       =>  $request['id_paciente'],
        ]);

        return back()->with('success', 'Guardado con exito:');
    }

    public function diagnostico005(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //$data=request::all();
        //dd($request->all());

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        if (count($request->tipo) > 0) {
            foreach ($request->tipo as $item => $v) {

                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_diagnostico' => $request->fecha_diagnostico[$item],
                    'cie_diagnostico' => $request->cie[$item],
                    'operacion_diagnostico' => $request->operacion[$item],
                    'tipo_diagnostico' => $request->tipo[$item],
                    'medico_diagnostico' => $request->medico_urgente[$item]
                );

                Diagnostico_005::insert($data2);
            }
        }
        return redirect()->back()->with('successo', 'Guardado Doc');
    }

    public function medidas_generales(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_general) > 0) {
            foreach ($request->medico_general as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_generales' => $request->fecha_generales[$item],
                    'medico_general' => $request->medico_general[$item],
                    'descripcion_general' => $request->descripcion_general[$item],
                );
                Medidas_generales::insert($data2);
            }
        }
        return redirect()->back()->with('exito', 'Guardado Doc');
    }

    public function salas(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request->all());
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medicina_salas) > 0) {
            foreach ($request->medicina_salas as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_salas' => $request->fecha_salas[$item],
                    'area_salas' => $request->area_salas[$item],
                    'medicina_salas' => $request->medicina_salas[$item],
                    'descripcion_salas' => $request->descripcion_salas[$item],
                    'medico_salas' => $request->medico_salas[$item],
                );
                Hospital_Salas::insert($data2);
            }
        }
        return redirect()->back()->with('dato', 'Guardado Doc');
    }
    public function tratamiento(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_tratamiento) > 0) {
            foreach ($request->medico_tratamiento as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechatratamiento' => $request->fechatratamiento[$item],
                    'medico_tratamiento' => $request->medico_tratamiento[$item],
                    'descripcion_tratamiento' => $request->descripcion_tratamiento[$item],
                );
                Tratamiento_005::insert($data2);
            }
        }
        return redirect()->back()->with('listo', 'Guardado Doctor!!');
    }
    public function plan(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->descripcion_plan) > 0) {
            foreach ($request->descripcion_plan as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechaplan' => $request->fechaplan[$item],
                    'medico_plan' => $request->medico_plan[$item],
                    'descripcion_plan' => $request->descripcion_plan[$item],
                );
                Plan_005::insert($data2);
            }
        }
        return redirect()->back()->with('validado', 'Guardado Doctor!!');
    }

    public function editarevolucion(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = [
            'examen_fisico' => $request['examen_fisico'],
            'nota_evolucion' => $request['nota_evolucion'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_diagnostico(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_diagnostico = $request->get('medico_diagnostico');
        $operacion_diagnostico = $request->get('operacion_diagnostico');
        $cie_diagnostico = $request->get('cie_diagnostico');

        $prescri = Diagnostico_005::where('id_paciente', '=', $id_paciente)
            ->medico_diagnostico($medico_diagnostico)
            ->operacion_diagnostico($operacion_diagnostico)
            ->cie_diagnostico($cie_diagnostico)
            ->paginate(5);

        return view('hospital/emergencia/resultado_diagnostico', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function modaleditar_diagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);

        return view('hospital/emergencia/modaleditar_diagnostico', ['campo' => $campo, 'id' => $id]);
    }

    public function editardiagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);
        $editar = [
            'operacion_diagnostico' => $request['operacion'],
            'cie_diagnostico' => $request['cie'],
            'tipo_diagnostico' => $request['tipo'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_generales(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_general = $request->get('medico_general');
        $descripcion_general = $request->get('descripcion_general');
        $prescri = medidas_generales::where('id_paciente', '=', $id_paciente)
            ->medico_general($medico_general)
            ->descripcion_general($descripcion_general)
            ->paginate(5);

        return view('hospital/emergencia/resultado_generales', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function editar_generales(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = medidas_generales::findOrFail($id);

        return view('hospital/emergencia/editar_generales', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_gene(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Medidas_generales::findOrFail($id);
        $editar = [
            'medico_general' => $request['medico_general'],
            'descripcion_general' => $request['descripcion_general'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function mostrar_resultadotratamiento(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_tratamiento = $request->get('medico_tratamiento');
        $descripcion_tratamiento = $request->get('descripcion_tratamiento');

        $tratamiento = Tratamiento_005::where('id_paciente', '=', $id_paciente)
            ->medico_tratamiento($medico_tratamiento)
            ->descripcion_tratamiento($descripcion_tratamiento)
            ->paginate(5);
        return view('hospital/emergencia/mostrar_resultadotratamiento', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);

        return view('hospital/emergencia/modal_tratamiento', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);
        $editar = [
            'medico_tratamiento' => $request['medico_tratamiento'],
            'descripcion_tratamiento' => $request['descripcion_tratamiento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function resultado_plan(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_plan = $request->get('medico_plan');
        $descripcion_plan = $request->get('descripcion_plan');

        $tratamiento = Plan_005::where('id_paciente', '=', $id_paciente)
            ->medico_plan($medico_plan)
            ->descripcion_plan($descripcion_plan)
            ->paginate(5);
        return view('hospital/emergencia/resultado_plan', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_editarplan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);

        return view('hospital/emergencia/modal_editarplan', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_plan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);
        $editar = [
            'descripcion_plan' => $request['descripcion_plan'],
            'medico_plan' => $request['medico_plan'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function medicamentos_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medicamento = $request->get('medicamento');

        $medicamento = Formulario005::where('id_paciente', '=', $id_paciente)
            ->medicamento($medicamento)
            ->paginate(5);

        return view('hospital/emergencia/medicamentos_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function modal_medicamentos(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);

        return view('hospital/emergencia/modal_medicamentos', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_medi(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);
        $editar = [
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medicinas'],
            'cantidad_medicinas' => $request['cantidad_medicinas'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function salas_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $area_salas = $request->get('area_salas');
        $medicina_salas = $request->get('medicina_salas');

        $medicamento = Hospital_Salas::where('id_paciente', '=', $id_paciente)
            ->area_salas($area_salas)
            ->medicina_salas($medicina_salas)
            ->paginate(5);

        return view('hospital/emergencia/salas_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function editar_salas(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        //dd($dato);

        return view('hospital/emergencia/editar_salas', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_modal(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        $editar = [
            'area_salas' => $request['area_salas'],
            'medicina_salas' => $request['medicina_salas'],
            'descripcion_salas' => $request['descripcion_salas'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

}
