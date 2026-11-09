<?php

namespace Sis_medico\Http\Controllers\auditoria_hc_admision;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Aud_hc_procedimiento_tarifario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Tarifario_Detalle_Procedimiento;
use Sis_medico\Tarifario_tipo_procedimiento;

class AuditoriaTarifarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function opciones(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'id_procedimiento' => 'required|integer',
        ]);

        $seleccionados = Aud_hc_procedimiento_tarifario::where(
            'aud_id_hc_procedimientos',
            $request->id_procedimiento
        )->where('id_tipo', $request->id)->pluck('id_procedimiento')->all();

        $opciones = Tarifario_Detalle_Procedimiento::join(
            'procedimiento as p',
            'p.id',
            '=',
            'tarifario_detalle_procedimiento.id_procedimiento'
        )->select(
            'tarifario_detalle_procedimiento.id',
            'tarifario_detalle_procedimiento.id_procedimiento',
            'p.nombre'
        )->where('tarifario_detalle_procedimiento.id_tipo', $request->id)
            ->groupBy(
                'tarifario_detalle_procedimiento.id',
                'tarifario_detalle_procedimiento.id_procedimiento',
                'p.nombre'
            )->get();

        foreach ($opciones as $opcion) {
            $opcion->checked = in_array($opcion->id_procedimiento, $seleccionados);
        }

        return response()->json($opciones);
    }

    public function guardar(Request $request)
    {
        $this->validate($request, [
            'id_procedimiento' => 'required|integer',
            'tarifario' => 'required|integer',
            'complemento' => 'nullable|array',
            'complemento.*' => 'integer',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $procedimiento = Aud_Hc_Procedimientos::findOrFail($request->id_procedimiento);
                Aud_hc_procedimiento_tarifario::where(
                    'aud_id_hc_procedimientos',
                    $request->id_procedimiento
                )->where('porcentaje_honorario', '50')->delete();

                $grupo = Tarifario_tipo_procedimiento::find($request->tarifario);
                if (!$grupo || empty($request->complemento)) {
                    return;
                }

                $detalles = Tarifario_Detalle_Procedimiento::where('id_grupo', $grupo->id_grupo)
                    ->whereIn('id', $request->complemento)
                    ->where('porcentaje_honorario', '50')
                    ->get();
                $filas = [];

                foreach ($detalles as $detalle) {
                    $filas[] = [
                        'id_hc_procedimientos' => $procedimiento->id_procedimientos_org,
                        'aud_id_hc_procedimientos' => $request->id_procedimiento,
                        'id_tipo' => $detalle->id_tipo,
                        'id_procedimiento' => $detalle->id_procedimiento,
                        'codigo_tarifario' => $detalle->codigo_tarifario,
                        'porcentaje_honorario' => $detalle->porcentaje_honorario,
                        'id_usuariomod' => Auth::id(),
                        'id_usuariocrea' => Auth::id(),
                        'ip_modificacion' => request()->ip(),
                        'ip_creacion' => request()->ip(),
                    ];
                }

                if ($filas) {
                    Aud_hc_procedimiento_tarifario::insert($filas);
                }
            });

            return response()->json(['status' => true, 'msj' => 'Guardado correctamente.']);
        } catch (\Throwable $error) {
            report($error);

            return response()->json([
                'status' => false,
                'msj' => 'No se pudo guardar el tarifario de auditoría.',
            ], 500);
        }
    }
}
