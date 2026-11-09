<?php

namespace Sis_medico\Http\Controllers\odontologia;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Protocolo;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\hc4\HistoriaPacienteController;
use Sis_medico\Log_usuario;
use Sis_medico\OdontFormulario033;
use Sis_medico\OdontPacienteProcedimiento;
use Sis_medico\OdontProcedimiento;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Orden;
use Sis_medico\Orden_012;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Procedimiento;
use Sis_medico\Protocolo;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\User;

class Formulario033Controller extends Controller
{
    private $odontologiaEspecialidadIdsCache;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Odontologia y Periodontologia comparten el flujo y las vistas, pero no
     * sus registros clinicos. La subclase periodontal cambia solamente este
     * mapa y el modelo principal; las demas especialidades no pasan por este
     * controlador.
     */
    protected function tablaFormulario033($detalle = 'formulario')
    {
        $tablas = [
            'formulario' => 'odont_formulario033',
            'antecedentes' => 'odont_antecedentes',
            'estomatognatico' => 'odont_examen_estomatognatico',
            'odontogramas' => 'odontogramas',
            'odontograma_piezas' => 'odontograma_piezas',
            'odontograma_superficies' => 'odontograma_superficies',
            'indicadores' => 'odont_indicadores',
            'ihos_detalles' => 'odont_ihos_detalles',
            'indices' => 'odont_indices',
            'diagnosticos' => 'odont_diagnosticos',
            'evoluciones' => 'odont_evoluciones',
        ];

        return isset($tablas[$detalle]) ? $tablas[$detalle] : $tablas['formulario'];
    }

    protected function modeloFormulario033()
    {
        return OdontFormulario033::class;
    }

    protected function consultaFormulario033()
    {
        $modelo = $this->modeloFormulario033();

        return $modelo::query();
    }

    protected function nombreModuloFormulario033()
    {
        return 'Odontologia';
    }

    protected function tablaProcedimientoClinico($detalle = 'cabecera')
    {
        return $detalle === 'detalle' ? 'odont_paciente_procedimientos' : 'odont_procedimientos';
    }

    protected function modeloProcedimientoClinico()
    {
        return OdontProcedimiento::class;
    }

    protected function modeloDetalleProcedimientoClinico()
    {
        return OdontPacienteProcedimiento::class;
    }

    protected function consultaProcedimientoClinico()
    {
        $modelo = $this->modeloProcedimientoClinico();

        return $modelo::query();
    }

    protected function consultaDetalleProcedimientoClinico()
    {
        $modelo = $this->modeloDetalleProcedimientoClinico();

        return $modelo::query();
    }

    protected function prefijoModuloClinico()
    {
        return 'odontologia';
    }

    private function rolNew($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcionUsuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcionUsuario)) {
            return true;
        }

        return false;
    }

    /**
     * La especialidad de Odontologia no tiene el mismo ID en todas las bases.
     * Se resuelve primero por nombre y el ID historico 3 queda solo como
     * respaldo para instalaciones antiguas que no tengan el nombre catalogado.
     */
    protected function odontologiaEspecialidadIds()
    {
        if (! is_null($this->odontologiaEspecialidadIdsCache)) {
            return $this->odontologiaEspecialidadIdsCache;
        }

        $ids = Especialidad::query()
            ->where('estado', 1)
            ->whereRaw('LOWER(nombre) LIKE ?', ['%odontolog%'])
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        if (count($ids) === 0) {
            $idHistorico = Especialidad::query()
                ->where('id', 3)
                ->where('estado', 1)
                ->value('id');
            $ids = is_null($idHistorico) ? [] : [(int) $idHistorico];
        }

        $this->odontologiaEspecialidadIdsCache = $ids;

        return $this->odontologiaEspecialidadIdsCache;
    }

    public function buscadorPaciente($id_paciente, $id_agenda = null)
    {
        if ($this->rolNew('2')) {
            return redirect('/');
        }

        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return redirect('/');
        }

        $edad = 0;
        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $paciente_observaciones = DB::table('paciente_observaciones')->where('id_paciente', $id_paciente)->first();
        $cortesia_paciente = Cortesia_paciente::where('id', $id_paciente)->get()->last();
        $alergiasxpac = Paciente_Alergia::with('principio_activo')->where('id_paciente', $id_paciente)->get();
        $historia_actual = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->select('historiaclinica.*', 'a.fechaini as agenda_fechaini', 'a.fechafin as agenda_fechafin', 'a.estado_cita as agenda_estado_cita', 'a.id_seguro as agenda_seguro', 'a.id_empresa as agenda_empresa')
            ->orderBy('a.fechaini', 'desc')
            ->orderBy('historiaclinica.hcid', 'desc')
            ->first();

        if (! is_null($id_agenda)) {
            $agendaSeleccionada = Agenda::where('id', $id_agenda)
                ->where('id_paciente', $id_paciente)
                ->whereIn('espid', $this->odontologiaEspecialidadIds())
                ->first();
            $historiaSeleccionada = ! is_null($agendaSeleccionada)
                ? Historiaclinica::where('id_paciente', $id_paciente)
                    ->where('id_agenda', $id_agenda)
                    ->first()
                : null;
            if (! is_null($historiaSeleccionada)) {
                $historia_actual = $historiaSeleccionada;
            }
        }

        $agenda_actual = ! is_null($historia_actual)
            ? Agenda::find($historia_actual->id_agenda)
            : Agenda::where('id_paciente', $id_paciente)
                ->whereIn('espid', $this->odontologiaEspecialidadIds())
                ->where('estado', '<>', '0')
                ->orderBy('fechaini', 'desc')
                ->first();
        $doctor_actual = ! is_null($historia_actual) ? User::find($historia_actual->id_doctor1) : (! is_null($agenda_actual) ? User::find($agenda_actual->id_doctor1) : null);
        $seguro_actual = ! is_null($agenda_actual) && ! is_null($agenda_actual->id_seguro) ? Seguro::find($agenda_actual->id_seguro) : (! is_null($paciente->id_seguro) ? Seguro::find($paciente->id_seguro) : null);
        $empresa_actual = ! is_null($agenda_actual) && ! is_null($agenda_actual->id_empresa) ? Empresa::find($agenda_actual->id_empresa) : null;
        $evoluciones = DB::table('hc_evolucion as e')
            ->join('historiaclinica as h', 'e.hcid', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->orderBy('a.fechaini', 'desc')
            ->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')
            ->orderBy('e.id', 'desc')
            ->get()
            ->first();

        $procedimientos_observaciones = null;
        $especialidad = null;
        if (! is_null($evoluciones)) {
            $procedimientos_observaciones = hc_procedimientos::find($evoluciones->hc_id_procedimiento);
            $especialidad = Especialidad::query()->find($evoluciones->espid);
        }

        $hc_rec = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->where('h.id_paciente', $id_paciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->whereNotNull('r.prescripcion')
            ->orderBy('a.fechaini', 'desc')
            ->orderBy('r.id', 'desc')
            ->select('r.*', 'a.fechaini')
            ->first();

        $estudios = DB::table('hc_imagenes_protocolo as ip')
            ->join('hc_protocolo as hp', 'hp.id', 'ip.id_hc_protocolo')
            ->join('historiaclinica as h', 'h.hcid', 'hp.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->where('h.id_paciente', $id_paciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('ip.estado', '3')
            ->select('ip.*')
            ->orderBy('ip.created_at', 'desc')
            ->orderBy('ip.id', 'desc')
            ->first();

        $orden_lab = $this->ordenesLaboratorioOdontologia($id_paciente)
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1')
            ->orderBy('fecha_orden', 'desc')
            ->first();

        $doctor_procedimiento_ecografia = null;
        $nuevo_armado = $this->ultimoProtocoloConImagenesOdontologia($id_paciente, [2, 4]);

        $consulta_nueva = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftJoin('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('a.estado_cita', '<>', '3')
            ->orderBy('h.created_at', 'desc')
            ->select('h.*', 'a.fechaini', 'hc_evo.motivo', 'hc_evo.cuadro_clinico', 'hc_evo.id as id_evolucion')
            ->get()
            ->first();

        $fecha = date('Y-m-d H:i:s');
        $prox_procedimientos = Agenda::where('id_paciente', $id_paciente)
            ->whereIn('espid', $this->odontologiaEspecialidadIds())
            ->where('estado', '<>', '0')
            ->where('proc_consul', '1')
            ->where('fechaini', '>=', $fecha)
            ->orderBy('fechaini', 'asc')
            ->first();

        $odont_procedimientos = [];
        $ultimo_procedimiento_odont = null;
        if (Schema::hasTable($this->tablaProcedimientoClinico('detalle')) && Schema::hasTable($this->tablaProcedimientoClinico())) {
            $odont_procedimientos = DB::table($this->tablaProcedimientoClinico('detalle').' as op')
                ->leftJoin($this->tablaProcedimientoClinico().' as p', 'p.id', 'op.id_odont_procedimiento')
                ->leftJoin('users as u', 'u.id', 'op.id_doctor')
                ->where('op.id_paciente', $id_paciente)
                ->where('op.estado', '1')
                ->select('op.*', 'p.codigo', 'p.nombre as procedimiento_nombre', 'u.nombre1 as doctor_nombre1', 'u.apellido1 as doctor_apellido1')
                ->orderBy('op.fecha', 'desc')
                ->orderBy('op.id', 'desc')
                ->take(8)
                ->get();
            $ultimo_procedimiento_odont = count($odont_procedimientos) > 0 ? $odont_procedimientos[0] : null;
        }

        $ultimo_formulario033 = null;
        if (Schema::hasTable('odont_formulario033')) {
            $ultimo_formulario033 = OdontFormulario033::where('id_paciente', $id_paciente)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
        }

        $ultima_cotizacion_odont = null;
        if (Schema::hasTable('proforma_cabecera')) {
            $ultima_cotizacion_odont = DB::table('proforma_cabecera')->where('id_paciente', $id_paciente)
                ->where('observacion', 'like', 'Cotizacion odontologica:%')
                ->where('estado', '<>', '0')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();
        }

        $consultaOdontologica = DB::table('agenda as a')
            ->leftJoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->leftJoin('hc_evolucion as e', 'e.hcid', 'h.hcid')
            ->leftJoin('hc_procedimientos as hp', 'hp.id', 'e.hc_id_procedimiento')
            ->where('a.id_paciente', $id_paciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('a.proc_consul', 0)
            ->where(function ($query) {
                $query->where('a.estado', '<>', '0')
                    ->orWhereNotNull('h.hcid');
            })
            ->where(function ($query) {
                $query->whereNull('a.estado_cita')
                    ->orWhere('a.estado_cita', '<>', '3');
            })
            ->select(
                'h.*',
                'a.id as id_agenda',
                'a.fechaini',
                'e.cuadro_clinico',
                DB::raw('COALESCE(hp.id_doctor_examinador, a.id_doctor1) as id_doctor_examinador')
            )
            ->orderBy('a.fechaini', 'desc')
            ->orderBy('e.id', 'desc')
            ->first();

        $vistaHistoria = app(HistoriaPacienteController::class)->historia_paciente_index($id_paciente);
        if (! method_exists($vistaHistoria, 'getData')) {
            return $vistaHistoria;
        }

        $datosHistoria = $vistaHistoria->getData();

        return view('odontologia.buscador', array_merge($datosHistoria, [
            'seguro' => $seguro_actual,
            'evoluciones' => $evoluciones,
            'procedimientos_observaciones' => $procedimientos_observaciones,
            'especialidad' => $especialidad,
            'hc_rec' => $hc_rec,
            'estudios' => $estudios,
            'orden_lab' => $orden_lab,
            'doctor_procedimiento_ecografia' => $doctor_procedimiento_ecografia,
            'nuevo_armado' => $nuevo_armado,
            'prox_procedimientos' => $prox_procedimientos,
            'laboratorio_externo' => null,
            'biopsias_1' => null,
            'biopsias_2' => null,
            'doctor_procedimiento_endoscopico' => null,
            'doctor_procedimiento_funcional' => null,
            'doctor_procedimiento_quirurgico' => null,
            'consulta_nueva' => $consultaOdontologica,
            'historia_actual' => $historia_actual,
            'agenda_actual' => $agenda_actual,
            'doctor_actual' => $doctor_actual,
            'seguro_actual' => $seguro_actual,
            'empresa_actual' => $empresa_actual,
            'odont_procedimientos' => $odont_procedimientos,
            'ultimo_procedimiento_odont' => $ultimo_procedimiento_odont,
            'ultimo_formulario033' => $ultimo_formulario033,
            'ultima_cotizacion_odont' => $ultima_cotizacion_odont,
        ]));
    }

    private function ordenesLaboratorioOdontologia($idPaciente)
    {
        return DB::table('examen_orden as eo')
            ->leftJoin('historiaclinica as h_eo', 'h_eo.hcid', 'eo.hcid')
            ->leftJoin('agenda as a_hc', 'a_hc.id', 'h_eo.id_agenda')
            ->leftJoin('agenda as a_eo', 'a_eo.id', 'eo.id_agenda')
            ->leftJoin('agenda as a_consulta', 'a_consulta.id', 'eo.id_agenda_consulta')
            ->where('eo.id_paciente', $idPaciente)
            ->where(function ($query) {
                $query->whereIn('a_hc.espid', $this->odontologiaEspecialidadIds())
                    ->orWhereIn('a_eo.espid', $this->odontologiaEspecialidadIds())
                    ->orWhereIn('a_consulta.espid', $this->odontologiaEspecialidadIds());
            });
    }

    private function protocolosOdontologia($idPaciente, $tipos)
    {
        $procedimientosCompletos = DB::table('historiaclinica as h')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftJoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->whereIn('gp.tipo_procedimiento', $tipos)
            ->where('hc_p.estado', '1')
            ->select(
                'pc.nombre_general as nombre',
                'u.nombre1',
                'u.apellido1',
                'u.id',
                'hc_p.id_doctor_examinador',
                'gp.tipo_procedimiento',
                'h.hcid',
                'h.hcid as id_hc',
                'hc_proto.hallazgos',
                'hc_proto.conclusion',
                'hc_proto.id as id_protocolo',
                'hc_proto.id as hc_proto_id',
                'hc_proto.id_hc_procedimientos as id_procedimiento',
                'hc_proto.fecha as fecha',
                'hc_proto.created_at as created_at_proto',
                'hc_p.id_doctor_examinador2',
                'hc_proto.fecha_operacion as f_operacion',
                'h.id_agenda',
                'hc_p.id_doctor_responsable',
                'hc_p.id_seguro as seguro_final',
                'h.id_seguro as hc_id_seguro'
            )
            ->orderBy('hc_proto.created_at', 'desc')
            ->get();

        $procedimientosFinales = DB::table('historiaclinica as h')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftJoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->leftJoin($this->tablaProcedimientoClinico().' as odont', 'odont.id_hc_protocolo', 'hc_proto.id')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->whereIn('hc_proto.tipo_procedimiento', $tipos)
            ->where('hc_p.estado', '1')
            ->select(
                DB::raw('NULL as nombre'),
                'odont.nombre as odont_nombre',
                'odont.id as odont_procedimiento_id',
                'u.nombre1',
                'u.apellido1',
                'u.id',
                'hc_p.id_doctor_examinador',
                'hc_proto.tipo_procedimiento',
                'h.hcid',
                'h.hcid as id_hc',
                'hc_proto.hallazgos',
                'hc_proto.conclusion',
                'hc_proto.id as id_protocolo',
                'hc_proto.id as hc_proto_id',
                'hc_proto.id_hc_procedimientos as id_procedimiento',
                'hc_proto.fecha as fecha',
                'hc_proto.created_at as created_at_proto',
                'hc_p.id_doctor_examinador2',
                'hc_proto.fecha_operacion as f_operacion',
                'h.id_agenda',
                'hc_p.id_doctor_responsable',
                'hc_p.id_seguro as seguro_final',
                'h.id_seguro as hc_id_seguro'
            )
            ->orderBy('hc_proto.created_at', 'desc')
            ->get();

        return [$procedimientosCompletos, $procedimientosFinales];
    }

    private function ultimoProtocoloOdontologia($idPaciente, $tipos)
    {
        [$completos, $finales] = $this->protocolosOdontologia($idPaciente, $tipos);
        $ultimoCompleto = $completos->first();
        $ultimoFinal = $finales->first();

        if (is_null($ultimoCompleto)) {
            return $ultimoFinal;
        }
        if (is_null($ultimoFinal)) {
            return $ultimoCompleto;
        }

        return strtotime($ultimoFinal->created_at_proto) >= strtotime($ultimoCompleto->created_at_proto)
            ? $ultimoFinal
            : $ultimoCompleto;
    }

    private function ultimoProtocoloConImagenesOdontologia($idPaciente, $tipos)
    {
        [$completos, $finales] = $this->protocolosOdontologia($idPaciente, $tipos);

        return $completos->merge($finales)
            ->sortByDesc(function ($protocolo) {
                return $protocolo->created_at_proto;
            })
            ->first(function ($protocolo) {
                return hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo)
                    ->where('estado', 1)
                    ->where(function ($query) {
                        $query->where('nombre', 'not like', '%.mp4')
                            ->where('nombre', 'not like', '%.webm');
                    })
                    ->exists();
            });
    }

    public function recetasPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        if (is_null($paciente) || $this->rolNew('2')) {
            return response()->view('errors.404', [], 404);
        }

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->orderBy('a.fechaini', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.fecha_atencion')
            ->get();

        return view('hc4/recetas/index', ['hist_recetas' => $hist_recetas, 'paciente' => $paciente]);
    }

    public function nuevaRecetaPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        if (is_null($paciente) || $this->rolNew('2')) {
            return response()->view('errors.404', [], 404);
        }

        $idDoctor = Auth::user()->id;
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $agendaBase = Agenda::where('id_paciente', $idPaciente)
            ->whereIn('espid', $this->odontologiaEspecialidadIds())
            ->where('estado', '<>', '0')
            ->orderBy('fechaini', 'desc')
            ->first();
        $evolucion = DB::table('historiaclinica as h')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_evolucion as e', 'e.hcid', 'h.hcid')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('e.secuencia', 0)
            ->whereNotNull('e.cuadro_clinico')
            ->select('e.*')
            ->orderBy('e.updated_at', 'desc')
            ->first();

        $texto = '';
        if (! is_null($evolucion)) {
            $diagnosticos = DB::table('hc_cie10')
                ->where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->select('cie10')
                ->distinct()
                ->get();
            foreach ($diagnosticos as $diagnostico) {
                $cie = Cie_10_4::find($diagnostico->cie10);
                if (is_null($cie)) {
                    $cie = Cie_10_3::find($diagnostico->cie10);
                }
                if (! is_null($cie)) {
                    $texto .= '<div class="cie10-receta">'.$diagnostico->cie10.':'.trim($cie->descripcion).'</div>';
                }
            }
        }

        $idSeguro = ! is_null($agendaBase) && ! is_null($agendaBase->id_seguro)
            ? $agendaBase->id_seguro
            : $paciente->id_seguro;
        $odontologiaIds = $this->odontologiaEspecialidadIds();
        $idEspecialidad = DB::table('user_espe')
            ->where('usuid', $idDoctor)
            ->whereIn('espid', $odontologiaIds)
            ->value('espid');
        if (is_null($idEspecialidad) && ! is_null($agendaBase) && in_array((int) $agendaBase->espid, $odontologiaIds, true)) {
            $idEspecialidad = $agendaBase->espid;
        }
        if (is_null($idEspecialidad)) {
            $idEspecialidad = reset($odontologiaIds);
        }

        $idReceta = DB::transaction(function () use ($paciente, $idPaciente, $idDoctor, $idSeguro, $idEspecialidad, $agendaBase, $ip, $texto) {
            $idAgenda = Agenda::insertGetId([
                'fechaini' => date('Y-m-d H:i:s'),
                'fechafin' => date('Y-m-d H:i:s'),
                'id_paciente' => $idPaciente,
                'id_doctor1' => $idDoctor,
                'proc_consul' => '4',
                'estado_cita' => '4',
                'espid' => $idEspecialidad,
                'observaciones' => 'RECETA ODONTOLOGICA CREADA POR EL DOCTOR',
                'id_seguro' => $idSeguro,
                'id_empresa' => ! is_null($agendaBase) ? $agendaBase->id_empresa : null,
                'estado' => '4',
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $idDoctor,
                'id_usuariomod' => $idDoctor,
            ]);
            $idHistoria = Historiaclinica::insertGetId([
                'parentesco' => $paciente->parentesco,
                'id_usuario' => $paciente->id_usuario,
                'id_agenda' => $idAgenda,
                'id_paciente' => $idPaciente,
                'id_seguro' => $idSeguro,
                'id_doctor1' => $idDoctor,
                'id_usuariocrea' => $idDoctor,
                'id_usuariomod' => $idDoctor,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ]);
            $idRecetaNueva = hc_receta::insertGetId([
                'id_hc' => $idHistoria,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $idDoctor,
                'id_usuariomod' => $idDoctor,
                'ip_creacion' => $ip,
                'rp' => $texto,
            ]);

            Hc_Log::create([
                'anterior' => 'ODONTOLOGIA -> El Dr. creo nueva receta',
                'nuevo' => 'ODONTOLOGIA -> El Dr. creo nueva receta',
                'id_paciente' => $idPaciente,
                'id_usuariomod' => $idDoctor,
                'id_usuariocrea' => $idDoctor,
                'ip_modificacion' => $ip,
                'ip_creacion' => $ip,
            ]);

            return $idRecetaNueva;
        });

        $hc_receta = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftJoin('seguros as s', 's.id', 'h.id_seguro')
            ->where('r.id', $idReceta)
            ->select('r.*', 'a.fechaini', 's.id as id_seguro', 's.nombre as nombre_seguro')
            ->first();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $idPaciente)->get();
        $publica = '0';

        return view('hc4/recetas/nueva_receta', compact('alergiasxpac', 'paciente', 'idPaciente', 'hc_receta', 'publica'))
            ->with('id_paciente', $idPaciente);
    }

    public function imagenesPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        [$procedimientos1, $procedimientos2] = $this->protocolosOdontologia($idPaciente, [2, 4]);
        $doctores = User::where('id_tipo_usuario', 3)->orderBy('apellido1')->get();
        $anestesiologo = User::where('id_tipo_usuario', 9)->orderBy('apellido1')->get();
        $sedaciones = DB::table('hc_sedacion')->get();

        return view('hc4/imagenes_paciente/index', compact(
            'paciente',
            'procedimientos1',
            'procedimientos2',
            'doctores',
            'anestesiologo',
            'sedaciones'
        ));
    }

    public function visualizadorPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        [$procedimientos1, $procedimientos2] = $this->protocolosOdontologia($idPaciente, [2, 4]);
        $doctores = User::where('id_tipo_usuario', 3)->orderBy('apellido1')->get();

        return view('hc4/estudios_visualizador/index', compact('paciente', 'procedimientos1', 'procedimientos2', 'doctores'));
    }

    public function estudiosPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        [$procedimientos1, $procedimientos2] = $this->protocolosOdontologia($idPaciente, [2]);
        $imagenes2 = DB::table('hc_imagenes_protocolo as ip')
            ->join('hc_protocolo as hp', 'hp.id', 'ip.id_hc_protocolo')
            ->join('historiaclinica as h', 'h.hcid', 'hp.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_procedimientos as hcp', 'hcp.id', 'hp.id_hc_procedimientos')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('hcp.estado', 1)
            ->where('ip.estado', 1)
            ->select('ip.*')
            ->orderBy('ip.id', 'desc')
            ->get();

        return view('hc4/estudios/index', compact('paciente', 'procedimientos1', 'procedimientos2', 'imagenes2'));
    }

    public function procedimientoEcografiaPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        [$procedimientos1, $procedimientos2] = $this->protocolosOdontologia($idPaciente, [2]);

        return view('hc4/procedimiento_ecografia/index', compact('paciente', 'procedimientos1', 'procedimientos2'));
    }

    public function laboratorioPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $ordenes = $this->ordenesLaboratorioOdontologia($idPaciente)
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            ->leftJoin('empresa as em', 'em.id', 'eo.id_empresa')
            ->leftJoin('nivel as n', 'n.id', 'eo.id_nivel')
            ->leftJoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')
            ->leftJoin('users as d', 'd.id', 'eo.id_doctor_ieced')
            ->join('users as cu', 'cu.id', 'eo.id_usuariocrea')
            ->join('users as mu', 'mu.id', 'eo.id_usuariomod')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo')
            ->orderBy('eo.fecha_orden', 'desc')
            ->paginate(2);

        return view('hc4/laboratorio/index', compact('ordenes', 'usuarios', 'paciente'));
    }

    public function resultadosExternosPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);

        return view('hc4/resultados_externos/index', ['biopsias_1' => collect(), 'paciente' => $paciente]);
    }

    public function proximosProcedimientosPaciente($idPaciente)
    {
        $fecha = date('Y-m-d H:i:s');
        $prox_procedimientos = Agenda::where('agenda.id_paciente', $idPaciente)
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->whereIn('agenda.espid', $this->odontologiaEspecialidadIds())
            ->where('agenda.proc_consul', '1')
            ->where('agenda.fechaini', '>=', $fecha)
            ->select('agenda.*', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre')
            ->orderBy('agenda.fechaini', 'asc')
            ->get();
        $procedimientos = Procedimiento::all();
        $paciente = Paciente::find($idPaciente);

        return view('hc4/proximos_procedimientos/index', compact('paciente', 'prox_procedimientos', 'procedimientos'));
    }

    public function ordenesImagenesPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        $edad = ! is_null($paciente) && ! is_null($paciente->fecha_nacimiento)
            ? Carbon::parse($paciente->fecha_nacimiento)->age
            : 0;
        $idsPorEvolucion = DB::table('orden as o')
            ->join('hc_evolucion as e', 'e.id', 'o.id_evolucion')
            ->join('historiaclinica as h', 'h.hcid', 'e.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->where('o.tipo_procedimiento', 2)
            ->where('o.id_paciente', $idPaciente)
            ->where('o.estado', 1)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->distinct()
            ->pluck('o.id')
            ->all();

        // Algunas ordenes se crean antes de que exista una evolucion dental.
        // En ese caso, el procedimiento seleccionado identifica la especialidad.
        $idsPorProcedimiento = DB::table('orden as o')
            ->join('orden_tipo as ot', 'ot.id_orden', 'o.id')
            ->join('orden_procedimiento as op', 'op.id_orden_tipo', 'ot.id')
            ->join('procedimiento as p', 'p.id', 'op.id_procedimiento')
            ->where('o.tipo_procedimiento', 2)
            ->where('o.id_paciente', $idPaciente)
            ->where('o.estado', 1)
            ->whereIn('p.id_especialidad', $this->odontologiaEspecialidadIds())
            ->distinct()
            ->pluck('o.id')
            ->all();

        $idsOrdenes = array_values(array_unique(array_merge($idsPorEvolucion, $idsPorProcedimiento)));
        $listado_ordImagenes = empty($idsOrdenes)
            ? collect()
            : Orden::whereIn('id', $idsOrdenes)->orderBy('id', 'desc')->get();

        return view('odontologia.ordenes_imagenes', compact('paciente', 'listado_ordImagenes', 'edad'));
    }

    public function ordenesLaboratorioPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);

        return view('odontologia.ordenes_laboratorio', compact('paciente'));
    }

    public function ordenesLaboratorioListaPaciente($idPaciente, $editar = '0')
    {
        $paciente = Paciente::find($idPaciente);
        $ids = $this->ordenesLaboratorioOdontologia($idPaciente)
            ->where('eo.estado', '<>', '0')
            ->pluck('eo.id');
        $ordenes = Examen_Orden::whereIn('id', $ids)->orderBy('created_at', 'desc')->get();

        return view('hc4/ordenes/orden_labs/index2', compact('ordenes', 'paciente', 'editar'));
    }

    public function crearOrdenLaboratorioPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        $historia = Historiaclinica::where('historiaclinica.id_paciente', $idPaciente)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->select('historiaclinica.*', 'a.id_seguro as agenda_seguro', 'a.id_empresa as agenda_empresa')
            ->orderBy('a.fechaini', 'desc')
            ->first();
        if (is_null($paciente) || is_null($historia)) {
            return response('No existe una historia clinica odontologica para asociar la orden.', 422);
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $idDoctor = Auth::user()->id;
        $protocolo = Protocolo::where('estado', '2')->where('id_usuariocrea', $idDoctor)->first();
        $idProtocolo = ! is_null($protocolo) ? $protocolo->id : null;
        $idOrden = Examen_Orden::insertGetId([
            'id_paciente' => $idPaciente,
            'hcid' => $historia->hcid,
            'id_agenda' => $historia->id_agenda,
            'id_agenda_consulta' => $historia->id_agenda,
            'anio' => date('Y'),
            'mes' => date('m'),
            'id_protocolo' => $idProtocolo,
            'id_seguro' => $historia->agenda_seguro ? $historia->agenda_seguro : $historia->id_seguro,
            'id_empresa' => $historia->agenda_empresa,
            'est_amb_hos' => '0',
            'id_doctor_ieced' => $idDoctor,
            'cantidad' => '0',
            'estado' => '-1',
            'valor' => '0',
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $idDoctor,
            'id_usuariomod' => $idDoctor,
            'fecha_orden' => date('Y-m-d H:i:s'),
        ]);

        $etxt = '';
        if (! is_null($protocolo)) {
            $total = 0;
            $cantidad = 0;
            foreach (Examen_Protocolo::where('id_protocolo', $protocolo->id)->get() as $examenProtocolo) {
                $examen = Examen::find($examenProtocolo->id_examen);
                if (! is_null($examen)) {
                    Examen_Detalle::create([
                        'id_examen_orden' => $idOrden,
                        'id_examen' => $examen->id,
                        'valor' => $examen->valor,
                        'cubre' => 'NO',
                        'ip_creacion' => $ip,
                        'ip_modificacion' => $ip,
                        'id_usuariocrea' => $idDoctor,
                        'id_usuariomod' => $idDoctor,
                    ]);
                    $total += $examen->valor;
                    $cantidad++;
                    $etxt .= '-'.$examen->id;
                }
            }
            Examen_Orden::where('id', $idOrden)->update(['cantidad' => $cantidad, 'valor' => round($total, 2)]);
        }

        Log_usuario::create([
            'id_usuario' => $idDoctor,
            'ip_usuario' => $ip,
            'descripcion' => 'GENERA ORDEN ODONTOLOGIA',
            'dato_ant1' => 'orden: '.$idOrden.'- paciente: '.$paciente->id,
            'dato1' => strtoupper($paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2),
            'dato_ant2' => $etxt,
        ]);

        return $this->ordenesLaboratorioListaPaciente($idPaciente, $idOrden);
    }

    public function crearOrdenImagenesPaciente($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        if (is_null($paciente)) {
            return response()->view('errors.404', [], 404);
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $idDoctor = Auth::user()->id;
        $evolucion = DB::table('historiaclinica as h')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_evolucion as e', 'e.hcid', 'h.hcid')
            ->where('h.id_paciente', $idPaciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('e.secuencia', 0)
            ->whereNotNull('e.cuadro_clinico')
            ->select('e.*', 'a.id_empresa')
            ->orderBy('e.updated_at', 'desc')
            ->first();

        $diagnosticosTexto = '';
        if (! is_null($evolucion)) {
            foreach (DB::table('hc_cie10')->where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)->groupBy('cie10')->get() as $diagnostico) {
                $cie = Cie_10_4::find($diagnostico->cie10);
                if (is_null($cie)) {
                    $cie = Cie_10_3::find($diagnostico->cie10);
                }
                if (! is_null($cie)) {
                    $diagnosticosTexto .= ($diagnosticosTexto == '' ? '' : '<br>').$diagnostico->cie10.':'.$cie->descripcion.'-'.$diagnostico->presuntivo_definitivo;
                }
            }
        }

        $idOrden = Orden::insertGetId([
            'id_paciente' => $idPaciente,
            'id_doctor' => $idDoctor,
            'id_evolucion' => ! is_null($evolucion) ? $evolucion->id : null,
            'motivo_consulta' => ! is_null($evolucion) ? $evolucion->motivo : null,
            'resumen_clinico' => ! is_null($evolucion) ? $evolucion->cuadro_clinico : null,
            'diagnosticos' => $diagnosticosTexto,
            'fecha_orden' => date('Y-m-d H:i:s'),
            'tipo_procedimiento' => '2',
            'anio' => date('Y'),
            'mes' => date('m'),
            'id_usuariocrea' => $idDoctor,
            'id_usuariomod' => $idDoctor,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);
        $orden = Orden::find($idOrden);

        if (! is_null($evolucion)) {
            Orden_012::create([
                'id_paciente' => $idPaciente,
                'id_doctor_solicita' => $idDoctor,
                'id_doctor_firma' => $idDoctor,
                'fecha_orden' => $orden->fecha_orden,
                'id_hc_evolucion' => $evolucion->id,
                'id_orden' => $idOrden,
                'estado' => '1',
                'rutina' => '1',
                'puede_mover' => '1',
                'motivo' => $evolucion->motivo,
                'id_empresa' => $evolucion->id_empresa,
                'cuadro_clinico' => $evolucion->cuadro_clinico,
                'id_usuariocrea' => $idDoctor,
                'id_usuariomod' => $idDoctor,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ]);
        }

        Hc_Log::create([
            'anterior' => 'ODONTOLOGIA -> El Dr. creo nueva orden de imagenes',
            'nuevo' => 'ODONTOLOGIA -> El Dr. creo nueva orden de imagenes',
            'id_paciente' => $idPaciente,
            'id_usuariocrea' => $idDoctor,
            'id_usuariomod' => $idDoctor,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);

        return $this->editarOrdenImagenesPaciente($idOrden, $idPaciente);
    }

    private function catalogoImagenesOdontologia()
    {
        return Procedimiento::where('procedimiento.estado', 1)
            ->join('grupo_procedimiento as gp', 'gp.id', '=', 'procedimiento.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', 2)
            ->whereIn('procedimiento.id_especialidad', $this->odontologiaEspecialidadIds())
            ->whereNull('procedimiento.deleted_at')
            ->orderBy('procedimiento.nombre')
            ->get(['procedimiento.*']);
    }

    public function editarOrdenImagenesPaciente($idOrden, $idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        $orden = Orden::where('id', $idOrden)
            ->where('id_paciente', $idPaciente)
            ->where('tipo_procedimiento', 2)
            ->where('estado', 1)
            ->first();
        if (is_null($paciente) || is_null($orden) || is_null($this->historiaOdontologicaPaciente($idPaciente))) {
            return response()->view('errors.404', [], 404);
        }

        $edad = ! is_null($paciente->fecha_nacimiento)
            ? Carbon::parse($paciente->fecha_nacimiento)->age
            : 0;
        $ndoctor = User::find($orden->id_doctor);
        $px = $this->catalogoImagenesOdontologia();

        return view('odontologia.ordenes_imagenes_editar', [
            'data_orden_imag' => $orden,
            'ndoctor' => $ndoctor,
            'edad' => $edad,
            'paciente' => $paciente,
            'px' => $px,
        ]);
    }

    public function consultasPaciente($id_paciente)
    {
        if ($this->rolNew('2')) {
            return redirect('/');
        }

        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return response()->view('errors.404', [], 404);
        }
        $consultas = DB::table('agenda as a')
            ->leftJoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->leftJoin('especialidad as e', 'e.id', 'a.espid')
            ->leftJoin('users as u', 'u.id', 'a.id_doctor1')
            ->where('a.id_paciente', $id_paciente)
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('a.proc_consul', 0)
            ->where(function ($query) {
                $query->where('a.estado', '<>', '0')
                    ->orWhereNotNull('h.hcid');
            })
            ->where(function ($query) {
                $query->whereNull('a.estado_cita')
                    ->orWhere('a.estado_cita', '<>', '3');
            })
            ->select(
                'h.hcid',
                'a.id as id_agenda',
                'a.fechaini',
                'a.fechafin',
                'a.estado_cita',
                'e.nombre as especialidad_nombre',
                'u.nombre1 as doctor_nombre1',
                'u.nombre2 as doctor_nombre2',
                'u.apellido1 as doctor_apellido1',
                'u.apellido2 as doctor_apellido2'
            )
            ->orderBy('a.fechaini', 'desc')
            ->orderBy('h.hcid', 'desc')
            ->get();

        return view('odontologia.consultas', [
            'paciente' => $paciente,
            'consultas' => $consultas,
        ]);
    }

    public function formularioEmbebido($hcid)
    {
        if ($this->rolNew('2')) {
            return redirect('/');
        }

        $historia = Historiaclinica::where('historiaclinica.hcid', $hcid)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->where('a.proc_consul', 0)
            ->select('historiaclinica.*')
            ->first();
        if (is_null($historia)) {
            return response()->view('errors.404', [], 404);
        }

        return $this->renderizarFormularioEmbebido($historia);
    }

    protected function renderizarFormularioEmbebido(Historiaclinica $historia)
    {
        $vista = $this->vista($historia, false, null, true);
        $secciones = $vista->renderSections();

        return response(isset($secciones['content']) ? $secciones['content'] : $vista->render());
    }

    protected function asegurarHistoriaConsultaOdontologica($idAgenda)
    {
        return DB::transaction(function () use ($idAgenda) {
            $agenda = Agenda::where('id', $idAgenda)
                ->whereIn('espid', $this->odontologiaEspecialidadIds())
                ->where('proc_consul', 0)
                ->where(function ($query) {
                    $query->whereNull('estado_cita')
                        ->orWhere('estado_cita', '<>', '3');
                })
                ->lockForUpdate()
                ->first();
            if (is_null($agenda)) {
                return null;
            }

            $historia = Historiaclinica::where('id_agenda', $agenda->id)->first();
            if (! is_null($historia)) {
                return $historia;
            }

            // Al atender una cita, el flujo legado puede cerrar agenda.estado
            // en 0. Solo se admite ese estado cuando la historia ya existe;
            // nunca se crea una historia nueva para una agenda inactiva.
            if ((string) $agenda->estado === '0') {
                return null;
            }

            $paciente = Paciente::find($agenda->id_paciente);
            if (is_null($paciente)) {
                return null;
            }

            $usuario = Auth::user();
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

            $datosHistoria = [
                'parentesco' => $paciente->parentesco,
                'id_usuario' => $paciente->id_usuario,
                'id_agenda' => $agenda->id,
                'id_paciente' => $agenda->id_paciente,
                'id_seguro' => ! is_null($agenda->id_seguro) ? $agenda->id_seguro : $paciente->id_seguro,
                'id_doctor1' => ! is_null($agenda->id_doctor1) ? $agenda->id_doctor1 : $usuario->id,
                'id_usuariocrea' => $usuario->id,
                'id_usuariomod' => $usuario->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];
            if (Schema::hasColumn('historiaclinica', 'encounter_id') && ! empty($agenda->encounter_id)) {
                $datosHistoria['encounter_id'] = $agenda->encounter_id;
            }

            $hcid = DB::table('historiaclinica')->insertGetId($datosHistoria);

            return Historiaclinica::find($hcid);
        });
    }

    public function actualizarPaciente(Request $request)
    {
        $ip_cliente = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $idusuario = Auth::user()->id;
        $paciente = Paciente::find($request->get('id_paciente'));
        if (is_null($paciente)) {
            return response()->json(['ok' => false, 'mensaje' => 'Paciente no encontrado.'], 404);
        }

        $observacionesAdmin = [
            'id_paciente' => $request->get('id_paciente'),
            'observacion' => $request->get('observacion_admin'),
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,
        ];
        $pacienteObservaciones = Paciente_Observaciones::where('id_paciente', $request->get('id_paciente'))->first();
        if (! is_null($pacienteObservaciones)) {
            Paciente_Observaciones::where('id_paciente', $request->get('id_paciente'))->update($observacionesAdmin);
        } else {
            Paciente_Observaciones::create($observacionesAdmin);
        }

        Hc_Log::create([
            'anterior' => 'ODONTOLOGIA DATOS PRINCIPALES-> observacion: '.$paciente->observacion.' Habitos: '.$paciente->alcohol.' Antecedentes_Patologicos: '.$paciente->antecedentes_pat.' Antecedentes_Familiares: '.$paciente->antecedentes_fam.' Antecedentes_Quirurgicos: '.$paciente->antecedentes_quir,
            'nuevo' => 'ODONTOLOGIA DATOS PRINCIPALES-> observacion: '.$request->get('observacion').' Habitos: '.$request->get('habitos').' Antecedentes_Patologicos: '.$request->get('an_patologicos').' Antecedentes_Familiares: '.$request->get('an_familiares').' Antecedentes_Quirurgicos: '.$request->get('an_quirurgicos'),
            'id_paciente' => $request->get('id_paciente'),
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,
        ]);

        Paciente::where('id', $request->get('id_paciente'))->update([
            'observacion' => $request->get('observacion'),
            'alcohol' => $request->get('habitos'),
            'antecedentes_pat' => $request->get('an_patologicos'),
            'antecedentes_fam' => $request->get('an_familiares'),
            'antecedentes_quir' => $request->get('an_quirurgicos'),
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,
        ]);

        return response()->json(['ok' => true, 'mensaje' => 'Datos del paciente actualizados.']);
    }

    private function historiaOdontologicaPaciente($idPaciente, $hcid = null)
    {
        $query = Historiaclinica::where('historiaclinica.id_paciente', $idPaciente)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->whereIn('a.espid', $this->odontologiaEspecialidadIds())
            ->select('historiaclinica.*', 'a.fechaini as agenda_fechaini');

        if (! is_null($hcid)) {
            $query->where('historiaclinica.hcid', $hcid);
        }

        return $query->orderBy('a.fechaini', 'desc')
            ->orderBy('historiaclinica.hcid', 'desc')
            ->first();
    }

    private function catalogoProcedimientosOdontologia($ids = null)
    {
        $query = Procedimiento::where('estado', 1)
            ->whereIn('id_especialidad', $this->odontologiaEspecialidadIds())
            ->whereNull('deleted_at');

        if (! is_null($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->orderBy('nombre')->get(['id', 'nombre', 'observacion']);
    }

    private function catalogoCie10Odontologia($idEspecialidad = null)
    {
        // Odontologia y Periodontologia comparten el catalogo bucodental. Los
        // IDs cambian entre instalaciones, por eso nunca se fija el historico 3.
        $especialidadIds = $this->odontologiaEspecialidadIds();
        if (! is_null($idEspecialidad) && ! in_array((int) $idEspecialidad, $especialidadIds, true)) {
            $especialidadIds[] = (int) $idEspecialidad;
        }

        $cie10Detalle = DB::table('cie_10_4 as c4')
            ->join('cie_10_3 as c3', 'c3.id', '=', 'c4.id_cie_10_3')
            ->where('c4.estado', 1)
            ->where('c3.estado', 1)
            ->whereIn('c3.id_especialidad', $especialidadIds)
            ->select('c4.id', 'c4.descripcion', 'c4.id_cie_10_3')
            ->get();

        $padresConDetalle = [];
        foreach ($cie10Detalle as $cie10Item) {
            $padresConDetalle[$cie10Item->id_cie_10_3] = true;
        }

        $cie10PadresQuery = DB::table('cie_10_3 as c3')
            ->where('c3.estado', 1)
            ->whereIn('c3.id_especialidad', $especialidadIds)
            ->select('c3.id', 'c3.descripcion');

        if (count($padresConDetalle)) {
            $cie10PadresQuery->whereNotIn('c3.id', array_keys($padresConDetalle));
        }

        return $cie10Detalle->merge($cie10PadresQuery->get())->sortBy('id')->values();
    }

    private function diagnosticosProcedimientoSeleccionados($diagnostico)
    {
        if (is_null($diagnostico) || trim((string) $diagnostico) === '') {
            return [];
        }

        $decodificados = json_decode($diagnostico, true);
        if (! is_array($decodificados)) {
            preg_match_all('/[A-Z][0-9]{2,3}/i', (string) $diagnostico, $coincidencias);
            $decodificados = isset($coincidencias[0]) ? $coincidencias[0] : [];
        }

        return array_values(array_unique(array_filter(array_map(function ($codigo) {
            return strtoupper(trim((string) $codigo));
        }, $decodificados))));
    }

    private function columnaTablaExiste($tabla, $columna)
    {
        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $tabla)
            ->where('COLUMN_NAME', $columna)
            ->exists();
    }

    private function protocoloImagenesOdontologiaExistente(OdontProcedimiento $procedimiento)
    {
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico(), 'id_hc_protocolo') || empty($procedimiento->id_hc_protocolo)) {
            return null;
        }

        return hc_protocolo::where('hc_protocolo.id', $procedimiento->id_hc_protocolo)
            ->join('historiaclinica as historia', 'historia.hcid', 'hc_protocolo.hcid')
            ->where('historia.id_paciente', $procedimiento->id_paciente)
            ->select('hc_protocolo.*')
            ->first();
    }

    private function asegurarHistoriaProcedimientoOdontologico(OdontProcedimiento $procedimiento)
    {
        $historia = Historiaclinica::where('hcid', $procedimiento->hcid)
            ->where('id_paciente', $procedimiento->id_paciente)
            ->first();

        if (is_null($historia) && ! empty($procedimiento->id_agenda)) {
            $agenda = Agenda::where('id', $procedimiento->id_agenda)
                ->where('id_paciente', $procedimiento->id_paciente)
                ->where('estado', '<>', '0')
                ->lockForUpdate()
                ->first();

            if (! is_null($agenda)) {
                $historia = Historiaclinica::where('id_agenda', $agenda->id)
                    ->where('id_paciente', $procedimiento->id_paciente)
                    ->first();

                if (is_null($historia)) {
                    $paciente = Paciente::find($procedimiento->id_paciente);
                    if (! is_null($paciente)) {
                        $usuario = Auth::user();
                        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                        $datosHistoria = [
                            'parentesco' => $paciente->parentesco,
                            'id_usuario' => $paciente->id_usuario,
                            'id_agenda' => $agenda->id,
                            'id_paciente' => $procedimiento->id_paciente,
                            'id_seguro' => ! is_null($agenda->id_seguro) ? $agenda->id_seguro : $paciente->id_seguro,
                            'id_doctor1' => ! is_null($agenda->id_doctor1) ? $agenda->id_doctor1 : $usuario->id,
                            'id_usuariocrea' => $usuario->id,
                            'id_usuariomod' => $usuario->id,
                            'ip_creacion' => $ip,
                            'ip_modificacion' => $ip,
                        ];
                        if (Schema::hasColumn('historiaclinica', 'encounter_id') && ! empty($agenda->encounter_id)) {
                            $datosHistoria['encounter_id'] = $agenda->encounter_id;
                        }
                        $hcid = DB::table('historiaclinica')->insertGetId($datosHistoria);
                        $historia = Historiaclinica::find($hcid);
                    }
                }
            }
        }

        if (is_null($historia)) {
            $historia = $this->historiaOdontologicaPaciente($procedimiento->id_paciente);
        }
        if (is_null($historia)) {
            return null;
        }

        if ((string) $procedimiento->hcid !== (string) $historia->hcid) {
            $usuario = Auth::user();
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $procedimiento->hcid = $historia->hcid;
            $procedimiento->id_usuariomod = $usuario->id;
            $procedimiento->ip_modificacion = $ip;
            $procedimiento->save();

            $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
                ->update([
                    'hcid' => $historia->hcid,
                    'id_usuariomod' => $usuario->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => Carbon::now(),
                ]);
        }

        return $historia;
    }

    private function asegurarProtocoloImagenesOdontologia(OdontProcedimiento $procedimiento)
    {
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico(), 'id_hc_protocolo')) {
            return null;
        }

        $protocoloExistente = $this->protocoloImagenesOdontologiaExistente($procedimiento);
        if (! is_null($protocoloExistente)) {
            return $protocoloExistente;
        }

        return DB::transaction(function () use ($procedimiento) {
            $procedimiento = $this->consultaProcedimientoClinico()->where('id', $procedimiento->id)->lockForUpdate()->first();
            $protocoloExistente = $this->protocoloImagenesOdontologiaExistente($procedimiento);
            if (! is_null($protocoloExistente)) {
                return $protocoloExistente;
            }

            $historia = $this->asegurarHistoriaProcedimientoOdontologico($procedimiento);
            if (is_null($historia)) {
                return null;
            }

            $detalle = $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
                ->where('estado', 1)
                ->orderBy('id')
                ->first();
            $idUsuario = Auth::user()->id;
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $fecha = ! is_null($detalle) && ! empty($detalle->fecha) ? $detalle->fecha : date('Y-m-d');
            $idDoctor = ! is_null($detalle) && ! empty($detalle->id_doctor) ? $detalle->id_doctor : $idUsuario;
            $auditoria = [
                'id_usuariocrea' => $idUsuario,
                'id_usuariomod' => $idUsuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $idHcProcedimiento = hc_procedimientos::insertGetId(array_merge($auditoria, [
                'id_hc' => $historia->hcid,
                'id_seguro' => $historia->id_seguro,
                'id_subseguro' => $historia->id_subseguro,
                'fecha' => $fecha,
                'hora_inicio' => date('H:i:s'),
                'hora_fin' => date('H:i:s'),
                'estado' => 1,
                'id_doctor_examinador' => $idDoctor,
                'observaciones' => 'Procedimiento odontologico #'.$procedimiento->id,
                'tipo_procedimiento' => 4,
            ]));

            $idProtocolo = hc_protocolo::insertGetId(array_merge($auditoria, [
                'hcid' => $historia->hcid,
                'id_hc_procedimientos' => $idHcProcedimiento,
                'fecha' => $fecha,
                'fecha_operacion' => $fecha,
                'hora_inicio' => date('H:i:s'),
                'hora_fin' => date('H:i:s'),
                'estado_final' => ' ',
                'tipo_procedimiento' => 4,
                'descripcion' => mb_substr((string) $procedimiento->nombre, 0, 255),
            ]));

            $procedimiento->id_hc_protocolo = $idProtocolo;
            $procedimiento->id_usuariomod = $idUsuario;
            $procedimiento->ip_modificacion = $ip;
            $procedimiento->save();

            return hc_protocolo::find($idProtocolo);
        });
    }

    public function procedimientosPaciente(Request $request, $idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        if (is_null($paciente)) {
            return response()->view('errors.404', [], 404);
        }

        $procedimientos = collect();
        if (Schema::hasTable($this->tablaProcedimientoClinico()) && Schema::hasTable($this->tablaProcedimientoClinico('detalle'))) {
            $procedimientos = $this->consultaProcedimientoClinico()->where('id_paciente', $idPaciente)
                ->where('estado', 1)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $ids = $procedimientos->pluck('id')->all();
            $detalles = collect();
            if (count($ids) > 0) {
                $detalles = DB::table($this->tablaProcedimientoClinico('detalle').' as op')
                    ->leftJoin('users as u', 'u.id', 'op.id_doctor')
                    ->whereIn('op.id_odont_procedimiento', $ids)
                    ->where('op.estado', 1)
                    ->select(
                        'op.*',
                        'u.nombre1 as doctor_nombre1',
                        'u.nombre2 as doctor_nombre2',
                        'u.apellido1 as doctor_apellido1',
                        'u.apellido2 as doctor_apellido2'
                    )
                    ->orderBy('op.id', 'asc')
                    ->get()
                    ->groupBy('id_odont_procedimiento');
            }

            foreach ($procedimientos as $procedimiento) {
                $procedimiento->detalles_odont = $detalles->get($procedimiento->id, collect());
            }
        }

        $abrirId = (int) $request->get('abrir', 0);

        $prefijoModulo = $this->prefijoModuloClinico();

        return view('odontologia.procedimientos.index', compact('paciente', 'procedimientos', 'abrirId', 'prefijoModulo'));
    }

    public function seleccionarProcedimientos($idPaciente)
    {
        $paciente = Paciente::find($idPaciente);
        if (is_null($paciente)) {
            return response()->view('errors.404', [], 404);
        }

        $historia = $this->historiaOdontologicaPaciente($idPaciente);
        $procedimientos = $this->catalogoProcedimientosOdontologia();

        $prefijoModulo = $this->prefijoModuloClinico();

        return view('odontologia.procedimientos.seleccionar', compact('paciente', 'historia', 'procedimientos', 'prefijoModulo'));
    }

    public function guardarProcedimiento(Request $request)
    {
        if (! Schema::hasTable($this->tablaProcedimientoClinico()) || ! Schema::hasTable($this->tablaProcedimientoClinico('detalle'))) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de Odontologia.'], 422);
        }
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico('detalle'), 'id_procedimiento_catalogo')) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de Odontologia.'], 422);
        }
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico('detalle'), 'cara_dental')) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de Odontologia.'], 422);
        }

        $idPaciente = $request->get('id_paciente');
        $historia = $this->historiaOdontologicaPaciente($idPaciente, $request->get('hcid'));
        if (is_null($historia)) {
            return response()->json(['ok' => false, 'mensaje' => 'No existe una historia clinica odontologica para este paciente.'], 422);
        }

        $idsSeleccionados = $request->get('procedimientos', []);
        if (! is_array($idsSeleccionados)) {
            $idsSeleccionados = [$idsSeleccionados];
        }
        $idsSeleccionados = array_values(array_unique(array_filter(array_map('intval', $idsSeleccionados))));
        if (count($idsSeleccionados) === 0) {
            return response()->json(['ok' => false, 'mensaje' => 'Seleccione al menos un procedimiento odontologico.'], 422);
        }

        $catalogo = $this->catalogoProcedimientosOdontologia($idsSeleccionados)->keyBy('id');
        if ($catalogo->count() !== count($idsSeleccionados)) {
            return response()->json(['ok' => false, 'mensaje' => 'Uno de los procedimientos seleccionados no pertenece a odontologia.'], 422);
        }

        $user = Auth::user();
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $now = Carbon::now();
        $nombres = $catalogo->pluck('nombre')->map(function ($nombre) {
            return trim((string) $nombre);
        })->all();

        $base = [
            'id_usuariocrea' => $user->id,
            'id_usuariomod' => $user->id,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $idProcedimiento = DB::transaction(function () use ($historia, $base, $user, $now, $catalogo, $idsSeleccionados, $nombres) {
            $ultimoFormulario = Schema::hasTable($this->tablaFormulario033())
                ? $this->consultaFormulario033()->where('hcid', $historia->hcid)->orderBy('id', 'desc')->first()
                : null;
            $procedimiento = $this->consultaProcedimientoClinico()->create(array_merge($base, [
                'hcid' => $historia->hcid,
                'id_agenda' => $historia->id_agenda,
                'id_paciente' => $historia->id_paciente,
                'codigo' => (string) $idsSeleccionados[0],
                'nombre' => mb_substr(strtoupper(implode(' + ', $nombres)), 0, 255),
                'descripcion' => null,
                'estado' => 1,
            ]));

            foreach ($idsSeleccionados as $idCatalogo) {
                $procedimientoCatalogo = $catalogo->get($idCatalogo);
                $this->consultaDetalleProcedimientoClinico()->create(array_merge($base, [
                    'hcid' => $historia->hcid,
                    'id_agenda' => $historia->id_agenda,
                    'id_paciente' => $historia->id_paciente,
                    'id_formulario033' => ! is_null($ultimoFormulario) ? $ultimoFormulario->id : null,
                    'id_odont_procedimiento' => $procedimiento->id,
                    'id_procedimiento_catalogo' => $procedimientoCatalogo->id,
                    'fecha' => $now->toDateString(),
                    'pieza_fdi' => null,
                    'cara_dental' => null,
                    'diagnostico' => null,
                    'procedimiento' => trim((string) $procedimientoCatalogo->nombre),
                    'observaciones' => null,
                    'estado_procedimiento' => 'REALIZADO',
                    'id_doctor' => $user->id,
                    'estado' => 1,
                ]));
            }

            return $procedimiento->id;
        });

        return response()->json([
            'ok' => true,
            'mensaje' => 'Procedimiento odontologico creado.',
            'id' => $idProcedimiento,
            'id_abrir' => $idProcedimiento,
        ]);
    }

    public function editarProcedimiento($id)
    {
        $procedimiento = $this->consultaProcedimientoClinico()->where('id', $id)->where('estado', 1)->first();
        if (is_null($procedimiento)) {
            return response()->view('errors.404', [], 404);
        }

        $detalles = $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
            ->where('estado', 1)
            ->orderBy('id')
            ->get();
        $detalleBase = $detalles->first();
        $seleccionados = $detalles->pluck('id_procedimiento_catalogo')->filter()->map(function ($idCatalogo) {
            return (int) $idCatalogo;
        })->all();

        if (count($seleccionados) === 0 && ! empty($procedimiento->codigo)) {
            $seleccionados = [(int) $procedimiento->codigo];
        }

        $catalogo = $this->catalogoProcedimientosOdontologia();
        $cie10 = $this->catalogoCie10Odontologia();
        $diagnosticosSeleccionados = $this->diagnosticosProcedimientoSeleccionados(
            ! is_null($detalleBase) ? $detalleBase->diagnostico : null
        );
        $doctores = User::where('id_tipo_usuario', 3)
            ->where('estado', 1)
            ->orderBy('apellido1')
            ->orderBy('nombre1')
            ->get(['id', 'nombre1', 'nombre2', 'apellido1', 'apellido2']);
        $protocoloImagenes = $this->protocoloImagenesOdontologiaExistente($procedimiento);
        $imagenes = ! is_null($protocoloImagenes)
            ? hc_imagenes_protocolo::where('id_hc_protocolo', $protocoloImagenes->id)
                ->where('estado', 1)
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get()
            : collect();

        $prefijoModulo = $this->prefijoModuloClinico();

        return view('odontologia.procedimientos.editar', compact(
            'procedimiento',
            'detalles',
            'detalleBase',
            'seleccionados',
            'catalogo',
            'cie10',
            'diagnosticosSeleccionados',
            'doctores',
            'imagenes',
            'protocoloImagenes',
            'prefijoModulo'
        ));
    }

    public function imagenesProcedimiento($id)
    {
        $procedimiento = $this->consultaProcedimientoClinico()->where('id', $id)->where('estado', 1)->first();
        if (is_null($procedimiento)) {
            return response()->view('errors.404', [], 404);
        }

        try {
            $protocolo = $this->asegurarProtocoloImagenesOdontologia($procedimiento);
        } catch (\Throwable $error) {
            report($error);

            return response()->json([
                'mensaje' => 'No se pudo preparar el almacenamiento de imágenes del procedimiento.',
            ], 500);
        }
        if (is_null($protocolo)) {
            return response()->json([
                'mensaje' => 'El procedimiento no tiene una agenda o historia clínica válida para almacenar imágenes.',
            ], 422);
        }

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)
            ->where('estado', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        $cimagenes = $imagenes->filter(function ($imagen) {
            return ! in_array(strtolower(pathinfo((string) $imagen->nombre, PATHINFO_EXTENSION)), ['mp4', 'webm'], true);
        })->count();
        $cvideo = $imagenes->filter(function ($imagen) {
            return in_array(strtolower(pathinfo((string) $imagen->nombre, PATHINFO_EXTENSION)), ['mp4', 'webm'], true);
        })->count();
        $imagenesPaciente = DB::table('hc_imagenes_protocolo as imagen')
            ->join('hc_protocolo as protocolo', 'protocolo.id', 'imagen.id_hc_protocolo')
            ->join('historiaclinica as historia', 'historia.hcid', 'protocolo.hcid')
            ->where('historia.id_paciente', $procedimiento->id_paciente)
            ->where('imagen.estado', 1)
            ->select('imagen.*')
            ->orderBy('imagen.id', 'desc')
            ->limit(100)
            ->get();

        $prefijoModulo = $this->prefijoModuloClinico();

        return view('odontologia.procedimientos.imagenes', compact(
            'procedimiento',
            'protocolo',
            'imagenes',
            'imagenesPaciente',
            'cimagenes',
            'cvideo',
            'prefijoModulo'
        ));
    }

    public function actualizarProcedimiento(Request $request, $id)
    {
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico('detalle'), 'id_procedimiento_catalogo')) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de Odontologia.'], 422);
        }
        if (! $this->columnaTablaExiste($this->tablaProcedimientoClinico('detalle'), 'cara_dental')) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de Odontologia.'], 422);
        }

        $procedimiento = $this->consultaProcedimientoClinico()->where('id', $id)->where('estado', 1)->first();
        if (is_null($procedimiento)) {
            return response()->json(['ok' => false, 'mensaje' => 'Procedimiento odontologico no encontrado.'], 404);
        }

        $idsSeleccionados = $request->get('procedimientos', []);
        if (! is_array($idsSeleccionados)) {
            $idsSeleccionados = [$idsSeleccionados];
        }
        $idsSeleccionados = array_values(array_unique(array_filter(array_map('intval', $idsSeleccionados))));
        if (count($idsSeleccionados) === 0) {
            return response()->json(['ok' => false, 'mensaje' => 'Seleccione al menos un procedimiento odontologico.'], 422);
        }

        $catalogo = $this->catalogoProcedimientosOdontologia($idsSeleccionados)->keyBy('id');
        if ($catalogo->count() !== count($idsSeleccionados)) {
            return response()->json(['ok' => false, 'mensaje' => 'Uno de los procedimientos seleccionados no pertenece a odontologia.'], 422);
        }

        $idDoctor = $request->get('id_doctor') ? $request->get('id_doctor') : Auth::user()->id;
        $doctor = User::where('id', $idDoctor)->where('id_tipo_usuario', 3)->where('estado', 1)->first();
        if (is_null($doctor)) {
            return response()->json(['ok' => false, 'mensaje' => 'Seleccione un medico examinador valido.'], 422);
        }

        $fecha = $request->get('fecha');
        if (empty($fecha) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return response()->json(['ok' => false, 'mensaje' => 'Ingrese una fecha valida.'], 422);
        }

        $piezaFdi = trim((string) $request->get('pieza_fdi'));
        if ($piezaFdi !== '' && ! preg_match('/^([1-4][1-8]|[5-8][1-5])$/', $piezaFdi)) {
            return response()->json(['ok' => false, 'mensaje' => 'La pieza FDI debe contener dos digitos validos.'], 422);
        }

        $caraDental = strtoupper(trim((string) $request->get('cara_dental')));
        if ($caraDental !== '' && ! in_array($caraDental, ['V', 'L', 'M', 'D', 'O'], true)) {
            return response()->json(['ok' => false, 'mensaje' => 'Seleccione una cara dental valida.'], 422);
        }
        if ($caraDental !== '' && $piezaFdi === '') {
            return response()->json(['ok' => false, 'mensaje' => 'Ingrese la pieza FDI antes de seleccionar una cara dental.'], 422);
        }

        $diagnosticos = $request->get('diagnosticos', []);
        if (! is_array($diagnosticos)) {
            $diagnosticos = [$diagnosticos];
        }
        $diagnosticos = array_values(array_unique(array_filter(array_map(function ($codigo) {
            return strtoupper(trim((string) $codigo));
        }, $diagnosticos))));
        $catalogoCie10 = $this->catalogoCie10Odontologia()->keyBy(function ($cie) {
            return strtoupper((string) $cie->id);
        });
        foreach ($diagnosticos as $codigo) {
            if (! $catalogoCie10->has($codigo)) {
                return response()->json(['ok' => false, 'mensaje' => 'Uno de los diagnosticos no pertenece al catalogo CIE-10 de odontologia.'], 422);
            }
        }
        $diagnosticosJson = count($diagnosticos) ? json_encode($diagnosticos, JSON_UNESCAPED_UNICODE) : null;

        $user = Auth::user();
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $now = Carbon::now();
        $nombres = $catalogo->pluck('nombre')->map(function ($nombre) {
            return trim((string) $nombre);
        })->all();

        DB::transaction(function () use ($request, $procedimiento, $catalogo, $idsSeleccionados, $idDoctor, $fecha, $piezaFdi, $caraDental, $diagnosticosJson, $user, $ip, $now, $nombres) {
            $procedimiento->update([
                'codigo' => (string) $idsSeleccionados[0],
                'nombre' => mb_substr(strtoupper(implode(' + ', $nombres)), 0, 255),
                'id_usuariomod' => $user->id,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            ]);

            $existentes = $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
                ->where('estado', 1)
                ->orderBy('id')
                ->get();
            $porCatalogo = $existentes->filter(function ($detalle) {
                return ! is_null($detalle->id_procedimiento_catalogo);
            })->keyBy('id_procedimiento_catalogo');
            $legados = $existentes->filter(function ($detalle) {
                return is_null($detalle->id_procedimiento_catalogo);
            })->values();
            $idsConservados = [];

            foreach ($idsSeleccionados as $idCatalogo) {
                $detalle = $porCatalogo->get($idCatalogo);
                if (is_null($detalle) && $legados->count() > 0) {
                    $detalle = $legados->shift();
                }

                $payload = [
                    'id_procedimiento_catalogo' => $idCatalogo,
                    'fecha' => $fecha,
                    'pieza_fdi' => $piezaFdi !== '' ? $piezaFdi : null,
                    'cara_dental' => $caraDental !== '' ? $caraDental : null,
                    'diagnostico' => $diagnosticosJson,
                    'procedimiento' => trim((string) $catalogo->get($idCatalogo)->nombre),
                    'observaciones' => $request->get('observaciones'),
                    'estado_procedimiento' => $request->get('estado_procedimiento') ? $request->get('estado_procedimiento') : 'REALIZADO',
                    'id_doctor' => $idDoctor,
                    'estado' => 1,
                    'id_usuariomod' => $user->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ];

                if (! is_null($detalle)) {
                    $detalle->update($payload);
                    $idsConservados[] = $detalle->id;

                    continue;
                }

                $nuevo = $this->consultaDetalleProcedimientoClinico()->create(array_merge($payload, [
                    'hcid' => $procedimiento->hcid,
                    'id_agenda' => $procedimiento->id_agenda,
                    'id_paciente' => $procedimiento->id_paciente,
                    'id_formulario033' => null,
                    'id_odont_procedimiento' => $procedimiento->id,
                    'id_usuariocrea' => $user->id,
                    'ip_creacion' => $ip,
                    'created_at' => $now,
                ]));
                $idsConservados[] = $nuevo->id;
            }

            $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
                ->where('estado', 1)
                ->whereNotIn('id', $idsConservados)
                ->update([
                    'estado' => 0,
                    'id_usuariomod' => $user->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ]);
        });

        return response()->json([
            'ok' => true,
            'mensaje' => 'Procedimiento odontologico actualizado.',
            'id_abrir' => $procedimiento->id,
        ]);
    }

    public function eliminarProcedimiento($id)
    {
        $procedimiento = $this->consultaProcedimientoClinico()->where('id', $id)->where('estado', 1)->first();
        if (is_null($procedimiento)) {
            return response()->json(['ok' => false, 'mensaje' => 'Procedimiento odontologico no encontrado.'], 404);
        }

        $user = Auth::user();
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $now = Carbon::now();

        DB::transaction(function () use ($procedimiento, $user, $ip, $now) {
            $procedimiento->update([
                'estado' => 0,
                'id_usuariomod' => $user->id,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            ]);
            $this->consultaDetalleProcedimientoClinico()->where('id_odont_procedimiento', $procedimiento->id)
                ->where('estado', 1)
                ->update([
                    'estado' => 0,
                    'id_usuariomod' => $user->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ]);
        });

        return response()->json(['ok' => true, 'mensaje' => 'Procedimiento odontologico eliminado.']);
    }

    public function porHistoria($hcid)
    {
        $historia = Historiaclinica::where('hcid', $hcid)->first();
        if (is_null($historia)) {
            return response()->view('errors.404');
        }

        return $this->vista($historia, false);
    }

    public function porAgenda(Request $request, $id_agenda)
    {
        if ($this->rolNew('2')) {
            return redirect('/');
        }

        $historia = $this->asegurarHistoriaConsultaOdontologica($id_agenda);
        if (is_null($historia)) {
            return response()->view('errors.404', [], 404);
        }

        if ($request->get('embebido') == '1') {
            return $this->renderizarFormularioEmbebido($historia);
        }

        return $this->vista($historia, false);
    }

    public function imprimir($id)
    {
        if (! Schema::hasTable($this->tablaFormulario033())) {
            return response()->view('errors.404');
        }
        $formulario = $this->consultaFormulario033()->find($id);
        if (is_null($formulario)) {
            return response()->view('errors.404');
        }
        $historia = Historiaclinica::where('hcid', $formulario->hcid)->first();
        if (is_null($historia)) {
            return response()->view('errors.404');
        }

        return $this->pdf($historia, $formulario);
    }

    public function json($id)
    {
        if (! Schema::hasTable($this->tablaFormulario033())) {
            return response()->json(['ok' => false, 'mensaje' => 'Ejecute primero las migraciones de '.$this->nombreModuloFormulario033().'.'], 422);
        }
        $formulario = $this->consultaFormulario033()->find($id);
        if (is_null($formulario)) {
            return response()->json(['ok' => false, 'mensaje' => 'Formulario no encontrado.'], 404);
        }
        $payload = json_decode($formulario->snapshot_json, true);

        return response()->json(['ok' => true, 'id_formulario033' => $formulario->id, 'version' => $formulario->version, 'vigente_hasta' => $formulario->vigente_hasta, 'payload' => is_array($payload) ? $payload : []]);
    }

    public function guardar(Request $request)
    {
        if (! Schema::hasTable($this->tablaFormulario033())) {
            return response()->json(['ok' => false, 'mensaje' => 'No existe la estructura de '.$this->nombreModuloFormulario033().'. Ejecute sus migraciones.'], 422);
        }
        $historia = Historiaclinica::where('hcid', $request->get('hcid'))->first();
        if (is_null($historia)) {
            return response()->json(['ok' => false, 'mensaje' => 'Historia clinica no encontrada.'], 404);
        }
        $payload = json_decode($request->get('payload'), true);
        if (! is_array($payload)) {
            return response()->json(['ok' => false, 'mensaje' => 'Payload JSON invalido.'], 422);
        }
        $user = Auth::user();
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $now = Carbon::now();
        $formularios = $this->consultaFormulario033()->where('hcid', $historia->hcid)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $ultimo = $formularios->first();
        $version = 1;
        $id = DB::transaction(function () use ($payload, $historia, $user, $ip, $now, $version, $formularios, $ultimo) {
            foreach ($formularios as $formularioAnterior) {
                if (! is_null($ultimo) && $formularioAnterior->id != $ultimo->id) {
                    $this->eliminarFormularioCompleto($formularioAnterior->id);
                }
            }

            $agendaBase = Agenda::find($historia->id_agenda);
            $empresaBase = (! is_null($agendaBase) && ! is_null($agendaBase->id_empresa)) ? Empresa::find($agendaBase->id_empresa) : null;
            $seguroBase = (! is_null($agendaBase) && ! is_null($agendaBase->id_seguro)) ? Seguro::find($agendaBase->id_seguro) : null;
            $institucionBase = ! is_null($seguroBase) ? $seguroBase->nombre : null;
            $establecimientoBase = ! is_null($empresaBase) ? ($empresaBase->nombre_form ? $empresaBase->nombre_form : $empresaBase->nombrecomercial) : null;
            $unicodigoBase = ! is_null($empresaBase) ? $empresaBase->unicodigo : null;
            if (! isset($payload['bloques']) || ! is_array($payload['bloques'])) {
                $payload['bloques'] = [];
            }
            $payload['bloques']['institucion_sistema'] = $institucionBase;
            $payload['bloques']['unicodigo'] = $unicodigoBase;
            $bloques = $this->arr($payload, 'bloques');
            $vitales = $this->arr($payload, 'signos_vitales');
            $indices = $this->arr($payload, 'indices');
            $cpod = $this->arr($indices, 'cpod');
            $ceod = $this->arr($indices, 'ceod');
            $prof = $this->arr($payload, 'profesional');
            $base = ['id_usuariocrea' => $user->id, 'id_usuariomod' => $user->id, 'ip_creacion' => $ip, 'ip_modificacion' => $ip, 'created_at' => $now, 'updated_at' => $now];

            $datosFormulario = [
                'hcid' => $historia->hcid,
                'id_agenda' => $historia->id_agenda,
                'id_paciente' => $historia->id_paciente,
                'id_doctor' => $this->val($prof, 'id_profesional', $historia->id_doctor1),
                'version' => $version,
                'fecha_atencion' => $this->val($bloques, 'fecha_atencion', date('Y-m-d')),
                'hora_atencion' => $this->val($bloques, 'hora_atencion', date('H:i')),
                'tipo_atencion' => $this->val($bloques, 'tipo_atencion', 'Primera'),
                'nro_hoja' => $this->val($bloques, 'nro_hoja', 1),
                'institucion_sistema' => $this->val($bloques, 'institucion_sistema', $institucionBase),
                'unicodigo' => $this->val($bloques, 'unicodigo', $unicodigoBase),
                'establecimiento' => $this->val($bloques, 'establecimiento', $establecimientoBase),
                'servicio' => $this->val($bloques, 'servicio'),
                'motivo_consulta' => $this->val($bloques, 'motivo_consulta'),
                'enfermedad_actual' => $this->val($bloques, 'enfermedad_actual'),
                'embarazo' => $this->val($bloques, 'embarazo', 'NA'),
                'presion_arterial' => $this->val($vitales, 'presion_arterial'),
                'frecuencia_cardiaca' => $this->val($vitales, 'frecuencia_cardiaca'),
                'temperatura' => $this->val($vitales, 'temperatura'),
                'frecuencia_respiratoria' => $this->val($vitales, 'frecuencia_respiratoria'),
                'alerta_signos_vitales' => $this->val($vitales, 'alerta', 0),
                'plan_diagnostico' => $this->val($bloques, 'plan_diagnostico'),
                'plan_terapeutico' => $this->val($bloques, 'plan_terapeutico'),
                'plan_educacional' => $this->val($bloques, 'plan_educacional'),
                'examenes_complementarios_json' => $this->jsonEncode($this->arr($payload, 'examenes_complementarios')),
                'cpod_c' => $this->val($cpod, 'c', 0),
                'cpod_p' => $this->val($cpod, 'p', 0),
                'cpod_o' => $this->val($cpod, 'o', 0),
                'cpod_total' => $this->val($cpod, 'total', 0),
                'ceod_c' => $this->val($ceod, 'c', 0),
                'ceod_e' => $this->val($ceod, 'e', 0),
                'ceod_o' => $this->val($ceod, 'o', 0),
                'ceod_total' => $this->val($ceod, 'total', 0),
                'snapshot_json' => $this->jsonEncode($payload),
                'estado' => 1,
            ];

            if (is_null($ultimo)) {
                $idFormulario = DB::table($this->tablaFormulario033())->insertGetId(array_merge($base, $datosFormulario, [
                    'fecha_apertura' => $now,
                    'vigente_hasta' => $now->copy()->addDays(365)->toDateString(),
                ]));
            } else {
                $idFormulario = $ultimo->id;
                $this->limpiarDetallesFormulario($idFormulario);
                DB::table($this->tablaFormulario033())->where('id', $idFormulario)->update(array_merge($datosFormulario, [
                    'id_usuariomod' => $user->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ]));
            }

            $this->guardarAntecedentes($idFormulario, $payload, $base);
            $this->guardarEstomatognatico($idFormulario, $payload, $base);
            $idOdontograma = $this->guardarOdontograma($idFormulario, $payload, $base, $now);
            $this->guardarIndicadores($idFormulario, $idOdontograma, $payload, $base);
            $this->guardarDiagnosticos($idFormulario, $payload, $base);
            $this->guardarEvoluciones($idFormulario, $payload, $base, $user->id);

            return $idFormulario;
        });

        $nombreRuta = $request->route() ? $request->route()->getName() : '';
        $flujoPeriodontal = $nombreRuta === 'periodontologia.formulario033.guardar';
        $rutaJson = $flujoPeriodontal ? 'periodontologia.formulario033.json' : 'odontologia.formulario033.json';
        $rutaImprimir = $flujoPeriodontal ? 'periodontologia.formulario033.imprimir' : 'odontologia.formulario033.imprimir';

        return response()->json([
            'ok' => true,
            'mensaje' => $flujoPeriodontal ? 'Formulario 033 de Periodontologia guardado.' : 'Formulario 033 guardado.',
            'id_formulario033' => $id,
            'version' => $version,
            'url_json' => route($rutaJson, ['id' => $id]),
            'url_imprimir' => route($rutaImprimir, ['id' => $id]),
        ]);
    }

    private function denticionPorEdad($edad)
    {
        if (! is_numeric($edad)) {
            return 'permanente';
        }

        return ((int) $edad) <= 11 ? 'temporal' : 'permanente';
    }

    protected function vista(Historiaclinica $historia, $imprimir, $formulario = null, $embebido = false, $contextoFormulario = [])
    {
        $paciente = Paciente::find($historia->id_paciente);
        $agenda = Agenda::find($historia->id_agenda);
        $doctor = User::find($historia->id_doctor1);
        $seguro = (! is_null($agenda) && ! is_null($agenda->id_seguro)) ? Seguro::find($agenda->id_seguro) : (! is_null($historia->id_seguro) ? Seguro::find($historia->id_seguro) : null);
        $sala = (! is_null($agenda) && ! is_null($agenda->id_sala)) ? Sala::find($agenda->id_sala) : null;
        $hospital = (! is_null($sala)) ? $sala->hospital : null;
        $procedimiento = (! is_null($agenda) && ! is_null($agenda->id_procedimiento)) ? Procedimiento::find($agenda->id_procedimiento) : null;
        $empresa = (! is_null($agenda) && ! is_null($agenda->id_empresa)) ? Empresa::find($agenda->id_empresa) : Empresa::where('prioridad', 1)->first();
        $edad = (! is_null($paciente) && $paciente->fecha_nacimiento) ? Carbon::parse($paciente->fecha_nacimiento)->age : '';
        if (is_null($formulario) && Schema::hasTable($this->tablaFormulario033())) {
            $formulario = $this->consultaFormulario033()->where('hcid', $historia->hcid)->orderBy('version', 'desc')->first();
        }
        $payload = (! is_null($formulario) && $formulario->snapshot_json) ? json_decode($formulario->snapshot_json, true) : [];
        if (! is_array($payload)) {
            $payload = [];
        }
        $idEspecialidad = (! is_null($agenda) && ! is_null($agenda->espid)) ? (int) $agenda->espid : 3;
        $cie10 = $this->catalogoCie10Odontologia($idEspecialidad);
        $procedimientosCatalogo = Procedimiento::where('estado', 1)
            ->where('id_especialidad', $idEspecialidad)
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $doctoresCatalogo = User::where('id_tipo_usuario', 3)
            ->where('estado', 1)
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre1')
            ->get(['id', 'nombre1', 'nombre2', 'apellido1', 'apellido2']);

        return view('odontologia.formulario033', ['historia' => $historia, 'paciente' => $paciente, 'agenda' => $agenda, 'doctor' => $doctor, 'seguro' => $seguro, 'sala' => $sala, 'hospital' => $hospital, 'procedimiento' => $procedimiento, 'empresa' => $empresa, 'edad' => $edad, 'denticion' => $this->denticionPorEdad($edad), 'formulario' => $formulario, 'payload' => $payload, 'payloadJson' => $this->jsonEncode($payload), 'cie10' => $cie10, 'procedimientosCatalogo' => $procedimientosCatalogo, 'doctoresCatalogo' => $doctoresCatalogo, 'idEspecialidad' => $idEspecialidad, 'imprimir' => $imprimir, 'embebido' => $embebido, 'contextoFormulario' => $contextoFormulario]);
    }

    protected function pdf(Historiaclinica $historia, OdontFormulario033 $formulario, $contextoFormulario = [])
    {
        $contextoFormulario = is_array($contextoFormulario) ? $contextoFormulario : [];
        $modoPeriodontal = ! empty($contextoFormulario['modo_periodontal']);
        $periodontogramaSvgData = isset($contextoFormulario['periodontograma_svg_data']) ? $contextoFormulario['periodontograma_svg_data'] : '';
        $paciente = Paciente::find($historia->id_paciente);
        $agenda = Agenda::find($historia->id_agenda);
        $doctor = User::find($historia->id_doctor1);
        $seguro = (! is_null($agenda) && ! is_null($agenda->id_seguro)) ? Seguro::find($agenda->id_seguro) : (! is_null($historia->id_seguro) ? Seguro::find($historia->id_seguro) : null);
        $sala = (! is_null($agenda) && ! is_null($agenda->id_sala)) ? Sala::find($agenda->id_sala) : null;
        $hospital = (! is_null($sala)) ? $sala->hospital : null;
        $procedimiento = (! is_null($agenda) && ! is_null($agenda->id_procedimiento)) ? Procedimiento::find($agenda->id_procedimiento) : null;
        $empresa = (! is_null($agenda) && ! is_null($agenda->id_empresa)) ? Empresa::find($agenda->id_empresa) : Empresa::where('prioridad', 1)->first();
        $edad = (! is_null($paciente) && $paciente->fecha_nacimiento) ? Carbon::parse($paciente->fecha_nacimiento)->age : '';
        $payload = $formulario->snapshot_json ? json_decode($formulario->snapshot_json, true) : [];
        if (! is_array($payload)) {
            $payload = [];
        }
        $denticion = $this->denticionPorEdad($edad);
        $odontogramaPiezas = $this->arr($this->arr($payload, 'odontograma'), 'piezas');
        $anatomiasDentales = $this->cargarAnatomiasDentales();
        $odontogramaSvg = $this->odontogramaSvgAnatomico($odontogramaPiezas, $denticion, $anatomiasDentales);
        $odontogramaSvgData = $odontogramaSvg != '' ? 'data:image/svg+xml;base64,'.base64_encode($odontogramaSvg) : '';
        $odontogramaImg = $odontogramaSvgData == '' ? $this->odontogramaPng($odontogramaPiezas, $denticion) : '';
        $view = view('odontologia.formulario033_pdf', [
            'historia' => $historia,
            'paciente' => $paciente,
            'agenda' => $agenda,
            'doctor' => $doctor,
            'seguro' => $seguro,
            'sala' => $sala,
            'hospital' => $hospital,
            'procedimiento' => $procedimiento,
            'empresa' => $empresa,
            'edad' => $edad,
            'denticion' => $denticion,
            'formulario' => $formulario,
            'payload' => $payload,
            'odontogramaSvgData' => $odontogramaSvgData,
            'odontogramaImg' => $odontogramaImg,
            'modoPeriodontal' => $modoPeriodontal,
            'periodontogramaSvgData' => $periodontogramaSvgData,
        ])->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'portrait');
        $nombreModulo = $modoPeriodontal ? 'periodontologia' : 'odontologia';
        $respuesta = $pdf->stream('formulario_033_'.$nombreModulo.'_'.$formulario->id.'.pdf', ['Attachment' => false]);
        $respuesta->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $respuesta->headers->set('Pragma', 'no-cache');

        return $respuesta;
    }

    private function cargarAnatomiasDentales()
    {
        $path = public_path('odontologia/data/anatomias_dentales.json');
        if (! is_file($path) || ! is_readable($path)) {
            return [];
        }

        $data = json_decode(file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }

    private function odontogramaSvgAnatomico($piezas, $denticion, $anatomias)
    {
        if (! is_array($anatomias) || count($anatomias) == 0) {
            return '';
        }

        $temporal = $denticion == 'temporal';
        $superiores = $temporal
            ? ['55', '54', '53', '52', '51', '61', '62', '63', '64', '65']
            : ['18', '17', '16', '15', '14', '13', '12', '11', '21', '22', '23', '24', '25', '26', '27', '28'];
        $inferiores = $temporal
            ? ['85', '84', '83', '82', '81', '71', '72', '73', '74', '75']
            : ['48', '47', '46', '45', '44', '43', '42', '41', '31', '32', '33', '34', '35', '36', '37', '38'];
        $inicioX = $temporal ? 185 : 30;
        $ySuperior = $temporal ? 118 : 36;
        $yInferior = $temporal ? 206 : 286;
        $offsetSuperior = $temporal ? 13 : 95;
        $offsetInferior = $temporal ? 45 : -35;
        $xDerecho = $temporal ? 328 : 264;
        $xIzquierdo = $temporal ? 633 : 752;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1040" height="440" viewBox="0 0 1040 440" preserveAspectRatio="xMidYMid meet">';
        $svg .= '<rect x="0" y="0" width="1040" height="440" fill="#ffffff"/>';
        $svg .= '<line x1="520" y1="25" x2="520" y2="414" stroke="#dbe5ec" stroke-width="1"/>';
        $svg .= '<line x1="18" y1="211" x2="1022" y2="211" stroke="#dbe5ec" stroke-width="1"/>';
        $svg .= $this->textoSvgAnatomico($xDerecho, 18, 'SUPERIOR DERECHO', 11, '#315f8a', 'bold');
        $svg .= $this->textoSvgAnatomico($xIzquierdo, 18, 'SUPERIOR IZQUIERDO', 11, '#315f8a', 'bold');
        $svg .= $this->textoSvgAnatomico($xDerecho, 429, 'INFERIOR DERECHO', 11, '#315f8a', 'bold');
        $svg .= $this->textoSvgAnatomico($xIzquierdo, 429, 'INFERIOR IZQUIERDO', 11, '#315f8a', 'bold');
        $svg .= $this->odontogramaFilaSvgAnatomico($superiores, $inicioX, $ySuperior, $offsetSuperior, $piezas, $anatomias, false);
        $svg .= $this->odontogramaFilaSvgAnatomico($inferiores, $inicioX, $yInferior, $offsetInferior, $piezas, $anatomias, true);
        $svg .= '</svg>';

        return $svg;
    }

    private function odontogramaFilaSvgAnatomico($dientes, $inicioX, $y, $offset, $piezas, $anatomias, $inferior)
    {
        $svg = '';
        foreach ($dientes as $indice => $fdi) {
            $pieza = isset($piezas[$fdi]) && is_array($piezas[$fdi]) ? $piezas[$fdi] : [];
            $svg .= $this->piezaSvgAnatomica($fdi, $inicioX + ($indice * 61), $y, $offset, $pieza, $anatomias, $inferior);
        }

        return $svg;
    }

    private function piezaSvgAnatomica($fdi, $x, $y, $offset, $pieza, $anatomias, $inferior)
    {
        $tipo = $this->tipoDienteSvgAnatomico($fdi);
        if (! isset($anatomias[$tipo]) || ! is_array($anatomias[$tipo])) {
            return '';
        }

        $anatomia = $anatomias[$tipo];
        $escala = isset($anatomia['escala']) && is_array($anatomia['escala']) ? $anatomia['escala'] : [];
        $familia = $this->familiaDienteSvgAnatomico($fdi);
        $cx = $x + 21;
        $cy = $y + 21;
        $geometria = $this->geometriaSuperficiesSvgAnatomico($familia, $cx, $cy);
        $esIzquierdo = in_array((int) substr((string) $fdi, 0, 1), [2, 3, 6, 7]);
        $espejo = $esIzquierdo ? -1 : 1;
        $general = isset($escala['general']) ? (float) $escala['general'] : 1;
        $coronaX = isset($escala['coronaX']) ? (float) $escala['coronaX'] : 1;
        $raizX = isset($escala['raizX']) ? (float) $escala['raizX'] : 1;
        $anclaSuperior = isset($escala['anclaSuperior']) ? (float) $escala['anclaSuperior'] : 0;
        $anclaInferior = isset($escala['anclaInferior']) ? (float) $escala['anclaInferior'] : 0;
        $destinoY = $inferior ? $cy + $geometria['mitad_alto'] + 8 : $cy - $geometria['mitad_alto'] - 8;
        $anclaY = $inferior ? $anclaSuperior : $anclaInferior;
        $superficies = $this->arr($pieza, 'superficies');
        $laterales = $esIzquierdo
            ? ['M' => $geometria['izquierda'], 'D' => $geometria['derecha']]
            : ['M' => $geometria['derecha'], 'D' => $geometria['izquierda']];
        $mapa = ['V' => $geometria['arriba'], 'L' => $geometria['abajo'], 'M' => $laterales['M'], 'D' => $laterales['D']];

        $rootShadow = '';
        $crownShadow = '';
        if (isset($anatomia['detailPaths']) && is_array($anatomia['detailPaths'])) {
            foreach ($anatomia['detailPaths'] as $detail) {
                if (! is_array($detail)) {
                    continue;
                }
                if ($this->val($detail, 'className') == 'root-shadow') {
                    $rootShadow = $this->val($detail, 'path');
                } elseif ($this->val($detail, 'className') == 'enamel-shadow') {
                    $crownShadow = $this->val($detail, 'path');
                }
            }
        }

        $svg = '<g data-fdi="'.$fdi.'" transform="translate(0 '.$this->numeroSvg($offset).')">';
        $svg .= '<g transform="translate('.$this->numeroSvg($cx).' '.$this->numeroSvg($destinoY).') scale('.$this->numeroSvg($espejo * $general).' '.$this->numeroSvg($general).') translate(-90 -'.$this->numeroSvg($anclaY).')">';
        $svg .= '<g transform="translate(90 0) scale('.$this->numeroSvg($raizX).' 1) translate(-90 0)">';
        // Dompdf convierte los paint servers url(#gradient) del SVG embebido a negro.
        // Los rellenos solidos conservan la anatomia y se renderizan igual en PDF y navegador.
        $svg .= '<path d="'.$this->atributoSvg($this->val($anatomia, 'rootPath')).'" fill="#fff8e8" stroke="#a68d68" stroke-width="1.45" stroke-linejoin="miter"/>';
        if ($rootShadow != '') {
            $svg .= '<path d="'.$this->atributoSvg($rootShadow).'" fill="#eadfc9" stroke="none"/>';
        }
        $svg .= '</g>';
        $svg .= '<g transform="translate(90 0) scale('.$this->numeroSvg($coronaX).' 1) translate(-90 0)">';
        $svg .= '<path d="'.$this->atributoSvg($this->val($anatomia, 'crownPath')).'" fill="#f9fbfc" stroke="#6f8799" stroke-width="1.55" stroke-linejoin="round"/>';
        if ($crownShadow != '') {
            $svg .= '<path d="'.$this->atributoSvg($crownShadow).'" fill="#e8eef2" stroke="none"/>';
        }
        $svg .= '</g></g>';
        $svg .= $geometria['base'];

        foreach (['V', 'L', 'M', 'D'] as $codigo) {
            $dato = isset($superficies[$codigo]) ? $superficies[$codigo] : null;
            $svg .= '<polygon points="'.$this->puntosSvg($mapa[$codigo]).'" fill="'.$this->superficieColor($dato).'" stroke="'.$this->bordeSuperficieSvg($dato).'" stroke-width="'.$this->anchoBordeSuperficieSvg($dato).'" stroke-linejoin="round"/>';
        }

        $datoO = isset($superficies['O']) ? $superficies['O'] : null;
        $o = $geometria['oclusal'];
        $svg .= '<rect x="'.$this->numeroSvg($o['x']).'" y="'.$this->numeroSvg($o['y']).'" width="'.$this->numeroSvg($o['width']).'" height="'.$this->numeroSvg($o['height']).'" rx="'.$this->numeroSvg($o['rx']).'" ry="'.$this->numeroSvg($o['ry']).'" fill="'.$this->superficieColor($datoO).'" stroke="'.$this->bordeSuperficieSvg($datoO).'" stroke-width="'.$this->anchoBordeSuperficieSvg($datoO).'"'.($o['transform'] != '' ? ' transform="'.$o['transform'].'"' : '').'/>';
        $svg .= $geometria['anillo'];
        foreach ($geometria['detalles'] as $detalle) {
            $svg .= '<path d="'.$detalle.'" fill="none" stroke="#aebdca" stroke-width="0.8" stroke-linecap="round"/>';
        }

        $svg .= $this->marcaSvgAnatomica($this->val($pieza, 'marca'), $cx, $cy, $this->val($pieza, 'color'));
        $labelY = $inferior ? $cy - $geometria['mitad_alto'] - 11 : $cy + $geometria['mitad_alto'] + 17;
        $svg .= $this->textoSvgAnatomico($cx, $labelY, $fdi, 11, '#20364b', 'bold');
        if (((int) $fdi) < 50) {
            $movilidad = $this->val($pieza, 'movilidad', '');
            $recesion = $this->val($pieza, 'recesion', '');
            if ($movilidad !== '' || $recesion !== '') {
                $mrY = $inferior ? $labelY - 13 : $labelY + 13;
                $svg .= $this->textoSvgAnatomico($cx, $mrY, 'M'.($movilidad !== '' ? $movilidad : '-').' R'.($recesion !== '' ? $recesion : '-'), 7, '#52697d', 'normal');
            }
        }
        $svg .= '</g>';

        return $svg;
    }

    private function tipoDienteSvgAnatomico($fdi)
    {
        $numero = (int) $fdi;
        $cuadrante = (int) substr((string) $fdi, 0, 1);
        $posicion = (int) substr((string) $fdi, 1, 1);
        $temporal = $numero >= 50;
        $arco = in_array($cuadrante, [3, 4, 7, 8]) ? 'lower' : 'upper';
        $prefijo = $temporal ? 'primary-' : '';

        if ($posicion == 1) {
            $nombre = 'central-incisor';
        } elseif ($posicion == 2) {
            $nombre = 'lateral-incisor';
        } elseif ($posicion == 3) {
            $nombre = 'canine';
        } elseif ($posicion == 4) {
            $nombre = $temporal ? 'first-molar' : 'first-premolar';
        } elseif ($posicion == 5) {
            $nombre = $temporal ? 'second-molar' : 'second-premolar';
        } elseif ($posicion == 6) {
            $nombre = 'first-molar';
        } elseif ($posicion == 7) {
            $nombre = 'second-molar';
        } else {
            $nombre = 'third-molar';
        }

        return $prefijo.$arco.'-'.$nombre;
    }

    private function familiaDienteSvgAnatomico($fdi)
    {
        $posicion = (int) substr((string) $fdi, 1, 1);
        if ($posicion <= 2) {
            return 'incisor';
        }
        if ($posicion == 3) {
            return 'canine';
        }
        if (((int) $fdi) < 50 && in_array($posicion, [4, 5])) {
            return 'premolar';
        }

        return 'molar';
    }

    private function geometriaSuperficiesSvgAnatomico($familia, $cx, $cy)
    {
        $geometria = ['mitad_alto' => 18, 'detalles' => []];
        if ($familia == 'incisor') {
            $geometria['mitad_alto'] = 15;
            $geometria['arriba'] = [[$cx - 12, $cy - 15], [$cx + 12, $cy - 15], [$cx + 7, $cy - 6], [$cx - 7, $cy - 6]];
            $geometria['derecha'] = [[$cx + 12, $cy - 15], [$cx + 18, $cy - 9], [$cx + 18, $cy + 9], [$cx + 12, $cy + 15], [$cx + 7, $cy + 6], [$cx + 7, $cy - 6]];
            $geometria['abajo'] = [[$cx + 12, $cy + 15], [$cx - 12, $cy + 15], [$cx - 7, $cy + 6], [$cx + 7, $cy + 6]];
            $geometria['izquierda'] = [[$cx - 12, $cy + 15], [$cx - 18, $cy + 9], [$cx - 18, $cy - 9], [$cx - 12, $cy - 15], [$cx - 7, $cy - 6], [$cx - 7, $cy + 6]];
            $geometria['oclusal'] = ['x' => $cx - 7, 'y' => $cy - 6, 'width' => 14, 'height' => 12, 'rx' => 4, 'ry' => 4, 'transform' => ''];
            $geometria['base'] = '<rect x="'.($cx - 18).'" y="'.($cy - 15).'" width="36" height="30" rx="9" fill="#fbfdfe" stroke="#d7e0e7" stroke-width="0.8"/>';
            $geometria['anillo'] = '<rect x="'.($cx - 18).'" y="'.($cy - 15).'" width="36" height="30" rx="9" fill="none" stroke="#71879a" stroke-width="1.15"/>';
        } elseif ($familia == 'canine') {
            $geometria['mitad_alto'] = 21;
            $geometria['arriba'] = [[$cx - 12, $cy - 12], [$cx, $cy - 21], [$cx + 12, $cy - 12], [$cx + 6, $cy - 6], [$cx - 6, $cy - 6]];
            $geometria['derecha'] = [[$cx + 12, $cy - 12], [$cx + 21, $cy], [$cx + 12, $cy + 12], [$cx + 6, $cy + 6], [$cx + 6, $cy - 6]];
            $geometria['abajo'] = [[$cx + 12, $cy + 12], [$cx, $cy + 21], [$cx - 12, $cy + 12], [$cx - 6, $cy + 6], [$cx + 6, $cy + 6]];
            $geometria['izquierda'] = [[$cx - 12, $cy + 12], [$cx - 21, $cy], [$cx - 12, $cy - 12], [$cx - 6, $cy - 6], [$cx - 6, $cy + 6]];
            $geometria['oclusal'] = ['x' => $cx - 6.5, 'y' => $cy - 6.5, 'width' => 13, 'height' => 13, 'rx' => 3, 'ry' => 3, 'transform' => 'rotate(45 '.$cx.' '.$cy.')'];
            $rombo = 'M '.$cx.' '.($cy - 22).' Q '.($cx + 16).' '.($cy - 16).' '.($cx + 22).' '.$cy.' Q '.($cx + 16).' '.($cy + 16).' '.$cx.' '.($cy + 22).' Q '.($cx - 16).' '.($cy + 16).' '.($cx - 22).' '.$cy.' Q '.($cx - 16).' '.($cy - 16).' '.$cx.' '.($cy - 22).' Z';
            $geometria['base'] = '<path d="'.$rombo.'" fill="#fbfdfe" stroke="#d7e0e7" stroke-width="0.8"/>';
            $geometria['anillo'] = '<path d="M '.$cx.' '.($cy - 21).' L '.($cx + 21).' '.$cy.' L '.$cx.' '.($cy + 21).' L '.($cx - 21).' '.$cy.' Z" fill="none" stroke="#71879a" stroke-width="1.15"/>';
        } elseif ($familia == 'premolar') {
            $geometria['arriba'] = [[$cx - 15, $cy - 13], [$cx, $cy - 18], [$cx + 15, $cy - 13], [$cx + 7, $cy - 6], [$cx - 7, $cy - 6]];
            $geometria['derecha'] = [[$cx + 15, $cy - 13], [$cx + 21, $cy], [$cx + 15, $cy + 13], [$cx + 7, $cy + 6], [$cx + 7, $cy - 6]];
            $geometria['abajo'] = [[$cx + 15, $cy + 13], [$cx, $cy + 18], [$cx - 15, $cy + 13], [$cx - 7, $cy + 6], [$cx + 7, $cy + 6]];
            $geometria['izquierda'] = [[$cx - 15, $cy + 13], [$cx - 21, $cy], [$cx - 15, $cy - 13], [$cx - 7, $cy - 6], [$cx - 7, $cy + 6]];
            $geometria['oclusal'] = ['x' => $cx - 8, 'y' => $cy - 6, 'width' => 16, 'height' => 12, 'rx' => 6, 'ry' => 6, 'transform' => ''];
            $geometria['base'] = '<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="21.5" ry="18.5" fill="#fbfdfe" stroke="#d7e0e7" stroke-width="0.8"/>';
            $geometria['anillo'] = '<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="20.5" ry="17.5" fill="none" stroke="#71879a" stroke-width="1.15"/>';
            $geometria['detalles'] = ['M '.$cx.' '.($cy - 15).' V '.($cy - 8), 'M '.$cx.' '.($cy + 8).' V '.($cy + 15)];
        } else {
            $geometria['arriba'] = [[$cx - 17, $cy - 18], [$cx + 17, $cy - 18], [$cx + 23, $cy - 11], [$cx + 8, $cy - 7], [$cx - 8, $cy - 7], [$cx - 23, $cy - 11]];
            $geometria['derecha'] = [[$cx + 23, $cy - 11], [$cx + 23, $cy + 11], [$cx + 17, $cy + 18], [$cx + 8, $cy + 7], [$cx + 8, $cy - 7]];
            $geometria['abajo'] = [[$cx + 17, $cy + 18], [$cx - 17, $cy + 18], [$cx - 23, $cy + 11], [$cx - 8, $cy + 7], [$cx + 8, $cy + 7], [$cx + 23, $cy + 11]];
            $geometria['izquierda'] = [[$cx - 23, $cy + 11], [$cx - 23, $cy - 11], [$cx - 17, $cy - 18], [$cx - 8, $cy - 7], [$cx - 8, $cy + 7]];
            $geometria['oclusal'] = ['x' => $cx - 8, 'y' => $cy - 7, 'width' => 16, 'height' => 14, 'rx' => 4, 'ry' => 4, 'transform' => ''];
            $geometria['base'] = '<rect x="'.($cx - 23).'" y="'.($cy - 18).'" width="46" height="36" rx="9" fill="#fbfdfe" stroke="#d7e0e7" stroke-width="0.8"/>';
            $geometria['anillo'] = '<rect x="'.($cx - 22).'" y="'.($cy - 17).'" width="44" height="34" rx="8" fill="none" stroke="#71879a" stroke-width="1.15"/>';
            $geometria['detalles'] = ['M '.($cx - 16).' '.$cy.' H '.($cx - 10), 'M '.($cx + 10).' '.$cy.' H '.($cx + 16)];
        }

        return $geometria;
    }

    private function marcaSvgAnatomica($marca, $cx, $cy, $colorDato)
    {
        if ($marca == '') {
            return '';
        }
        $color = $colorDato == 'rojo' ? '#c62828' : ($colorDato == 'azul' ? '#1d4ed8' : '#687684');
        $stroke = ' stroke="'.$color.'" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"';
        if (in_array($marca, ['extraccion_indicada', 'perdida_caries'])) {
            return '<path d="M '.($cx - 7).' '.($cy - 7).' L '.($cx + 7).' '.($cy + 7).' M '.($cx + 7).' '.($cy - 7).' L '.($cx - 7).' '.($cy + 7).'"'.$stroke.'/>';
        }
        if ($marca == 'perdida_otra') {
            return '<circle cx="'.$cx.'" cy="'.$cy.'" r="9"'.$stroke.'/><path d="M '.($cx - 5).' '.($cy - 5).' L '.($cx + 5).' '.($cy + 5).' M '.($cx + 5).' '.($cy - 5).' L '.($cx - 5).' '.($cy + 5).'"'.$stroke.'/>';
        }
        if (in_array($marca, ['endodoncia_realizar', 'endodoncia_realizada'])) {
            return '<path d="M '.$cx.' '.($cy - 9).' L '.($cx + 8).' '.($cy + 7).' L '.($cx - 8).' '.($cy + 7).' Z"'.$stroke.'/>';
        }
        if (in_array($marca, ['corona_indicada', 'corona_realizada'])) {
            return '<circle cx="'.$cx.'" cy="'.$cy.'" r="9"'.$stroke.'/>';
        }
        if ($marca == 'protesis_fija') {
            return '<path d="M '.($cx - 14).' '.($cy - 3).' H '.($cx + 14).' M '.$cx.' '.($cy - 3).' V '.($cy + 8).'"'.$stroke.'/>';
        }
        if (in_array($marca, ['protesis_removible_indicada', 'protesis_removible_realizada'])) {
            return '<path d="M '.($cx - 15).' '.$cy.' H '.($cx + 15).'" stroke="'.$color.'" stroke-width="2.2" stroke-dasharray="5 3" fill="none"/>';
        }
        if (in_array($marca, ['protesis_total_indicada', 'protesis_total_realizada'])) {
            return '<path d="M '.($cx - 14).' '.($cy - 4).' H '.($cx + 14).' M '.($cx - 14).' '.($cy + 4).' H '.($cx + 14).'"'.$stroke.'/>';
        }
        if ($marca == 'ausente') {
            return $this->textoSvgAnatomico($cx, $cy + 5, 'A', 14, $color, 'bold');
        }

        return '';
    }

    private function puntosSvg($puntos)
    {
        $salida = [];
        foreach ($puntos as $punto) {
            $salida[] = $this->numeroSvg($punto[0]).','.$this->numeroSvg($punto[1]);
        }

        return implode(' ', $salida);
    }

    private function numeroSvg($numero)
    {
        return rtrim(rtrim(number_format((float) $numero, 4, '.', ''), '0'), '.');
    }

    private function atributoSvg($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    private function textoSvgAnatomico($x, $y, $texto, $tamano, $color, $peso)
    {
        return '<text x="'.$this->numeroSvg($x).'" y="'.$this->numeroSvg($y).'" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="'.$this->numeroSvg($tamano).'" font-weight="'.$peso.'" fill="'.$color.'">'.$this->atributoSvg($texto).'</text>';
    }

    private function bordeSuperficieSvg($data)
    {
        $condicion = is_array($data) ? $this->val($data, 'condicion') : '';
        if ($condicion == 'sellante_necesario') {
            return '#c62828';
        }
        if ($condicion == 'sellante_realizado') {
            return '#1d4ed8';
        }

        return '#71879a';
    }

    private function anchoBordeSuperficieSvg($data)
    {
        $condicion = is_array($data) ? $this->val($data, 'condicion') : '';

        return in_array($condicion, ['sellante_necesario', 'sellante_realizado']) ? '1.8' : '1.05';
    }

    private function odontogramaPng($piezas, $denticion = 'permanente')
    {
        if (! function_exists('imagecreatetruecolor')) {
            return '';
        }
        if (! in_array($denticion, ['temporal', 'permanente'])) {
            $denticion = 'permanente';
        }

        $w = 1040;
        $h = 440;
        $img = imagecreatetruecolor($w, $h);
        if (function_exists('imageantialias')) {
            imageantialias($img, true);
        }
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w, $h, $white);
        $dark = imagecolorallocate($img, 55, 65, 75);
        $guide = imagecolorallocate($img, 220, 229, 236);
        imageline($img, 520, 24, 520, 414, $guide);
        imageline($img, 18, 211, 1022, 211, $guide);

        $permanentesSuperiores = ['18', '17', '16', '15', '14', '13', '12', '11', '21', '22', '23', '24', '25', '26', '27', '28'];
        $permanentesInferiores = ['48', '47', '46', '45', '44', '43', '42', '41', '31', '32', '33', '34', '35', '36', '37', '38'];
        $temporalesSuperiores = ['55', '54', '53', '52', '51', '61', '62', '63', '64', '65'];
        $temporalesInferiores = ['85', '84', '83', '82', '81', '71', '72', '73', '74', '75'];

        if ($denticion == 'temporal') {
            $this->odontogramaEtiquetasPng($img, 4, 426, $dark, 328, 633);
            $this->odontogramaFilaPng($img, $temporalesSuperiores, 185, 131, $piezas, false);
            $this->odontogramaFilaPng($img, $temporalesInferiores, 185, 251, $piezas, true);
        } else {
            $this->odontogramaEtiquetasPng($img, 4, 426, $dark, 264, 752);
            $this->odontogramaFilaPng($img, $permanentesSuperiores, 30, 131, $piezas, false);
            $this->odontogramaFilaPng($img, $permanentesInferiores, 30, 251, $piezas, true);
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,'.base64_encode($png);
    }

    private function odontogramaEtiquetasPng($img, $ySuperior, $yInferior, $color, $xDerecho = 264, $xIzquierdo = 752)
    {
        $this->pngTextCentered($img, $xDerecho, $ySuperior, 'SUPERIOR DERECHO', 2, $color);
        $this->pngTextCentered($img, $xIzquierdo, $ySuperior, 'SUPERIOR IZQUIERDO', 2, $color);
        $this->pngTextCentered($img, $xDerecho, $yInferior, 'INFERIOR DERECHO', 2, $color);
        $this->pngTextCentered($img, $xIzquierdo, $yInferior, 'INFERIOR IZQUIERDO', 2, $color);
    }

    private function odontogramaFilaPng($img, $dientes, $x, $y, $piezas, $inferior)
    {
        foreach ($dientes as $i => $fdi) {
            $this->piezaPng($img, $fdi, $x + ($i * 61), $y, isset($piezas[$fdi]) ? $piezas[$fdi] : [], $inferior);
        }
    }

    private function piezaPng($img, $fdi, $x, $y, $pieza, $inferior)
    {
        $sup = $this->arr($pieza, 'superficies');
        $gray = imagecolorallocate($img, 150, 160, 170);
        $surfaceLine = imagecolorallocate($img, 100, 123, 143);
        $dark = imagecolorallocate($img, 55, 65, 75);
        $red = imagecolorallocate($img, 198, 40, 40);
        $blue = imagecolorallocate($img, 29, 78, 216);
        $neutral = imagecolorallocate($img, 104, 118, 132);
        $cx = $x + 21;
        $cy = $y + 21;
        $fills = [
            'V' => $this->pngColor($img, isset($sup['V']) ? $sup['V'] : null),
            'L' => $this->pngColor($img, isset($sup['L']) ? $sup['L'] : null),
            'M' => $this->pngColor($img, isset($sup['M']) ? $sup['M'] : null),
            'D' => $this->pngColor($img, isset($sup['D']) ? $sup['D'] : null),
            'O' => $this->pngColor($img, isset($sup['O']) ? $sup['O'] : null),
        ];

        $this->anatomiaPng($img, $fdi, $cx, $y, $inferior);
        imagefilledarc($img, $cx, $cy, 40, 40, 225, 315, $fills['V'], IMG_ARC_PIE);
        imagefilledarc($img, $cx, $cy, 40, 40, 315, 405, $fills['D'], IMG_ARC_PIE);
        imagefilledarc($img, $cx, $cy, 40, 40, 45, 135, $fills['L'], IMG_ARC_PIE);
        imagefilledarc($img, $cx, $cy, 40, 40, 135, 225, $fills['M'], IMG_ARC_PIE);
        imageellipse($img, $cx, $cy, 40, 40, $surfaceLine);
        imageline($img, $cx - 6, $cy - 6, $cx - 14, $cy - 14, $surfaceLine);
        imageline($img, $cx + 6, $cy - 6, $cx + 14, $cy - 14, $surfaceLine);
        imageline($img, $cx + 6, $cy + 6, $cx + 14, $cy + 14, $surfaceLine);
        imageline($img, $cx - 6, $cy + 6, $cx - 14, $cy + 14, $surfaceLine);
        imagefilledellipse($img, $cx, $cy, 14, 14, $fills['O']);
        imageellipse($img, $cx, $cy, 14, 14, $surfaceLine);
        $marca = $this->val($pieza, 'marca', '');
        $simbolo = $this->marcaSimboloPdf($marca);
        $colorDato = $this->val($pieza, 'color', '');
        $overlayColor = $colorDato == 'rojo' ? $red : ($colorDato == 'azul' ? $blue : $neutral);
        if ($simbolo != '') {
            $this->pngTextCentered($img, $cx, $cy - 7, $simbolo, 5, $overlayColor);
        }
        $this->pngTextCentered($img, $cx, $inferior ? $y - 14 : $y + 55, $fdi, 2, $dark);
        if (((int) $fdi) < 50) {
            $mov = $this->val($pieza, 'movilidad', '');
            $rec = $this->val($pieza, 'recesion', '');
            if ($mov !== '' || $rec !== '') {
                $boxY = $inferior ? $y - 28 : $y + 44;
                imagerectangle($img, $x + 2, $boxY, $x + 20, $boxY + 10, $gray);
                imagerectangle($img, $x + 22, $boxY, $x + 40, $boxY + 10, $gray);
                if ($mov !== '') {
                    $this->pngTextCentered($img, $x + 11, $boxY, (string) $mov, 1, $dark);
                }
                if ($rec !== '') {
                    $this->pngTextCentered($img, $x + 31, $boxY, (string) $rec, 1, $dark);
                }
            }
        }
    }

    private function anatomiaPng($img, $fdi, $cx, $y, $inferior)
    {
        $config = $this->configuracionAnatomiaPng($fdi, $inferior);
        $escala = $config['temporal'] ? 0.84 : 1;
        $escalaX = $escala * $config['escala_x'];
        $origenY = $inferior ? $y + 46 : $y - 4;
        $factorY = $inferior ? 1 : -1;
        $esmalte = imagecolorallocate($img, 248, 251, 252);
        $raiz = imagecolorallocate($img, 249, 240, 217);
        $borde = imagecolorallocate($img, 113, 135, 150);
        $detalle = imagecolorallocate($img, 182, 196, 204);

        $raices = $config['raices'];
        $separacion = $config['separacion'];
        $offsets = $raices == 3 ? [-$separacion, 0, $separacion] : ($raices == 2 ? [-$separacion, $separacion] : [0]);
        foreach ($offsets as $indice => $offset) {
            $largo = $config['largo_raiz'];
            if ($raices == 3 && $indice == 1) {
                $largo += 7;
            }
            $anchoBase = $raices == 1 ? max(5, $config['ancho_corona'] * 0.24) : max(4, $config['ancho_corona'] * 0.12);
            $punta = $offset * ($raices > 1 ? 1.3 : 0);
            $baseY = $config['alto_corona'] - 4;
            $medioY = $baseY + ($largo * 0.55);
            $finY = $baseY + $largo;
            $puntosRaiz = [
                [$offset - $anchoBase, $baseY],
                [$offset - ($anchoBase * 0.85), $medioY],
                [$punta - 1.5, $finY - 5],
                [$punta, $finY],
                [$punta + 1.5, $finY - 5],
                [$offset + ($anchoBase * 0.85), $medioY],
                [$offset + $anchoBase, $baseY],
            ];
            $this->poligonoAnatomiaPng($img, $puntosRaiz, $cx, $origenY, $escalaX, $escala, $factorY, $raiz, $borde);
            $inicioDetalle = $this->puntoAnatomiaPng($cx, $origenY, $offset, $baseY + 4, $escalaX, $escala, $factorY);
            $finDetalle = $this->puntoAnatomiaPng($cx, $origenY, $punta, $finY - 7, $escalaX, $escala, $factorY);
            imageline($img, $inicioDetalle[0], $inicioDetalle[1], $finDetalle[0], $finDetalle[1], $detalle);
        }

        $mitad = $config['ancho_corona'] / 2;
        $alto = $config['alto_corona'];
        if ($config['tipo'] == 'incisivo') {
            $puntosCorona = [[-$mitad, 1], [-$mitad, 9], [-$mitad * 0.82, $alto * 0.72], [-$mitad * 0.48, $alto], [$mitad * 0.48, $alto], [$mitad * 0.82, $alto * 0.72], [$mitad, 9], [$mitad, 1], [$mitad * 0.45, 0], [-$mitad * 0.45, 0]];
        } elseif ($config['tipo'] == 'canino') {
            $puntosCorona = [[-$mitad, 7], [-$mitad * 0.62, 4], [0, -5], [$mitad * 0.62, 4], [$mitad, 7], [$mitad * 0.9, $alto * 0.72], [$mitad * 0.45, $alto], [-$mitad * 0.45, $alto], [-$mitad * 0.9, $alto * 0.72]];
        } elseif ($config['tipo'] == 'premolar') {
            $puntosCorona = [[-$mitad, 7], [-$mitad * 0.55, 3], [-$mitad * 0.18, 0], [0, 5], [$mitad * 0.2, 1], [$mitad * 0.58, 3], [$mitad, 7], [$mitad * 0.9, $alto * 0.72], [$mitad * 0.48, $alto], [-$mitad * 0.48, $alto], [-$mitad * 0.9, $alto * 0.72]];
        } else {
            $puntosCorona = [[-$mitad, 8], [-$mitad * 0.78, 3], [-$mitad * 0.48, 6], [-$mitad * 0.22, 1], [0, 5], [$mitad * 0.23, 1], [$mitad * 0.5, 6], [$mitad * 0.8, 3], [$mitad, 8], [$mitad * 0.9, $alto * 0.72], [$mitad * 0.52, $alto], [-$mitad * 0.52, $alto], [-$mitad * 0.9, $alto * 0.72]];
        }
        $this->poligonoAnatomiaPng($img, $puntosCorona, $cx, $origenY, $escalaX, $escala, $factorY, $esmalte, $borde);

        $cuelloIzquierdo = $this->puntoAnatomiaPng($cx, $origenY, -$mitad * 0.45, $alto - 1, $escalaX, $escala, $factorY);
        $cuelloDerecho = $this->puntoAnatomiaPng($cx, $origenY, $mitad * 0.45, $alto - 1, $escalaX, $escala, $factorY);
        imageline($img, $cuelloIzquierdo[0], $cuelloIzquierdo[1], $cuelloDerecho[0], $cuelloDerecho[1], $detalle);
    }

    private function configuracionAnatomiaPng($fdi, $inferior)
    {
        $posicion = (int) substr((string) $fdi, 1, 1);
        $temporal = ((int) $fdi) >= 50;
        $config = ['temporal' => $temporal, 'tipo' => 'incisivo', 'ancho_corona' => 30, 'alto_corona' => 31, 'largo_raiz' => 70, 'raices' => 1, 'separacion' => 0, 'escala_x' => 1];

        if ($posicion == 1) {
            $config['ancho_corona'] = $inferior ? 20 : 30;
            $config['largo_raiz'] = $inferior ? 66 : 73;
            $config['escala_x'] = $inferior ? 0.9 : 1;
        } elseif ($posicion == 2) {
            $config['ancho_corona'] = $inferior ? 22 : 25;
            $config['largo_raiz'] = $inferior ? 69 : 70;
            $config['escala_x'] = 0.95;
        } elseif ($posicion == 3) {
            $config['tipo'] = 'canino';
            $config['ancho_corona'] = $inferior ? 25 : 28;
            $config['alto_corona'] = 33;
            $config['largo_raiz'] = 80;
        } elseif (! $temporal && ($posicion == 4 || $posicion == 5)) {
            $config['tipo'] = 'premolar';
            $config['ancho_corona'] = $posicion == 4 ? 34 : 32;
            $config['alto_corona'] = $posicion == 4 ? 33 : 32;
            $config['largo_raiz'] = $posicion == 4 ? 66 : 63;
            $config['raices'] = (! $inferior && $posicion == 4) ? 2 : 1;
            $config['separacion'] = $config['raices'] == 2 ? 6 : 0;
        } else {
            $config['tipo'] = 'molar';
            $config['ancho_corona'] = $posicion == 4 ? 35 : ($posicion == 5 ? 39 : ($posicion == 6 ? 42 : ($posicion == 7 ? 39 : 36)));
            $config['alto_corona'] = $posicion == 4 ? 31 : 34;
            $config['largo_raiz'] = $posicion == 4 ? 53 : ($posicion == 5 ? 58 : ($posicion == 6 ? 65 : ($posicion == 7 ? 61 : 55)));
            $config['raices'] = $inferior ? 2 : 3;
            $config['separacion'] = $inferior ? 9 : 8;
            $config['escala_x'] = $posicion == 8 ? 0.92 : 1;
        }

        if ($temporal) {
            $config['largo_raiz'] = max(48, $config['largo_raiz'] - 7);
            if ($posicion <= 2) {
                $config['ancho_corona'] += $inferior ? 1 : 0;
            }
        }

        return $config;
    }

    private function puntoAnatomiaPng($cx, $origenY, $x, $y, $escalaX, $escalaY, $factorY)
    {
        return [(int) round($cx + ($x * $escalaX)), (int) round($origenY + ($y * $escalaY * $factorY))];
    }

    private function poligonoAnatomiaPng($img, $puntos, $cx, $origenY, $escalaX, $escalaY, $factorY, $relleno, $borde)
    {
        $plano = [];
        foreach ($puntos as $punto) {
            $transformado = $this->puntoAnatomiaPng($cx, $origenY, $punto[0], $punto[1], $escalaX, $escalaY, $factorY);
            $plano[] = $transformado[0];
            $plano[] = $transformado[1];
        }
        imagefilledpolygon($img, $plano, count($plano) / 2, $relleno);
        imagepolygon($img, $plano, count($plano) / 2, $borde);
    }

    private function pngPolygon($img, $points, $fill, $line)
    {
        imagefilledpolygon($img, $points, count($points) / 2, $fill);
        imagepolygon($img, $points, count($points) / 2, $line);
    }

    private function pngColor($img, $data)
    {
        if (is_array($data) && isset($data['condicion'])) {
            if ($data['condicion'] == 'caries') {
                return imagecolorallocate($img, 198, 40, 40);
            }
            if ($data['condicion'] == 'obturado') {
                return imagecolorallocate($img, 29, 78, 216);
            }
            if ($data['condicion'] == 'sellante_necesario') {
                return imagecolorallocate($img, 255, 220, 220);
            }
            if ($data['condicion'] == 'sellante_realizado') {
                return imagecolorallocate($img, 220, 232, 255);
            }
        }

        return imagecolorallocate($img, 255, 255, 255);
    }

    private function pngText($img, $x, $y, $text, $font, $color)
    {
        imagestring($img, $font, (int) $x, (int) $y, $text, $color);
    }

    private function pngTextCentered($img, $cx, $y, $text, $font, $color)
    {
        $tw = imagefontwidth($font) * strlen($text);
        imagestring($img, $font, (int) ($cx - ($tw / 2)), (int) $y, $text, $color);
    }

    private function marcaSimboloPdf($marca)
    {
        $map = [
            'extraccion_indicada' => 'X',
            'perdida_caries' => 'X',
            'perdida_otra' => 'X',
            'endodoncia_realizar' => 'A',
            'endodoncia_realizada' => 'A',
            'corona_indicada' => 'O',
            'corona_realizada' => 'O',
            'protesis_fija' => '--',
            'protesis_removible_indicada' => '---',
            'protesis_removible_realizada' => '---',
            'protesis_total_indicada' => '=',
            'protesis_total_realizada' => '=',
            'ausente' => 'AUS',
        ];

        return isset($map[$marca]) ? $map[$marca] : '';
    }

    private function odontogramaSvg($piezas)
    {
        $permanentesSup = ['18', '17', '16', '15', '14', '13', '12', '11', '21', '22', '23', '24', '25', '26', '27', '28'];
        $temporalesSup = ['55', '54', '53', '52', '51', '61', '62', '63', '64', '65'];
        $temporalesInf = ['85', '84', '83', '82', '81', '71', '72', '73', '74', '75'];
        $permanentesInf = ['48', '47', '46', '45', '44', '43', '42', '41', '31', '32', '33', '34', '35', '36', '37', '38'];
        $svg = '<svg width="760" height="270" viewBox="0 0 1040 360" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<style>.s{stroke:#9aa7b2;stroke-width:1}.t{font-family:Arial;font-size:11px;font-weight:bold;fill:#37424d;text-anchor:middle}.o{font-family:Arial;font-size:20px;font-weight:bold;text-anchor:middle}</style>';
        $svg .= $this->odontogramaFilaSvg($permanentesSup, 30, 36, $piezas);
        $svg .= $this->odontogramaFilaSvg($temporalesSup, 185, 118, $piezas);
        $svg .= $this->odontogramaFilaSvg($temporalesInf, 185, 206, $piezas);
        $svg .= $this->odontogramaFilaSvg($permanentesInf, 30, 286, $piezas);
        $svg .= '</svg>';

        return $svg;
    }

    private function odontogramaFilaSvg($dientes, $x, $y, $piezas)
    {
        $html = '';
        foreach ($dientes as $i => $fdi) {
            $html .= $this->piezaSvg($fdi, $x + ($i * 61), $y, isset($piezas[$fdi]) ? $piezas[$fdi] : []);
        }

        return $html;
    }

    private function piezaSvg($fdi, $x, $y, $pieza)
    {
        $sup = $this->arr($pieza, 'superficies');
        $marca = $this->val($pieza, 'marca', '');
        $color = $this->val($pieza, 'color', '');
        $fill = [
            'V' => $this->superficieColor(isset($sup['V']) ? $sup['V'] : null),
            'L' => $this->superficieColor(isset($sup['L']) ? $sup['L'] : null),
            'M' => $this->superficieColor(isset($sup['M']) ? $sup['M'] : null),
            'D' => $this->superficieColor(isset($sup['D']) ? $sup['D'] : null),
            'O' => $this->superficieColor(isset($sup['O']) ? $sup['O'] : null),
        ];
        $w = 42;
        $h = 42;
        $cx = $x + 21;
        $cy = $y + 21;
        $overlay = $this->marcaSimbolo($marca);
        $overlayColor = $color == 'rojo' ? '#c62828' : ($color == 'azul' ? '#1d4ed8' : '#687684');
        $html = '<g>';
        $html .= '<polygon points="'.$x.','.$y.' '.($x + $w).','.$y.' '.($x + 31).','.($y + 12).' '.($x + 11).','.($y + 12).'" class="s" fill="'.$fill['V'].'"/>';
        $html .= '<polygon points="'.$x.','.($y + $h).' '.($x + $w).','.($y + $h).' '.($x + 31).','.($y + 30).' '.($x + 11).','.($y + 30).'" class="s" fill="'.$fill['L'].'"/>';
        $html .= '<polygon points="'.$x.','.$y.' '.($x + 11).','.($y + 12).' '.($x + 11).','.($y + 30).' '.$x.','.($y + $h).'" class="s" fill="'.$fill['M'].'"/>';
        $html .= '<polygon points="'.($x + $w).','.$y.' '.($x + 31).','.($y + 12).' '.($x + 31).','.($y + 30).' '.($x + $w).','.($y + $h).'" class="s" fill="'.$fill['D'].'"/>';
        $html .= '<rect x="'.($x + 12).'" y="'.($y + 13).'" width="18" height="16" class="s" fill="'.$fill['O'].'"/>';
        $html .= '<text x="'.$cx.'" y="'.($cy + 6).'" class="o" fill="'.$overlayColor.'">'.$overlay.'</text>';
        $html .= '<text x="'.$cx.'" y="'.($y + 57).'" class="t">'.$fdi.'</text>';
        $html .= '</g>';

        return $html;
    }

    private function superficieColor($data)
    {
        if (is_array($data) && isset($data['condicion'])) {
            if ($data['condicion'] == 'caries') {
                return '#c62828';
            }
            if ($data['condicion'] == 'obturado') {
                return '#1d4ed8';
            }
            if ($data['condicion'] == 'sellante_necesario') {
                return '#fff4f4';
            }
            if ($data['condicion'] == 'sellante_realizado') {
                return '#eef4ff';
            }
        }

        return '#ffffff';
    }

    private function marcaSimbolo($marca)
    {
        $map = [
            'extraccion_indicada' => 'X',
            'perdida_caries' => 'X',
            'perdida_otra' => 'X',
            'endodoncia_realizar' => '△',
            'endodoncia_realizada' => '△',
            'corona_indicada' => '○',
            'corona_realizada' => '○',
            'protesis_fija' => '┬',
            'protesis_removible_indicada' => '---',
            'protesis_removible_realizada' => '---',
            'protesis_total_indicada' => '=',
            'protesis_total_realizada' => '=',
            'ausente' => 'A',
        ];

        return isset($map[$marca]) ? $map[$marca] : '';
    }

    private function guardarAntecedentes($id, $payload, $base)
    {
        $ants = $this->arr($payload, 'antecedentes');
        foreach (['personales' => 'PERSONAL', 'familiares' => 'FAMILIAR'] as $k => $tipo) {
            foreach ($this->arr($ants, $k) as $item) {
                DB::table($this->tablaFormulario033('antecedentes'))->insert(array_merge($base, [
                    'id_formulario033' => $id,
                    'tipo' => $tipo,
                    'antecedente' => $this->val($item, 'nombre'),
                    'marcado' => $this->val($item, 'marcado', 0),
                    'descripcion' => $this->val($item, 'descripcion'),
                ]));
            }
        }
    }

    private function eliminarFormularioCompleto($idFormulario)
    {
        $this->limpiarDetallesFormulario($idFormulario);
        DB::table($this->tablaFormulario033())->where('id', $idFormulario)->delete();
    }

    private function limpiarDetallesFormulario($idFormulario)
    {
        $odontogramaIds = [];
        foreach (DB::table($this->tablaFormulario033('odontogramas'))->where('id_formulario033', $idFormulario)->select('id')->get() as $odontograma) {
            $odontogramaIds[] = $odontograma->id;
        }

        if (count($odontogramaIds) > 0) {
            $piezaIds = [];
            foreach (DB::table($this->tablaFormulario033('odontograma_piezas'))->whereIn('id_odontograma', $odontogramaIds)->select('id')->get() as $pieza) {
                $piezaIds[] = $pieza->id;
            }
            if (count($piezaIds) > 0) {
                DB::table($this->tablaFormulario033('odontograma_superficies'))->whereIn('id_pieza', $piezaIds)->delete();
            }
            DB::table($this->tablaFormulario033('odontograma_piezas'))->whereIn('id_odontograma', $odontogramaIds)->delete();
        }

        foreach ([
            $this->tablaFormulario033('antecedentes'),
            $this->tablaFormulario033('estomatognatico'),
            $this->tablaFormulario033('indicadores'),
            $this->tablaFormulario033('ihos_detalles'),
            $this->tablaFormulario033('indices'),
            $this->tablaFormulario033('diagnosticos'),
            $this->tablaFormulario033('evoluciones'),
        ] as $tabla) {
            DB::table($tabla)->where('id_formulario033', $idFormulario)->delete();
        }

        DB::table($this->tablaFormulario033('odontogramas'))->where('id_formulario033', $idFormulario)->delete();
    }

    private function guardarEstomatognatico($id, $payload, $base)
    {
        foreach ($this->arr($payload, 'examen_estomatognatico') as $item) {
            DB::table($this->tablaFormulario033('estomatognatico'))->insert(array_merge($base, ['id_formulario033' => $id, 'region' => $this->val($item, 'region'), 'patologia' => $this->val($item, 'patologia', 0), 'descripcion' => $this->val($item, 'descripcion')]));
        }
    }

    private function guardarOdontograma($id, $payload, $base, $now)
    {
        $odo = $this->arr($payload, 'odontograma');
        $idO = DB::table($this->tablaFormulario033('odontogramas'))->insertGetId(array_merge($base, ['id_formulario033' => $id, 'fecha_apertura' => $now, 'vigente_hasta' => $now->copy()->addDays(365)->toDateString(), 'snapshot_json' => $this->jsonEncode($odo), 'estado' => 1]));
        foreach ($this->arr($odo, 'piezas') as $fdi => $pieza) {
            $idP = DB::table($this->tablaFormulario033('odontograma_piezas'))->insertGetId(array_merge($base, ['id_odontograma' => $idO, 'fdi' => $fdi, 'denticion' => ((int) $fdi >= 50 ? 'TEMPORAL' : 'PERMANENTE'), 'marca' => $this->val($pieza, 'marca'), 'color' => $this->val($pieza, 'color'), 'movilidad' => $this->val($pieza, 'movilidad'), 'recesion' => $this->val($pieza, 'recesion'), 'meta_json' => $this->jsonEncode($pieza)]));
            foreach ($this->arr($pieza, 'superficies') as $sup => $cond) {
                DB::table($this->tablaFormulario033('odontograma_superficies'))->insert(array_merge($base, ['id_pieza' => $idP, 'superficie' => $sup, 'condicion' => is_array($cond) ? $this->val($cond, 'condicion') : $cond, 'color' => is_array($cond) ? $this->val($cond, 'color') : '']));
            }
        }

        return $idO;
    }

    private function guardarIndicadores($id, $idO, $payload, $base)
    {
        $ind = $this->arr($payload, 'indicadores');
        $ihos = $this->arr($ind, 'ihos');
        $tot = $this->arr($ihos, 'totales');
        DB::table($this->tablaFormulario033('indicadores'))->insert(array_merge($base, ['id_formulario033' => $id, 'id_odontograma' => $idO, 'ihos_placa' => $this->val($tot, 'placa', 0), 'ihos_calculo' => $this->val($tot, 'calculo', 0), 'ihos_gingivitis' => $this->val($tot, 'gingivitis', 0), 'ihos_total' => $this->val($tot, 'total', 0), 'enfermedad_periodontal' => $this->val($ind, 'periodontal'), 'oclusion' => $this->val($ind, 'oclusion'), 'fluorosis' => $this->val($ind, 'fluorosis')]));
        foreach ($this->arr($ihos, 'piezas') as $p) {
            DB::table($this->tablaFormulario033('ihos_detalles'))->insert(array_merge($base, ['id_formulario033' => $id, 'fdi' => $this->val($p, 'fdi'), 'placa' => $this->val($p, 'placa'), 'calculo' => $this->val($p, 'calculo'), 'gingivitis' => $this->val($p, 'gingivitis'), 'ausente' => $this->val($p, 'ausente', 0)]));
        }
        $idx = $this->arr($payload, 'indices');
        DB::table($this->tablaFormulario033('indices'))->insert(array_merge($base, ['id_formulario033' => $id, 'cpod_json' => $this->jsonEncode($this->arr($idx, 'cpod')), 'ceod_json' => $this->jsonEncode($this->arr($idx, 'ceod'))]));
    }

    private function guardarDiagnosticos($id, $payload, $base)
    {
        $n = 1;
        foreach ($this->arr($payload, 'diagnosticos') as $dx) {
            DB::table($this->tablaFormulario033('diagnosticos'))->insert(array_merge($base, ['id_formulario033' => $id, 'orden' => $n++, 'cie10' => $this->val($dx, 'cie10'), 'descripcion' => $this->val($dx, 'descripcion'), 'presuntivo_definitivo' => $this->val($dx, 'tipo')]));
        }
    }

    private function guardarEvoluciones($id, $payload, $base, $idUser)
    {
        $n = 1;
        foreach ($this->arr($payload, 'evoluciones') as $ev) {
            DB::table($this->tablaFormulario033('evoluciones'))->insert(array_merge($base, ['id_formulario033' => $id, 'sesion' => $n++, 'fecha' => $this->val($ev, 'fecha'), 'diagnostico' => $this->val($ev, 'diagnostico'), 'complicaciones' => $this->val($ev, 'complicaciones'), 'procedimientos' => $this->val($ev, 'procedimientos'), 'prescripciones' => $this->val($ev, 'prescripciones'), 'id_profesional' => $this->val($ev, 'id_profesional', $idUser), 'firma' => $this->val($ev, 'firma'), 'codigo_msp_acess' => $this->val($ev, 'codigo_msp_acess'), 'nro_hoja' => $this->val($ev, 'nro_hoja')]));
        }
    }

    private function arr($a, $k)
    {
        return is_array($a) && isset($a[$k]) && is_array($a[$k]) ? $a[$k] : [];
    }

    private function val($a, $k, $d = null)
    {
        return is_array($a) && array_key_exists($k, $a) ? $a[$k] : $d;
    }

    private function jsonEncode($v)
    {
        return json_encode($v, JSON_UNESCAPED_UNICODE);
    }
}
