<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Style_Alignment;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Firma_Usuario;
use Sis_medico\Formulario005;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Orden;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Pais;
use Sis_medico\Formulario_053;
use Sis_medico\Formulario_053_Cie10;

class Orden_formato012Controller extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20)) == false) {
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Muestra el historial de las ordenes de Imagenes
    //Solo para Doctores

    public function formato012($id, $id_orden)
    {

        //dd($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $evolucion = Hc_Evolucion::where('id', $id)->where('secuencia', '0')->first();

        //Obtenemos Historia Clinica a partir del hcid de la Tbl hc_evolucion
        $hcid            = $evolucion->hcid;
        $historiaclinica = Historiaclinica::find($hcid);

        //Obtenemos datos del Paciente
        $paciente = Paciente::find($historiaclinica->id_paciente);

        //Obtenemos datos de la Agenda
        $agenda = Agenda::find($historiaclinica->id_agenda);

        //Obtenemos una parte de la Fecha de la Agenda
        $fecha_agenda = substr($agenda->fechaini, 0, 10);

        //Obtenemos el Listado de Usuario de Tipo Doctores
        $doctores = User::where('estado', '1')
            ->where('id_tipo_usuario', '3')
            ->OrderBy('apellido1', 'asc')
            ->where('training', '0')
            ->where('uso_sistema', '0')
            ->get();

        //Obtenemos el Listado de Seguro
        $seguros = Seguro::where('inactivo', '1')
            ->OrderBy('nombre', 'asc')
            ->get();

        //Obtenemos  el Listado de Empresas que tengan convenio con tipo de Seguro IESS
        $empresas = Empresa::where('empresa.estado', '1')
            ->join('convenio as c', 'c.id_empresa', 'empresa.id')
            ->where('empresa.id', '<>', '9999999999')
            ->OrderBy('empresa.nombrecomercial', 'asc')
            ->where('c.id_seguro', '2')
            ->select('empresa.*')->get();

        //$orden_012 = Orden_012::where('id_hc_evolucion',$id)->first();

        //Si Existe el id evolucion en la tabla hc_evolucion
        if (!is_null($evolucion)) {

            //Luego Vericamos si existe el id evolucion en la tabla orden_012
            $orden_012 = Orden_012::where('id_hc_evolucion', $id)
                ->where('estado', '1')
                ->where('id_orden',$id_orden)
                ->first();
                

            if (!is_null($orden_012)) {

                $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)
                    ->groupBy('cie10')
                    ->get();

                if (!is_null($cie10)) {

                    foreach ($cie10 as $value) {

                        $arr_cie10 = [
                            'id_orden_012'          => $orden_012->id,
                            'cie10'                 => $value->cie10,
                            'presuntivo_definitivo' => $value->presuntivo_definitivo,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                            'ip_creacion'           => $ip_cliente,
                            'ip_modificacion'       => $ip_cliente,

                        ];

                        Orden_012_Cie10::create($arr_cie10);

                    }

                }

                //Consultamos si existe el ID de la Tbl orden_012
                $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)
                    ->get();

                //Invocamos a la vista de Formulario 012
                return view('hc4.ordenes.orden_procedimiento_endoscopico.formato012', ['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10, 'agenda' => $agenda]);

            } else {

                //Verificamos en la Tabla  Orden de procedimientos Endoscopicos,Funcionales,Imagenes
                /*$ordenes = Orden::where('id_paciente',$paciente->id)
                ->whereBetween('fecha_orden',[$fecha_agenda.' 0:00:00', $fecha_agenda.' 23:59:59'])->where('estado','1')
                ->get();*/

                $arr = [
                    'id_paciente'        => $paciente->id,
                    'id_doctor_solicita' => $idusuario,
                    'id_doctor_firma'    => $idusuario,
                    'fecha_orden'        => $agenda->fechaini,
                    'id_hc_evolucion'    => $id,
                    'id_orden'           => $id_orden,
                    'estado'             => '1',
                    'rutina'             => '1',
                    'puede_mover'        => '1',
                    //'descripcion' => $ordenes->descripcion,  Esta mal la Definicion
                    'motivo'             => $evolucion->motivo, //Evolucion Motivo
                    'id_empresa'         => $agenda->id_empresa,
                    'cuadro_clinico'     => $evolucion->cuadro_clinico, //Evolucion Cuadro Clinico
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,

                ];

                //Insertamos en la Tabla Orden_012 y Obtenemos el id de la Tabla
                $id_012 = Orden_012::insertGetId($arr);

                //Una vez Insertado Consultamos la Tabla orden_012
                $orden_012 = orden_012::find($id_012);

                /*$cie10 = Hc_Cie10::where('id',$historiaclinica->id)
                ->groupBy('cie10')
                ->get();*/

                $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)
                    ->groupBy('cie10')
                    ->get();

                if (!is_null($cie10)) {

                    foreach ($cie10 as $value) {

                        $arr_cie10 = [
                            'id_orden_012'          => $id_012,
                            'cie10'                 => $value->cie10,
                            'presuntivo_definitivo' => $value->presuntivo_definitivo,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                            'ip_creacion'           => $ip_cliente,
                            'ip_modificacion'       => $ip_cliente,

                        ];

                        Orden_012_Cie10::create($arr_cie10);

                    }

                }

                //Consultamos si existe el ID de la Tbl orden_012
                $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)
                    ->get();

                return view('hc4.ordenes.orden_procedimiento_endoscopico.formato012', ['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10, 'agenda' => $agenda]);

            }

        }

    }

    public function actualizar_formato012(Request $request)
    {

        //return $request->id_empresa;

        //return $request->id_orden;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden_012 = Orden_012::find($request->id_orden);

        //return $orden_012;

        $evolucion       = Hc_Evolucion::find($orden_012->id_hc_evolucion);
        $historiaclinica = $evolucion->historiaclinica;
        $agenda          = $historiaclinica->agenda;

        if (!is_null($orden_012)) {
            $arr = [
                'id_doctor_firma' => $request->id_doctor_examinador,
                'fecha_orden'     => $request->fecha_orden,
                'referido'        => $request->referido,
                'descripcion'     => $request->descripcion,
                'motivo'          => $request->motivo,
                'cuadro_clinico'  => $request->historia_clinica,
                'urgente'         => $request->urgente,
                'rutina'          => $request->rutina,
                'control'         => $request->control,
                'rx_convencional' => $request->rx_convencional,
                'tomografia'      => $request->tomografia,
                'resonancia'      => $request->resonancia,
                'ecografia'       => $request->ecografia,
                'procedimiento'   => $request->procedimiento,
                'otros'           => $request->otros,
                'texto_otros'     => $request->texto_otros,
                'puede_mover'     => $request->puede_mover,
                'puede_retirar'   => $request->puede_retirar,
                'medico_presente' => $request->medico_presente,
                'toma_radio'      => $request->toma_radio,
                'servicio'        => $request->servicio,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_empresa'      => $request->id_emp,
            ];

            //return $arr;

            $orden_012->update($arr);
            // $agenda->update(['id_empresa' => $request->id_empresa]); cambio

        }
        return 'ok';
    }

    public function imprimir_012_excel($id)
    {

        // return "ok"; //FUNCION ORIGINAL EN ORDEBNPROCCONTROLLER
        $orden_012   = Orden_012::find($id);
        $paciente    = Paciente::find($orden_012->id_paciente);
        $id_empresa  = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
        $inf_empresa = Empresa::where('id', $orden_012->id_empresa)
            ->OrderBy('estado', '1')
            ->first();

        $id_seguro = $orden_012->evolucion->procedimiento->id_seguro;
        if ($id_seguro != null) {
            $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
        }
        $empresa = Empresa::find($id_empresa);
        $seguro  = Seguro::find($id_seguro);
        $doctor  = User::find($orden_012->id_doctor_firma);
        $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();

        $age   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();

        $fecha_d = date('Y/m/d');
        Excel::create($paciente->apellido1 . '_' . $paciente->nombre1 . '_ORDEN_012', function ($excel) use ($orden_012, $inf_empresa, $paciente, $seguro, $empresa, $age, $doctor, $cie10) {

            $excel->sheet('Orden_012', function ($sheet) use ($orden_012, $paciente, $seguro, $empresa, $age, $doctor, $cie10, $inf_empresa) {
                $fecha_d = date('Y/m/d');

                $sheet->mergeCells('B2:H2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSTITUCION DEL SISTEMA  ADASDA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I2:P2');
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('UNIDAD OPERATIVA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Q2:T2');
                $sheet->cell('Q2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COD. UO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U2:AB2');
                $sheet->cell('U2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COD. LOCALIZACION');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC2:AG3');
                $sheet->cell('AC2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO DE HISTORIA CLINICA575676');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B3:H4');
                $sheet->cell('B3', function ($cell) use ($seguro) {
                    // manipulate the cel
                    $cell->setValue('IESS');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I3:P4');
                $sheet->cell('I3', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Q3:T4');
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U3:V3');
                $sheet->cell('U3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARROQUIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U4:V4');
                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARQUI');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W3:Z3');
                $sheet->cell('W3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTON');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W4:Z4');
                $sheet->cell('W4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAQUIL');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA3:AB3');
                $sheet->cell('AA3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROVINCIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA4:AB4');
                $sheet->cell('AA4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC4:AG4');
                $sheet->cell('AC4', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue(substr($paciente->id, 5, 5));
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO PATERNO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B6:E7');
                $sheet->cell('B6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->apellido1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F5:K5');
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO MATERNO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F6:K7');
                $sheet->cell('F6', function ($cell) use ($paciente) {
                    // manipulate the cel if si el campo es null
                    if ($paciente->apellido2 != '(N/A)') {
                        $cell->setValue($paciente->apellido2);
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('L5:Q5');
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIMER NOMBRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L6:Q7');
                $sheet->cell('L6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->nombre1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('R5:Z5');
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGUNDO NOMBRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R6:Z7');
                $sheet->cell('R6', function ($cell) use ($paciente) {
                    // manipulate the cel if si el campo es null
                    if ($paciente->nombre2 != '(N/A)') {
                        $cell->setValue($paciente->nombre2);
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA5:AB5');
                $sheet->cell('AA5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA6:AB7');
                $sheet->cell('AA6', function ($cell) use ($age) {
                    // manipulate the cel
                    $cell->setValue($age);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC5:AG5');
                $sheet->cell('AC5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA DE CIUDADANIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC6:AG7');
                $sheet->cell('AC6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->id);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B8:E8');
                $sheet->cell('B8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PERSONA QUE REFIERE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B9:E9');
                $sheet->cell('B9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->referido);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F8:L8');
                $sheet->cell('F8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROFESIONAL SOLICITANTE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F9:L9');
                $sheet->cell('F9', function ($cell) use ($doctor) {
                    // manipulate the cel
                    $cell->setValue('DR. ' . $doctor->apellido1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('M8:Q8');
                $sheet->cell('M8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERVICIO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('M9:Q9');
                $sheet->cell('M9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GASTROENTEROLOGIA');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R8:S8');
                $sheet->cell('R8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R9:S9');
                $sheet->cell('R9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T8:U8');
                $sheet->cell('T8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CAMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T9:U9');
                $sheet->cell('T9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V8:AD8');
                $sheet->cell('V8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIORIDAD');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V9:W9');
                $sheet->cell('V9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('URGENTE');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('X');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Y9:Z9');
                $sheet->cell('Y9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUTINA');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AB9:AC9');
                $sheet->cell('AB9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONTROL');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE8:AG8');
                $sheet->cell('AE8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE TOMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE9:AG9');
                $sheet->cell('AE9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B11:AG11');
                $sheet->cell('B11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('1. ESTUDIO SOLICITADO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B12:D12');
                $sheet->cell('B12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RX CONVENCIONAL');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('  ');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F12:I12');
                $sheet->cell('F12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOMOGRAFIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K12:M12');
                $sheet->cell('K12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESONANCIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('  ');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O12:Q12');
                $sheet->cell('O12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ECOGRAFIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S12:U12');
                $sheet->cell('S12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W12:X12');
                $sheet->cell('W12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Z12:AG12');
                $sheet->cell('Z12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B13:D14');
                $sheet->cell('B13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E14', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F13:AG13');
                $sheet->cell('F13', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->descripcion);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F14:AG14');
                $sheet->cell('F14', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B15:AG15');
                $sheet->mergeCells('B16:I16');
                $sheet->cell('B16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PUEDE MOVILIZARSE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K16:Q16');
                $sheet->cell('K16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PUEDE RETIRARSE VENDAS APOSITOS O YESOS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S16:X16');
                $sheet->cell('S16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EL MEDICO ESTARA PRESENTE EN ELE EXAMEN');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Z16:AF16');
                $sheet->cell('Z16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOMA DE RADIOGRAFIA EN LA CAMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B18:AG18');
                $sheet->cell('B18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('2. MOTIVO DE LA SOLICITUD.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B19:AG20');
                $sheet->cell('B19', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->motivo);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B21:R21');
                $sheet->cell('B21', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('3. RESUMEN CLINICO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B22:Q27');
                $sheet->cell('B22', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $var = strip_tags($orden_012->cuadro_clinico);
                    $cell->setValue($var);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('S21:AG21');
                $sheet->cell('S21', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4. DIAGNOSTICOS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S22:AD22');
                $sheet->cell('S22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE= CLASIFICACION INTERNACIONAL DE ENFERMEDADES  PRE= RESUNTIVO  DEF= DEFINITIVO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AE22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AF22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEF');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                foreach ($cie10 as $val) {

                    $c10 = Cie_10_3::find($val->cie10);
                    if (is_null($c10)) {
                        $c10 = Cie_10_4::find($val->cie10);
                    }

                    $sheet->cell('S23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('1');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T23:AD23');
                    $sheet->cell('T23', function ($cell) use ($c10) {
                        // manipulate the cel
                        $cell->setValue($c10->descripcion);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE23', function ($cell) use ($val) {
                        // manipulate the cel
                        $cell->setValue($val->cie10);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF23', function ($cell) use ($val) {
                        // manipulate the cel
                        if ($val->presuntivo_definitivo == 'PRESUNTIVO') {
                            $cell->setValue('X');
                        }
                        $cell->setAlignment('center');
                        $cell->setBackground('#ECEFF0');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG23', function ($cell) use ($val) {
                        // manipulate the cel @if($val->presuntivo_definitivo=='DEFINITIVO') X @endif
                        if ($val->presuntivo_definitivo == 'DEFINITIVO') {
                            $cell->setValue('X');
                        }
                        $cell->setAlignment('center');
                        $cell->setBackground('#ECEFF0');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('2');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T24:AD24');
                    $sheet->cell('T24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('3');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T25:AD25');
                    $sheet->cell('T25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('4');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T26:AD26');
                    $sheet->cell('T26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('5');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T27:AD27');
                    $sheet->cell('T27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $cuenta_c10 = $cie10->count();
                for ($cuenta_c10 = 0; $cuenta_c10 <= 5 - $cuenta_c10; $cuenta_c10 = $cuenta_c10 + 1) {
                    $sheet->cell('S23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('1');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T23:AD23');
                    $sheet->cell('T23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBackground('#ECEFF0');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG23', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBackground('#ECEFF0');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('2');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T24:AD24');
                    $sheet->cell('T24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG24', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('3');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T25:AD25');
                    $sheet->cell('T25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG25', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('4');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T26:AD26');
                    $sheet->cell('T26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG26', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('5');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('T27:AD27');
                    $sheet->cell('T27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AE27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AF27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AG27', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBackground('#ECEFF0');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $sheet->cell('B29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C29:E29');
                $sheet->cell('C29', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue(substr($orden_012->fecha_orden, 0, 10));
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H29:I29');
                $sheet->cell('H29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PROFESIONAL');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J29:S29');
                $sheet->cell('J29', function ($cell) use ($doctor) {
                    // manipulate the cel
                    $cell->setValue('Dr(a). ' . $doctor->apellido1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //CODIGO
                $sheet->mergeCells('T28:V28');
                $sheet->cell('T28', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('T29:V29');
                $sheet->cell('T29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('16203');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W29:X29');
                $sheet->cell('W29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Y29:AD29');
                $sheet->cell('Y29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE29:AF29');
                $sheet->cell('AE29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO HOJA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B30:G30');
                $sheet->cell('B30', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SNS-MSP / HCU-form.012a/2008');
                });
                $sheet->mergeCells('Z30:AG30');
                $sheet->cell('Z30', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMAGENOLOGIA - SOLICITUD');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                });

            });
            $excel->getActiveSheet()->getStyle('B2:AG30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle("B2:AG9")->getFont()->setSize(10);
            $excel->getActiveSheet()->getStyle("B11:AG29")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("B3:P4")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("L4:AB4")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AC2:AG3")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AC4:AG4")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B6:AG7")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("F9:Q9")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("U9:V9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("Y9:Z9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AB9:AC9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("B11:AG11")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("F13:AG13")->getFont()->setSize(16);
            $excel->getActiveSheet()->getStyle("B18:AG18")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B19:AG20")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("B21:AG21")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B22:Q27")->getFont()->setSize(11);
            $excel->getActiveSheet()->getStyle("S23:AG27")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("J29:V29")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("B30:G30")->getFont()->setSize(10);
            $excel->getActiveSheet()->getStyle("AA30:AG30")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B22:Q27")->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(5)->setAutosize(false);

        })->export('xlsx');

    }

    //ESTA FUNCTION OBTIENE LA INFOAMCION POR PACIENTE
    //MUESTRA EN UNA TABLA POR EL LADO DE SISTEMA MEDICO PRB
    public function orden_ingresada_formato012()
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();

        $fecha       = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');

        $ordenes_012 = DB::table('orden_012 as o12')
            ->join('paciente as p', 'p.id', '=', 'o12.id_paciente')
            ->join('empresa as e', 'e.id', '=', 'o12.id_empresa')
            ->whereBetween('o12.created_at', [$fecha . ' 00:00', $fecha . ' 23:59'])
            ->leftjoin('users as d1', 'd1.id', '=', 'o12.id_doctor_firma')
            ->select('o12.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2',
                'p.apellido1 as papellido1', 'p.apellido2 as papellido2',
                'e.nombrecomercial as enombre', 'd1.nombre1 as dnombre1',
                'd1.apellido1 as dapellido1', 'd1.apellido2 as dapellido2',
                'd1.color as d1color')
            ->orderby('o12.created_at', 'desc')
            ->get();

        // dd($ordenes_012);
        // dd($fecha);

        return view('hc4.ordenes.orden_ingresada_formato012.index', ['ordenes_012' => $ordenes_012, 'doctores' => $doctores, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'id_doctor1' => null, 'nombres' => '', 'cedula' => '']);
    }

    //ESTA FUNCTION BUSCA LAS ORDENES
    //EN SISTEMA MEDICO PRB
    public function search(Request $request)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        //$id_doctor1     = $request['id_doctor1'];
        $id_doctor1 = $request['id_doctor_firma'];

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->get();

        $ordenes_012 = DB::table('orden_012 as o12')
            ->join('paciente as p', 'p.id', '=', 'o12.id_paciente')
            ->join('empresa as e', 'e.id', '=', 'o12.id_empresa')
            ->leftjoin('users as d1', 'd1.id', '=', 'o12.id_doctor_firma')
            ->select('o12.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2',
                'p.apellido1 as papellido1', 'p.apellido2 as papellido2',
                'e.nombrecomercial as enombre', 'd1.nombre1 as dnombre1',
                'd1.apellido1 as dapellido1', 'd1.apellido2 as dapellido2',
                'd1.color as d1color');
        // dd($fecha,$ordenes_012->get());

        if ($fecha != null && $fecha_hasta != null) {
            $ordenes_012 = $ordenes_012->whereBetween('o12.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $ordenes_012 = $ordenes_012->where('o12.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $ordenes_012 = $ordenes_012->where('o12.id_doctor_firma', $id_doctor1);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes_012 = $ordenes_012->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });

            } else {

                $ordenes_012 = $ordenes_012->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }

        }

        // $ordenes_012 = Orden_012::orderBy('created_at','desc')->get();
        // $ordenes_012 = $ordenes_012->get();
        $ordenes_012 = $ordenes_012->orderBy('o12.created_at', 'desc')->get();

        // dd($ordenes_012);
        // dd($fecha);

        return view('hc4.ordenes.orden_ingresada_formato012.index', ['ordenes_012' => $ordenes_012, 'cedula' => $cedula, 'nombres' => $nombres, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1]);

    }

    //ESTA FUNCTION OBTIENE LA INFORMACION POR PACIENTE
    //MUESTRA LA INFORMACION EN UN FORMULARIO POR EL LADO DE SISTEMA MEDICO PRB
    public function Editar_formato012(Request $request, $id)
    {

        //Obtenemos la Lista de Doctores
        $doctores = User::where('estado', '1')
            ->where('id_tipo_usuario', '3')
            ->OrderBy('apellido1', 'asc')
            ->where('training', '0')
            ->where('uso_sistema', '0')
            ->get();

        //Obtenemos la Lista de Empresa
        $empresas = Empresa::where('empresa.estado', '1')
            ->join('convenio as c', 'c.id_empresa', 'empresa.id')
            ->where('empresa.id', '<>', '9999999999')
            ->OrderBy('empresa.nombrecomercial', 'asc')
            ->where('c.id_seguro', '2')
            ->select('empresa.*')
            ->get();

        $ordenes_012 = Orden_012::find($id);

        $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012', $ordenes_012->id)->get();
        //dd($id);

        return view('hc4.ordenes.orden_ingresada_formato012.editar_formato012', ['ordenes_012' => $ordenes_012, 'doctores' => $doctores, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10]);
    }

    //ACTUALIZA LA ORDEN 012 POR PACIENTE
    //EN El SISTEMA MEDICO PRB
    public function Actualizar_formato_012(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $ordenes_012     = Orden_012::find($request->id_orden);
        $evolucion       = Hc_Evolucion::find($ordenes_012->id_hc_evolucion);
        $historiaclinica = $evolucion->historiaclinica;
        $agenda          = $historiaclinica->agenda;

        if (!is_null($ordenes_012)) {
            $arr = [
                'id_doctor_firma' => $request->id_doctor_examinador,
                'fecha_orden'     => $request->fecha_orden,
                'referido'        => $request->referido,
                'descripcion'     => $request->descripcion,
                'motivo'          => $request->motivo,
                'cuadro_clinico'  => $request->historia_clinica,
                'urgente'         => $request->urgente,
                'rutina'          => $request->rutina,
                'control'         => $request->control,
                'rx_convencional' => $request->rx_convencional,
                'tomografia'      => $request->tomografia,
                'resonancia'      => $request->resonancia,
                'ecografia'       => $request->ecografia,
                'procedimiento'   => $request->procedimiento,
                'otros'           => $request->otros,
                'texto_otros'     => $request->texto_otros,
                'puede_mover'     => $request->puede_mover,
                'puede_retirar'   => $request->puede_retirar,
                'medico_presente' => $request->medico_presente,
                'toma_radio'      => $request->toma_radio,
                'servicio'        => $request->servicio,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_empresa'      => $request->id_empresa, //cambio
            ];

            // return $arr;

            $ordenes_012->update($arr);
            // $agenda->update(['id_empresa' => $request->id_empresa]); cambio

        }

        return 'ok';

    }

    public function formato_053($id_orden)
    {
        //dd($id_orden);
        $orden = Orden::find($id_orden);
        $fecha = date('Y-m-d H:i:s');
        $id_orden_12 = Orden_012::where('id_orden',$id_orden)->first();
        $formulario_053 = Formulario_053::where('id_orden', $id_orden)->orderBy('created_at', 'desc')->first();

        $agenda =  DB::table('agenda as a')
        ->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
        ->where('a.id_paciente', $orden->id_paciente)
        ->where('a.estado_cita', 4)
        ->where('a.estado', 1)
        ->first();
        
        if(is_null($agenda)){
            $agenda = Agenda::// DB::table('agenda as a')
            where('id_paciente', $orden->id_paciente)
            ->where('estado_cita', 4)
            ->where('estado', 1)
            ->get()
            ->last();
        }
        //dd($agenda);

        $form_053_cie10 = Formulario_053_Cie10::all();
        return view ('hc4.ordenes.formulario053.formato_053', ['orden'=>$orden, 'form_053_cie10'=> $form_053_cie10, 'fecha'=>$fecha, 'agenda'=>$agenda, 'formulario_053' => $formulario_053, 'id_orden_12' => $id_orden_12]);
    }
    public function store_053(Request $request){
         //dd($request->all());
        $agenda = Agenda::find($request->id_agenda);
        $orden = Orden::find($request->id_orden);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                           
        $arr_form_053 = [
          'id_agenda'                       => $agenda->id,
          'id_empresa'                      => $agenda->id_empresa,
          'id_orden'                        => $orden->id,
          'distrito'                        => $request['distrito'],
          'refiere'                         => $request['refiere'],
          'establecimiento_salud'           => $request['establecimiento'],
          'servicio'                        => $request['servicio'],
          'especialidad'                    => $request['especialidad'],
          'fecha'                           => $request['fecha_orden'],

          'limitada_capacidad'              => isset($request['limitada_capacidad']) ? 1 :0,
          'ausencia_temporal'               => isset($request['ausencia_temporal']) ? 1 :0,
          'falta_profesional'               => isset($request['falta_profesional']) ? 1 :0,
          'saturacion_capacidad'            => isset($request['saturacion_capacidad']) ? 1 :0,
          'otros'                           => isset($request['otros']) ? 1 :0,
          'texto_otros'                     => $request['texto_otros'],
          'cuadro_clinico'                  => $request['cuadro_clinico'],
     
          'id_paciente'                     => $orden->id_paciente,
          'id_doctor'                       => $orden->id_doctor,
   
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

                 
        ];
        //dd($arr_form_053);
   
        //Formulario_053::create($arr_form_053);
        $id_form_053 = Formulario_053::insertGetId($arr_form_053);
     
        $arr_form_053_cie10 = [
            'id_form_053'              => $id_form_053,
            'cie10'                    => $request['tdiagnostico'],
            'presuntivo_definitivo'    => $request['pre_def'],

            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
        ];
        Formulario_053_Cie10::create($arr_form_053_cie10);
        // echo "<a ></a>"

        return redirect(route('orden_012.pdf_053',['id_orden'=>$orden]));

    }
    public function pdf_053 (Request $request, $id_orden){
          
          $orden = Orden::find($id_orden);
          //dd($orden);
          $form_053 = Formulario_053::where('id_orden',$id_orden)->orderBy('created_at', 'desc')->first();
          $id_orden_12 = Orden_012::where('id_orden',$id_orden)->first();
          $diagnostico = Orden_012_Cie10::where('id_orden_012', $id_orden_12->id)->get();
          //dd($diagnostico);
          //dd($form_053->id_doctor);
         // dd($form_053);
          $paciente = Paciente::find($orden->id_paciente);
          //dd($paciente);
          $edad = Carbon::parse($paciente->fecha_nacimiento)->age; 
          $pais = Pais::where('id', $paciente->id_pais)->first();
          //dd($pais);
          $vistaurl="hc4.ordenes.formulario053.pdf_053";
          $view =  \View::make($vistaurl, compact('orden_proc_endoscopico','id_orden_12','diagnostico','form_053','paciente','edad','doctor_solicitante','firma', 'pais', 'orden'))->render();
          $pdf = \App::make('dompdf.wrapper');
          $pdf->loadHTML($view);
          $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
          $pdf->setPaper('A4', 'portrait');
          return $pdf->stream('formulario_053.pdf');
        
    }
}
