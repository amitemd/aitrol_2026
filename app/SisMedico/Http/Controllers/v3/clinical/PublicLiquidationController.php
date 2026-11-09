<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro;
use Sis_medico\v3\contable\Menu;

/**
 * Consulta de atenciones listas para la liquidación pública.
 *
 * Se mantiene la URL histórica de consultam para no romper accesos existentes,
 * pero se presenta mediante la plantilla V3 y una consulta tolerante a datos
 * incompletos (una agenda sin médico, especialidad o procedimiento no rompe la
 * pantalla).
 */
class PublicLiquidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);

        return $this->page('v3.clinical.public_liquidation.index', array(
            'filters' => $filters,
            'rows' => $this->search($filters)->paginate(50)->appends($request->query()),
            'insurances' => Seguro::where('tipo', '0')->where('inactivo', '1')->orderBy('nombre')->get(array('id', 'nombre')),
        ));
    }

    /**
     * Rutas heredadas de producción. La generación se mantiene individual por
     * seguridad: cada registro usa el flujo de planilla ya existente en local.
     */
    public function process(Request $request)
    {
        return redirect()->route('consultam.nuevaLiquidacionPublicaIndex', $this->requestQuery($request))
            ->with('info', 'Selecciona “Generar planilla” en el registro que deseas liquidar.');
    }

    public function processLab(Request $request)
    {
        return redirect()->route('consultam.nuevaLiquidacionPublicaIndex', $this->requestQuery($request))
            ->with('info', 'La consulta de laboratorio está disponible desde cada planilla generada.');
    }

    private function filters(Request $request)
    {
        $from = $this->date($request->input('fecha', $request->input('desde')));
        $to = $this->date($request->input('fecha_hasta', $request->input('hasta')));
        if ($to->lt($from)) {
            $swap = $from;
            $from = $to;
            $to = $swap;
        }

        return array(
            'fecha' => $from->format('Y-m-d'),
            'fecha_hasta' => $to->format('Y-m-d'),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'seguro' => trim((string) $request->input('seguro', $request->input('id_seguro', ''))),
            'tipo' => in_array((string) $request->input('tipo', ''), array('0', '1'), true) ? (string) $request->input('tipo') : '',
        );
    }

    private function requestQuery(Request $request)
    {
        return array_filter($request->only(array('fecha', 'fecha_hasta', 'paciente', 'seguro', 'tipo')), function ($value) {
            return $value !== null && $value !== '';
        });
    }

    private function date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return Carbon::today();
        }

        foreach (array('Y-m-d', 'Y/m/d', 'd/m/Y') as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date->startOfDay();
                }
            } catch (\Exception $exception) {
                // Intenta el siguiente formato válido para las pantallas antiguas.
            }
        }

        return Carbon::today();
    }

    private function search(array $filters)
    {
        $query = DB::table('agenda as a')
            ->join('historiaclinica as hc', 'a.id', '=', 'hc.id_agenda')
            ->join('seguros as s', 'hc.id_seguro', '=', 's.id')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->join('hc_procedimientos as hp', 'hp.id_hc', '=', 'hc.hcid')
            ->leftJoin('users as u', 'hc.id_doctor1', '=', 'u.id')
            ->leftJoin('especialidad as e', 'a.espid', '=', 'e.id')
            ->leftJoin('hc_procedimiento_final as hpf', 'hpf.id_hc_procedimientos', '=', 'hp.id')
            ->leftJoin('procedimiento as proc', 'proc.id', '=', 'hpf.id_procedimiento')
            ->leftJoin('archivo_plano_cabecera as planilla', function ($join) {
                $join->on('planilla.id_hc_procedimimentos', '=', 'hp.id')
                    ->where('planilla.estado', '=', '1');
            })
            ->where('a.estado', '1')
            ->where('a.estado_cita', '4')
            ->where('s.tipo', '0')
            ->where('a.proc_consul', '<>', '2')
            ->whereNotIn('a.id_doctor1', array('4444444444', 'A', 'B'))
            ->whereBetween('a.fechaini', array($filters['fecha'].' 00:00:00', $filters['fecha_hasta'].' 23:59:59'))
            ->where(function ($where) {
                $where->whereNull('e.nombre')->orWhere('e.nombre', '<>', 'CARDIOLOGO');
            })
            ->where(function ($where) {
                $where->whereNull('hpf.id')->orWhereNotNull('proc.id_grupo_procedimiento');
            });

        if ($filters['seguro'] !== '' && ctype_digit($filters['seguro'])) {
            $query->where('s.id', $filters['seguro']);
        }

        if ($filters['tipo'] !== '') {
            $query->where('a.proc_consul', $filters['tipo']);
        }

        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term !== '') {
                    $query->whereRaw("CONCAT_WS(' ', p.apellido1, p.apellido2, p.nombre1, p.nombre2) LIKE ?", array('%'.$term.'%'));
                }
            }
        }

        return $query->select(array(
            'hp.id as procedure_id', 'hc.hcid', 'hc.id_paciente as patient_id', 'hc.id_seguro as insurance_id',
            'a.fechaini', 'a.proc_consul', 'a.tipo_cita', 'a.observaciones',
            's.nombre as insurance_name', 'e.nombre as specialty_name', 'proc.nombre as procedure_name',
            'planilla.id as planilla_id', 'hp.hora_inicio', 'hp.hora_fin',
            DB::raw("TRIM(CONCAT(COALESCE(p.apellido1, ''), ' ', COALESCE(p.apellido2, ''), ' ', COALESCE(p.nombre1, ''), ' ', COALESCE(p.nombre2, ''))) as patient_name"),
            DB::raw("TRIM(CONCAT(COALESCE(u.apellido1, ''), ' ', COALESCE(u.apellido2, ''), ' ', COALESCE(u.nombre1, ''))) as doctor_name")
        ))->orderBy('a.fechaini', 'desc')->orderBy('hp.id', 'desc');
    }

    private function page($view, array $payload)
    {
        $data = array(
            'controlador' => 'legacy-route', 'carpeta' => 'finanzas',
            'submodulo' => 'consultam.nuevaLiquidacionPublicaIndex', 'submodulo_' => 'consultam.nuevaLiquidacionPublicaIndex',
            // Esta ruta histórica no pasa por el middleware V3: cargar el
            // árbol existente evita que el sidebar quede vacío.
            'menu' => Menu::getMenu(), 'menu_id' => 683, 'table' => 1,
            'titulo' => 'Control de liquidación automática', 'title' => 'Control de liquidación automática | Aitrol Hospitalario',
            'vista' => $view, 'obseraudi' => 'Consulta de liquidación automática', 'contenido' => view($view, $payload),
        );
        $current = config('data');
        config(array('data' => array_merge(is_array($current) ? $current : array(), $data)));

        return view('v3.layouts.view_child');
    }
}
