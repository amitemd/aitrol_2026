<?php

namespace Sis_medico\Http\Controllers\auditoria_hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Empresa;
use Sis_medico\Hc_cardio_cie10;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Interconsulta;
use Sis_medico\Interconsulta_Diagnostico;
use Sis_medico\User;

class AuditoriaDocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function formulario007Robot($hcid)
    {
        $historia = Historiaclinica::findOrFail($hcid);
        $paciente = $historia->paciente;
        $cie10 = DB::table('aud_hc_cie10')->where('hcid', $historia->hcid)->get();
        $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
        $doctor = null;

        if ($procedimiento) {
            $idDoctor = $procedimiento->id_doctor_responsable ?: $procedimiento->id_doctor_examinador2;
            if ($idDoctor) {
                $doctor = User::find($idDoctor);
            }
        }

        if (!$doctor) {
            $doctor = $historia->doctor_1;
        }

        $html = View::make(
            'pdf_hospital.formulario007_unificado_robot',
            compact('doctor', 'cie10', 'historia', 'paciente') + ['historia_clinica' => $historia]
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->loadHTML($html);

        return $pdf->stream('HCU_007.pdf', ['Attachment' => false]);
    }

    public function formulario007Unificado($hcid)
    {
        $historia = Historiaclinica::findOrFail($hcid);
        $paciente = $historia->paciente;
        $cardio = $historia->cardio;

        abort_if(!$cardio, 404, 'No existe información cardiológica para esta historia clínica.');

        $evolucion_orden = $cardio->evolucion_orden;
        $cie10_orden = Hc_cardio_cie10::where('id_cardio', $cardio->id)
            ->where('estado', '1')
            ->get();

        $interconsulta = DB::transaction(function () use ($historia, $cardio) {
            $interconsulta = Interconsulta::where('id_hc', $historia->hcid)
                ->where('estado', '1')
                ->first();

            $auditoria = Aud_Hc_Procedimientos::where('id_hc', $historia->hcid)->first();
            $evolucionOrden = $cardio->evolucion_orden;
            $especialidadOrigen = $evolucionOrden && $evolucionOrden->historiaclinica
                && $evolucionOrden->historiaclinica->agenda
                && $evolucionOrden->historiaclinica->agenda->especialidad
                ? $evolucionOrden->historiaclinica->agenda->especialidad->nombre
                : ' ';
            $especialidadResultado = $historia->agenda && $historia->agenda->especialidad
                ? $historia->agenda->especialidad->nombre
                : ' ';
            $ip = request()->ip();
            $usuario = Auth::id();
            $datos = [
                'id_paciente' => $historia->id_paciente,
                'id_doctor' => $auditoria ? $auditoria->id_doctor_examinador : $historia->id_doctor1,
                'id_empresa' => $historia->agenda ? $historia->agenda->id_empresa : null,
                'fecha' => $cardio->fecha_formato,
                'estado' => '1',
                'resultados_exa' => $cardio->resultados,
                'plan_terapeuticos' => $cardio->plan_tratamiento,
                'id_usuariomod' => $usuario,
                'ip_modificacion' => $ip,
                'id_hc' => $historia->hcid,
                'resumen' => $cardio->resumen,
                'cuadro_actual' => $cardio->cuadro_actual,
                'especialidad' => $especialidadOrigen,
                'especialidad_resultado' => $especialidadResultado,
            ];

            if ($interconsulta) {
                $interconsulta->update($datos);
            } else {
                $datos['evolucion'] = $cardio->cuadro_clinico;
                $datos['id_usuariocrea'] = $usuario;
                $datos['ip_creacion'] = $ip;
                $interconsulta = Interconsulta::create($datos);
            }

            Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->delete();
            foreach (Aud_Hc_Cie10::where('hcid', $historia->hcid)->get() as $diagnostico) {
                Interconsulta_Diagnostico::create([
                    'id_interconsulta' => $interconsulta->id,
                    'cie10' => $diagnostico->cie10,
                    'presuntivo_definitivo' => $diagnostico->presuntivo_definitivo,
                    'id_usuariocrea' => $usuario,
                    'id_usuariomod' => $usuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);
            }

            return $interconsulta->fresh();
        });

        $age = Carbon::parse($paciente->fecha_nacimiento)->age;
        $cie10 = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        $html = View::make(
            'pdf_hospital.formulario007_unificado',
            compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10', 'evolucion_orden', 'cardio', 'cie10_orden')
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->loadHTML($html);

        return $pdf->stream('HCU_007_' . $paciente->apellido1 . '.pdf', ['Attachment' => false]);
    }

    public function formulario007Espirometria(Request $request)
    {
        $this->validate($request, [
            'id_paciente' => 'required|string',
            'fecha_atencion' => 'required|date',
            'hcid' => 'nullable|integer',
            'hc_proc' => 'nullable|integer',
        ]);

        $historia = Historiaclinica::join('paciente', 'paciente.id', '=', 'historiaclinica.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'historiaclinica.id_seguro')
            ->join('agenda', 'agenda.id', '=', 'historiaclinica.id_agenda')
            ->where('historiaclinica.id_paciente', $request->id_paciente)
            ->where('agenda.fechaini', '<', $request->fecha_atencion)
            ->where('agenda.espid', '5')
            ->where('historiaclinica.id_seguro', '2')
            ->select('historiaclinica.*')
            ->first();

        abort_if(!$historia, 404, 'No se encontró una consulta neumológica anterior.');

        $paciente = $historia->paciente;
        $agenda = $historia->agenda;
        $evolucion_orden = Hc_Evolucion::where('hcid', $historia->hcid)->where('secuencia', '0')->first();
        $age = Carbon::parse($paciente->fecha_nacimiento)->age;
        $cie10_orden = Hc_Cie10::where('hcid', $historia->hcid)->get();
        $fecha = $request->fecha_atencion;
        $hc_proc = $request->hc_proc;
        $aud_hc = $request->hcid;
        $historia_clinica = $historia;
        $html = View::make(
            'pdf_hospital.formulario007_espirometria',
            compact('fecha', 'historia_clinica', 'age', 'paciente', 'agenda', 'evolucion_orden', 'cie10_orden', 'hc_proc', 'aud_hc')
        )->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->loadHTML($html);

        return $pdf->stream('HCU_007_' . $paciente->apellido1 . '.pdf', ['Attachment' => false]);
    }
}
