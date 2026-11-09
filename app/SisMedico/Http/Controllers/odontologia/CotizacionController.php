<?php

namespace Sis_medico\Http\Controllers\odontologia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Services\OdontCotizacion;

class CotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function tablaCotizaciones()
    {
        return 'odont_cotizaciones';
    }

    protected function tablaFormulario033()
    {
        return 'odont_formulario033';
    }

    protected function patronEspecialidad()
    {
        return '%odontolog%';
    }

    protected function prefijoRutas()
    {
        return 'odontologia';
    }

    protected function nombreArchivoPdf()
    {
        return 'cotizacion_odontologica_';
    }

    protected function observacionCotizacionAnterior()
    {
        return 'Cotizacion odontologica:%';
    }

    protected function claveModuloCotizacion()
    {
        return 'odontologica';
    }

    protected function configuracionCotizacion()
    {
        return [
            'tabla_formulario' => $this->tablaFormulario033(),
            'patron_especialidad' => $this->patronEspecialidad(),
        ];
    }

    protected function historia($hcid)
    {
        abort_unless(Opcion_Usuario::where('id_opcion', '2')->where('id_tipo_usuario', Auth::user()->id_tipo_usuario)->exists(), 403);
        $h = DB::table('historiaclinica as h')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('especialidad as e', 'e.id', 'a.espid')
            ->where('h.hcid', $hcid)
            ->where('e.estado', 1)
            ->whereRaw('LOWER(e.nombre) LIKE ?', [$this->patronEspecialidad()])
            ->select('h.*')
            ->first();
        abort_unless($h, 404);

        return $h;
    }

    public function preparar($hcid)
    {
        $h = $this->historia($hcid);
        $q = DB::table($this->tablaCotizaciones())->where('hcid', $hcid)->where('id_paciente', $h->id_paciente)->where('estado', 'BORRADOR')->orderBy('id', 'desc')->first();
        $filas = OdontCotizacion::seleccionados($hcid, $this->configuracionCotizacion());
        $precios = [];
        if ($q) {
            foreach (json_decode($q->detalle_json, true) ?: [] as $fila) {
                // Preserve prices edited in the draft, but let a newly configured
                // catalog price fill draft rows that were previously pending.
                if (isset($fila['clave']) && isset($fila['precio']) && trim((string) $fila['precio']) !== '') {
                    $precios[$fila['clave']] = $fila['precio'];
                }
            }
        }
        foreach ($filas as &$fila) {
            if (array_key_exists($fila['clave'], $precios)) {
                $fila['precio'] = $precios[$fila['clave']];
            }
        }
        unset($fila);

        return response()->json(['ok' => true, 'cotizacion' => $q ? OdontCotizacion::resumen($q) : null, 'filas' => $filas]);
    }

    public function ver($hcid, $id)
    {
        $h = $this->historia($hcid);
        $q = DB::table($this->tablaCotizaciones())->where('hcid', $hcid)->where('id_paciente', $h->id_paciente)->where('id', $id)->first();
        abort_unless($q, 404);

        return response()->json(['ok' => true, 'cotizacion' => OdontCotizacion::resumen($q)]);
    }

    public function pdf($hcid, $id)
    {
        $h = $this->historia($hcid);
        $q = DB::table($this->tablaCotizaciones())->where('hcid', $hcid)->where('id_paciente', $h->id_paciente)->where('id', $id)->first();
        abort_unless($q, 404);
        $paciente = DB::table('paciente')->where('id', $h->id_paciente)->first();
        $agenda = DB::table('agenda')->where('id', $h->id_agenda)->first();
        $empresa = $agenda && $agenda->id_empresa ? DB::table('empresa')->where('id', $agenda->id_empresa)->first() : null;
        // Export the stored quotation, never rebuild it from today's treatments or prices.
        $filas = json_decode($q->detalle_json, true) ?: [];
        $pendientes = count(array_filter($filas, function ($f) {
            return ! isset($f['precio']) || $f['precio'] === '';
        }));
        $html = view('odontologia.cotizacion_pdf', compact('q', 'paciente', 'empresa', 'filas', 'pendientes'))->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->output();
        $font = $pdf->getDomPDF()->getFontMetrics()->getFont('Helvetica', 'normal');
        $pdf->getDomPDF()->get_canvas()->page_text(710, 567, 'Pagina {PAGE_NUM} de {PAGE_COUNT}', $font, 8, [0.35, 0.4, 0.45]);

        return $pdf->stream($this->nombreArchivoPdf().$q->id.'.pdf');
    }

    public function historial($id_paciente)
    {
        abort_unless(Opcion_Usuario::where('id_opcion', '2')->where('id_tipo_usuario', Auth::user()->id_tipo_usuario)->exists(), 403);
        $cotizaciones = $this->cotizacionesPaciente($id_paciente);

        return view('odontologia.cotizaciones_historial', compact('cotizaciones'));
    }

    public function panel(Request $request, $id_paciente)
    {
        abort_unless(Opcion_Usuario::where('id_opcion', '2')->where('id_tipo_usuario', Auth::user()->id_tipo_usuario)->exists(), 403);
        $paciente = DB::table('paciente')->where('id', $id_paciente)->first();
        abort_unless($paciente, 404);
        $cotizaciones = $this->cotizacionesPaciente($id_paciente);
        $abrir = (string) $request->get('abrir', '');

        return view('odontologia.cotizaciones', compact('paciente', 'cotizaciones', 'abrir'));
    }

    protected function cotizacionesPaciente($id_paciente)
    {
        $historial = DB::table($this->tablaCotizaciones())->where('id_paciente', $id_paciente)->orderBy('id', 'desc')->get();
        $anteriores = DB::table('proforma_cabecera')->where('id_paciente', $id_paciente)->where('observacion', 'like', $this->observacionCotizacionAnterior())->where('estado', '<>', '0')->orderBy('id', 'desc')->get();
        $cotizaciones = [];
        foreach ($historial as $cotizacion) {
            $clave = $this->claveModuloCotizacion().'-'.$cotizacion->id;
            $cotizaciones[] = [
                'id' => $cotizacion->id,
                'clave' => $clave,
                'fecha' => substr($cotizacion->created_at, 0, 10),
                'orden' => $cotizacion->created_at,
                'total' => $cotizacion->total,
                'detalle_url' => route($this->prefijoRutas().'.cotizaciones.detalle', ['id_paciente' => $id_paciente, 'id' => $cotizacion->id]),
                'panel_url' => route($this->prefijoRutas().'.cotizaciones.panel', ['id_paciente' => $id_paciente]).'?abrir='.rawurlencode($clave),
            ];
        }
        foreach ($anteriores as $cotizacion) {
            $fecha = $cotizacion->fecha_emision ?: substr($cotizacion->created_at, 0, 10);
            $clave = 'anterior-'.$cotizacion->id;
            $cotizaciones[] = [
                'id' => $cotizacion->id,
                'clave' => $clave,
                'fecha' => substr($fecha, 0, 10),
                'orden' => $cotizacion->created_at ?: $fecha,
                'total' => $cotizacion->total_final,
                'detalle_url' => route($this->prefijoRutas().'.cotizaciones.anterior', ['id_paciente' => $id_paciente, 'id' => $cotizacion->id]),
                'panel_url' => route($this->prefijoRutas().'.cotizaciones.panel', ['id_paciente' => $id_paciente]).'?abrir='.rawurlencode($clave),
            ];
        }
        usort($cotizaciones, function ($a, $b) {
            if ($a['orden'] === $b['orden']) {
                return $b['id'] <=> $a['id'];
            }

            return strcmp($b['orden'], $a['orden']);
        });

        return $cotizaciones;
    }

    public function detalle($id_paciente, $id)
    {
        abort_unless(Opcion_Usuario::where('id_opcion', '2')->where('id_tipo_usuario', Auth::user()->id_tipo_usuario)->exists(), 403);
        $q = DB::table($this->tablaCotizaciones())->where('id_paciente', $id_paciente)->where('id', $id)->first();
        abort_unless($q, 404);
        $filas = json_decode($q->detalle_json, true) ?: [];
        $rutaPdf = $this->prefijoRutas().'.cotizaciones.pdf';

        return view('odontologia.cotizacion_detalle', compact('q', 'filas', 'rutaPdf'));
    }

    public function anterior($id_paciente, $id)
    {
        abort_unless(Opcion_Usuario::where('id_opcion', '2')->where('id_tipo_usuario', Auth::user()->id_tipo_usuario)->exists(), 403);
        $q = DB::table('proforma_cabecera')->where('id_paciente', $id_paciente)->where('id', $id)->where('observacion', 'like', $this->observacionCotizacionAnterior())->where('estado', '<>', '0')->first();
        abort_unless($q, 404);
        $detalles = DB::table('proforma_detalle')->where('id_proforma', $id)->get();

        return view('odontologia.cotizacion_anterior', compact('q', 'detalles'));
    }

    public function guardar(Request $request, $hcid)
    {
        $h = $this->historia($hcid);
        $this->validate($request, ['filas' => 'required|array|min:1|max:200', 'estado' => 'required|in:BORRADOR,EMITIDA', 'version' => 'required|integer|min:0', 'id' => 'nullable|integer|min:1']);

        return DB::transaction(function () use ($request, $hcid, $h) {
            // Serialize draft creation for this history, including simultaneous tabs.
            DB::table('historiaclinica')->where('hcid', $hcid)->lockForUpdate()->first();
            $q = DB::table($this->tablaCotizaciones())->where('hcid', $hcid)->where('id_paciente', $h->id_paciente);
            $q = $request->get('id') ? $q->where('id', $request->get('id'))->lockForUpdate()->first() : $q->where('estado', 'BORRADOR')->lockForUpdate()->first();
            if (($request->get('id') && ! $q) || ($q && ($q->estado !== 'BORRADOR' || (int) $q->version !== (int) $request->get('version')))) {
                return response()->json(['mensaje' => 'La cotizacion cambio o ya fue emitida. Vuelva a abrirla antes de editar.'], 409);
            }
            $actuales = OdontCotizacion::seleccionados($hcid, $this->configuracionCotizacion());
            $recibidas = [];
            foreach ($request->get('filas') as $fila) {
                if (! is_array($fila) || ! isset($fila['clave']) || ! is_string($fila['clave']) || isset($recibidas[$fila['clave']])) {
                    return response()->json(['mensaje' => 'Hay procedimientos duplicados o invalidos.'], 422);
                }
                $recibidas[$fila['clave']] = $fila;
            }
            if (count($actuales) !== count($recibidas)) {
                return $this->tratamientoCambio();
            }
            $centavos = 0;
            foreach ($actuales as &$fila) {
                if (! isset($recibidas[$fila['clave']])) {
                    return $this->tratamientoCambio();
                }
                $entrada = $recibidas[$fila['clave']];
                foreach (['fecha', 'pieza', 'caras', 'diagnosticos', 'id_procedimiento', 'procedimiento'] as $campo) {
                    if (! isset($entrada[$campo]) || (string) $entrada[$campo] !== (string) $fila[$campo]) {
                        return $this->tratamientoCambio();
                    }
                }
                $precio = isset($entrada['precio']) ? trim((string) $entrada['precio']) : '';
                if ($precio === '' && $request->get('estado') === 'BORRADOR') {
                    $fila['precio'] = null;

                    continue;
                }
                if (! preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $precio)) {
                    return response()->json(['mensaje' => 'Ingrese un precio valido para cada procedimiento (maximo dos decimales).'], 422);
                }
                $valor = (int) round((float) $precio * 100);
                $fila['precio'] = number_format($valor / 100, 2, '.', '');
                $centavos += $valor;
            }
            unset($fila);
            $now = date('Y-m-d H:i:s');
            $datos = ['id_paciente' => $h->id_paciente, 'hcid' => $hcid, 'estado' => $request->get('estado'),
                'version' => $q ? $q->version + 1 : 1, 'total' => number_format($centavos / 100, 2, '.', ''),
                'detalle_json' => json_encode($actuales, JSON_UNESCAPED_UNICODE), 'id_usuariomod' => Auth::id(), 'updated_at' => $now];
            if ($q) {
                $id = $q->id;
                DB::table($this->tablaCotizaciones())->where('id', $id)->update($datos);
            } else {
                $datos['created_at'] = $now;
                $datos['id_usuariocrea'] = Auth::id();
                $id = DB::table($this->tablaCotizaciones())->insertGetId($datos);
            }

            return response()->json(['ok' => true, 'cotizacion' => OdontCotizacion::resumen(DB::table($this->tablaCotizaciones())->where('id', $id)->first())]);
        });
    }

    private function tratamientoCambio()
    {
        return response()->json(['mensaje' => 'Los procedimientos asignados cambiaron. Pulse Armar cotización para actualizarlos.'], 409);
    }
}
