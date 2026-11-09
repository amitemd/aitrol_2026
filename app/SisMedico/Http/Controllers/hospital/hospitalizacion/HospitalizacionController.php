<?php

namespace Sis_medico\Http\Controllers\hospital\hospitalizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Sala;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\hc_receta;
use Sis_medico\Movimiento;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Producto_Medicina;
use Sis_medico\Hospitalizacion;
use Sis_medico\Empresa;
use Illuminate\Support\Facades\Session;
use Sis_medico\agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Ho_Hc_Receta_Evolucion;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Planilla;
use Sis_medico\Producto;
use Sis_medico\Services\CompanyMedicationAdministrationWarehouseService;
use Sis_medico\Services\CompanyPrimaryInventoryWarehouseService;
use Sis_medico\Services\CurrentHospitalResolver;
use Sis_medico\Services\ClinicalMedicationAdministrationService;
use Sis_medico\Services\MedicationInventoryProductService;
use Sis_medico\Services\HospitalEpisodeEmergencySource;
use Sis_medico\Services\HospitalEpisodeEvolutionSource;
use Sis_medico\Services\HospitalEpisodeMedicationPrescriptionService;
use Sis_medico\Services\HospitalDocumentService;
use Sis_medico\Services\ClinicalElectronicSignatureQr;
use Sis_medico\Services\ClinicalRecordSignatureService;
use Sis_medico\User;
use Sis_medico\Exceptions\ClinicalMedicationBlockedException;

class HospitalizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    public function master()
    {
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $hospitalId = app(CurrentHospitalResolver::class)->id();
        $salas = Sala::where('estado', '1')
            ->when($hospitalId, function ($query) use ($hospitalId) {
                return $query->where('id_hospital', $hospitalId);
            })->get();
        $en_habitacion = Ho_Traspaso_Sala008::where('ho_traspaso_sala008.estado', '2')
            ->join('sala as s', 's.id', 'ho_traspaso_sala008.id_sala')
            ->join('paciente as p', 'p.id', 'ho_traspaso_sala008.id_paciente')
            ->when($hospitalId, function ($query) use ($hospitalId) {
                return $query->where('s.id_hospital', $hospitalId);
            })
            ->select('ho_traspaso_sala008.*', 's.nombre_sala', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2')
            ->orderBy('ho_traspaso_sala008.fecha', 'desc')
            ->paginate(50);
        //dd($en_habitacion);
        //dd($en_habitacion);

        return view('hospital/habitacion/master', ['salas' => $salas, 'en_habitacion' => $en_habitacion, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'cedula' => '', 'nombres' => '', 'sala' => '']);
    }

    public function buscar_hospitalizado(Request $request)
    {
        //$fecha_hoy = date('Y-m-d');
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $sala = $request['sala'];
        $nombres = $request['paciente'];
        $cedula = $request['cedula'];
        //dd($request->all());
        $en_habitacion = Ho_Traspaso_Sala008::where('ho_traspaso_sala008.estado', '2')
            ->join('paciente as p', 'p.id', 'ho_traspaso_sala008.id_paciente')
            ->join('sala as s', 's.id', 'ho_traspaso_sala008.id_sala')
            ->select('ho_traspaso_sala008.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre_sala');

        $hospitalId = app(CurrentHospitalResolver::class)->id();
        if ($hospitalId) $en_habitacion->where('s.id_hospital', $hospitalId);

        if ($fecha_desde != null && $fecha_hasta != null) {
            $en_habitacion = $en_habitacion->whereBetween('fecha', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($sala != null) {
            $en_habitacion = $en_habitacion->where('id_sala', $sala);
        }


        if ($cedula != null) {
            $en_habitacion = $en_habitacion->where('id_paciente', $cedula);
        }

        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $en_habitacion = $en_habitacion->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $en_habitacion = $en_habitacion->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        $en_habitacion = $en_habitacion->orderBy('ho_traspaso_sala008.fecha', 'desc')->paginate(50);
        //dd($en_habitacion);

        $salas = Sala::where('estado', '1')
            ->when($hospitalId, function ($query) use ($hospitalId) {
                return $query->where('id_hospital', $hospitalId);
            })->get();

        return view('hospital/habitacion/master', ['salas' => $salas, 'en_habitacion' => $en_habitacion, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'cedula' => $cedula, 'sala' => $sala]);
    }

    public function descargo_medicina($id_solic)
    {

        $solicitud = app(HospitalDocumentService::class)->obtain($id_solic);

        $logs = $solicitud->log;
        $agenda = [];

        $historias = collect();

        foreach ($logs as $log) {
            if ($log->id_agenda != null && !is_null($log->agenda)) {
                $agenda[$log->id_agenda] = $log->agenda;
                if (!is_null($log->agenda->historia_clinica)
                    && (string) $log->agenda->historia_clinica->encounter_id === (string) $solicitud->encounter_id) {
                    $historias->push($log->agenda->historia_clinica);
                }
            }
        }

        $historia = $solicitud->agenda ? $solicitud->agenda->historia_clinica : null;
        if (is_null($historia) || (string) $historia->encounter_id !== (string) $solicitud->encounter_id) {
            $historia = Historiaclinica::where('encounter_id', $solicitud->encounter_id)
                ->where('id_paciente', $solicitud->id_paciente)
                ->orderBy('created_at', 'desc')->first();
        }
        if (!is_null($historia)) {
            $historias->push($historia);
        }
        $historias = $historias->unique('hcid');
        $recetas = $historias->flatMap(function ($historiaClinica) {
            return $historiaClinica->recetas()->with('detalles')->get();
        })->unique('id')->values();
        $procedimientos = $historia
            ? hc_procedimientos::where('id_hc', $historia->hcid)->get()
            : collect();

        return view('hospital/hospitalizacion/index', [
            'solicitud' => $solicitud,
            'agenda' => $agenda,
            'procedimientos' => $procedimientos,
            'recetas' => $recetas,
        ]);
    }


    public function descargo_enfermeria_detalle($id_receta, $id_solicitud)
    {
        $solicitud = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $receta = hc_receta::where('id', $id_receta)
            ->whereHas('historia', function ($query) use ($solicitud) {
                $query->where('encounter_id', $solicitud->encounter_id)
                    ->where('id_paciente', $solicitud->id_paciente);
            })->firstOrFail();
        $detalles = $receta->detalles;
        $id_empresa = Session::get('id_empresa');
        $bodegaAdministracion = null;
        $bodegaPrincipalInventario = null;
        $bodegaAdministracionError = null;
        try {
            $bodegaAdministracion = app(CompanyMedicationAdministrationWarehouseService::class)->resolveOrProvision(
                $id_empresa, Auth::user()->id, request()->ip(), app(CurrentHospitalResolver::class)->id()
            );
            $bodegaPrincipalInventario = app(CompanyPrimaryInventoryWarehouseService::class)->resolve($id_empresa);
        } catch (\DomainException $exception) {
            $bodegaAdministracionError = $exception->getMessage();
        }

        $productosPorDetalle = collect();
        if ($detalles->isNotEmpty()) {
            $relaciones = Producto_Medicina::whereIn('id_medicina', $detalles->pluck('id_medicina')->filter()->unique())
                ->get()->keyBy('id_medicina');
            $productos = Producto::whereIn('id', $relaciones->pluck('id_producto')->filter()->unique())
                ->where('estado', 1)->get()->keyBy('id');
            foreach ($detalles as $detalle) {
                $relacion = $relaciones->get($detalle->id_medicina);
                if ($relacion && $productos->has($relacion->id_producto)) {
                    $productosPorDetalle->put((string) $detalle->id, $productos->get($relacion->id_producto));
                    continue;
                }
                $producto = app(MedicationInventoryProductService::class)->resolve(
                    $detalle->id_medicina,
                    $id_empresa,
                    $bodegaPrincipalInventario ? $bodegaPrincipalInventario->inventory_id : null,
                    true,
                    Auth::user()->id,
                    request()->ip()
                );
                if ($producto) {
                    $productosPorDetalle->put((string) $detalle->id, $producto);
                }
            }
        }

        $procedimientos = hc_procedimientos::where('id_hc', $receta->id_hc)->orderBy('id', 'desc')->get();
        $procedimientoDescargo = null;
        if ($solicitud->id_hcproc) {
            $procedimientoDescargo = $procedimientos->firstWhere('id', $solicitud->id_hcproc);
        }
        if (!$procedimientoDescargo) $procedimientoDescargo = $procedimientos->first();
        if ($procedimientos->count() > 0) {
            $hcid = $receta->id_hc;

        } else {
            $hcid = "";
        }

        $administracionesPorDetalle = [];
        foreach (app(HospitalEpisodeMedicationPrescriptionService::class)->forEpisode($solicitud) as $prescription) {
            if (strpos($prescription['prescripcion'], 'hc_receta_detalle:') !== 0) continue;
            $detailId = substr($prescription['prescripcion'], strlen('hc_receta_detalle:'));
            $administracionesPorDetalle[$detailId] = $prescription['eventos_emar'] ?? [];
        }

        $viewData = [
            'detalles' => $detalles,
            'procedimientos' => $procedimientos, 'hcid' => $hcid, 'solicitud' => $solicitud,
            'administracionesPorDetalle' => $administracionesPorDetalle,
            'receta' => $receta, 'procedimientoDescargo' => $procedimientoDescargo,
            'bodegaAdministracion' => $bodegaAdministracion,
            'bodegaAdministracionError' => $bodegaAdministracionError,
            'productosPorDetalle' => $productosPorDetalle,
        ];
        if (request()->boolean('standalone')) {
            return view('hospital.hospitalizacion.administracion_medicamentos', $viewData);
        }
        return view('hospital/hospitalizacion/detalle', $viewData);
    }

    public function descargo_enfermeria_detalle_store(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $payload = $request->all();
                $discharge = null;

                if (($payload['status'] ?? null) === ClinicalMedicationAdministrationService::ADMINISTERED
                    && $request->boolean('auto_discharge')) {
                    $detail = hc_receta_detalle::with('hc_receta.historia')->find($request->id);
                    if (!$detail || !$detail->hc_receta || !$detail->hc_receta->historia) {
                        throw new ClinicalMedicationBlockedException(array('No existe una orden médica válida para administrar este medicamento.'));
                    }

                    $companyId = Session::get('id_empresa');
                    $warehouse = app(CompanyMedicationAdministrationWarehouseService::class)->resolveOrProvision(
                        $companyId, Auth::user()->id, $request->ip(), app(CurrentHospitalResolver::class)->id()
                    );
                    $primaryWarehouse = app(CompanyPrimaryInventoryWarehouseService::class)->resolve($companyId);
                    $product = app(MedicationInventoryProductService::class)->resolve(
                        $detail->id_medicina, $companyId, $primaryWarehouse->inventory_id,
                        true, Auth::user()->id, $request->ip()
                    );
                    if (!$product) {
                        throw new ClinicalMedicationBlockedException(array('No se encontró una correspondencia inequívoca entre el medicamento prescrito y el producto de inventario.'));
                    }

                    $procedureId = (int) $request->input('id_hc_procedimientos');
                    if ($procedureId < 1) {
                        throw new ClinicalMedicationBlockedException(array('No existe un procedimiento asociado para registrar el descargo.'));
                    }
                    if (!hc_procedimientos::where('id', $procedureId)
                        ->where('id_hc', $detail->hc_receta->id_hc)->exists()) {
                        throw new ClinicalMedicationBlockedException(array('El procedimiento de descargo no corresponde a esta orden médica.'));
                    }

                    $request->merge(array(
                        'id_bodega' => $warehouse->inventory_id,
                        'detalle_respuesta' => 1,
                    ));
                    $discharge = app(\Sis_medico\Http\Controllers\EnfermeriaController::class)
                        ->serie_enfermeroget($product->codigo, $procedureId, $request);
                    if (!is_array($discharge) || ($discharge['status'] ?? null) !== 'ok') {
                        $message = is_string($discharge) ? $discharge : 'No fue posible descargar el medicamento del inventario.';
                        throw new ClinicalMedicationBlockedException(array($message));
                    }
                    $payload['series'] = $discharge['serie'];
                }

                $administration = app(ClinicalMedicationAdministrationService::class)
                    ->registerLegacyRecipeDetail($request->id, $payload, Auth::user()->id, $request->ip());

                return array('administration' => $administration, 'discharge' => $discharge);
            });
            $administration = $result['administration'];

            return array(
                'estado' => 'O',
                'msn' => $administration->status === ClinicalMedicationAdministrationService::OMITTED
                    ? 'Omisión registrada con trazabilidad.'
                    : 'Medicamento egresado de la bodega de administración, contabilizado y registrado en el eMAR.',
                'administration_id' => $administration->id,
                'serie' => $result['discharge']['serie'] ?? $administration->inventory_series,
                'fecha' => $administration->administered_at->format('Y-m-d'),
                'hora' => $administration->administered_at->format('H:i'),
            );
        } catch (ClinicalMedicationBlockedException $exception) {
            return response()->json(array('estado' => 'E', 'msn' => implode("\n", $exception->errors()), 'errors' => $exception->errors()), 422);
        } catch (\DomainException $exception) {
            return response()->json(array('estado' => 'E', 'msn' => $exception->getMessage()), 422);
        } catch (\RuntimeException $exception) {
            return response()->json(array('estado' => 'E', 'msn' => $exception->getMessage()), 422);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(array(
                'estado' => 'E',
                'msn' => 'No fue posible completar conjuntamente el descargo y la administración.',
            ), 500);
        }
    }

    public function corregir_hora_entrega_medicamento($id, Request $request)
    {
        try {
            $administration = app(ClinicalMedicationAdministrationService::class)
                ->correctLegacyAdministrationTime(
                    $id,
                    $request->input('administered_at'),
                    $request->input('reason'),
                    Auth::user()->id,
                    $request->ip(),
                    Session::get('id_empresa')
                );

            return response()->json(array(
                'estado' => 'O',
                'msn' => 'Hora real de entrega actualizada. El Formulario 022 ya refleja la corrección.',
                'administration_id' => $administration->id,
                'administered_at' => $administration->administered_at->format('Y-m-d\TH:i'),
                'fecha' => $administration->administered_at->format('Y-m-d'),
                'hora' => $administration->administered_at->format('H:i'),
            ));
        } catch (ClinicalMedicationBlockedException $exception) {
            return response()->json(array(
                'estado' => 'E',
                'msn' => implode("\n", $exception->errors()),
                'errors' => $exception->errors(),
            ), 422);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(array(
                'estado' => 'E',
                'msn' => 'No fue posible corregir la hora real de entrega.',
            ), 500);
        }
    }
    public function formulario005_pdf($id, Request $request)
    {
        $solicitud005 = Ho_Solicitud::with(['paciente', 'agenda.historia_clinica'])
            ->where('id', $id)->firstOrFail();
        app(HospitalDocumentService::class)->authorize($solicitud005);
        $agenda = $solicitud005->agenda;
        $historyQuery = Historiaclinica::where('encounter_id', $solicitud005->encounter_id)
            ->where('id_paciente', $solicitud005->id_paciente);
        $historia = $agenda ? (clone $historyQuery)->where('id_agenda', $agenda->id)->first() : null;
        if (!$historia) $historia = $historyQuery->orderByDesc('hcid')->first();
        abort_if(!$historia, 404, 'El episodio no tiene historia clínica asociada.');
        $hcProc = $solicitud005->id_hcproc
            ? hc_procedimientos::where('id', $solicitud005->id_hcproc)->where('id_hc', $historia->hcid)->firstOrFail()
            : hc_procedimientos::where('id_hc', $historia->hcid)->orderBy('id', 'desc')->first();
        $solicitud005->id_agenda = $agenda ? $agenda->id : $historia->id_agenda;
        $solicitud005->hcid = $historia->hcid;
        $solicitud005->id_hcproc = $hcProc ? $hcProc->id : null;
        $empresa = $agenda && $agenda->id_empresa
            ? Empresa::find($agenda->id_empresa)
            : Empresa::find($request->session()->get('id_empresa'));
        $evoluciones = app(HospitalEpisodeEvolutionSource::class)
            ->forEpisode($solicitud005, $historia, optional($empresa)->id)
            ->filter(function ($evolucion) {
            $motivo = trim(strip_tags((string) $evolucion->motivo));
            $cuadroClinico = trim(strip_tags((string) $evolucion->cuadro_clinico));
            return $motivo !== '' || $cuadroClinico !== '';
        })->values();
        // Cada evolución nueva tiene su propia receta. Mantener el mapa por
        // evolución evita repetir una receta en todas las filas del PDF.
        $recetaBase = hc_receta::where('id_hc', $historia->hcid)->latest('created_at')->first();
        $recetasPorEvolucion = Ho_Hc_Receta_Evolucion::whereIn('id_evolucion', $evoluciones->pluck('id'))
            ->get()->keyBy('id_evolucion');
        $recetas = hc_receta::with('detalles')->whereIn('id', $recetasPorEvolucion->pluck('id_receta'))
            ->get()->keyBy('id');
        $examenesFisicos = hc_child_pugh::whereIn('id_hc_evolucion', $evoluciones->pluck('id'))
            ->pluck('examen_fisico', 'id_hc_evolucion');
        $diagnosticosPorProcedimiento = Hc_Cie10::whereIn('hc_id_procedimiento', $evoluciones->pluck('hc_id_procedimiento')->filter()->unique())
            ->where('ingreso_egreso', 'INGRESO')->get()->groupBy('hc_id_procedimiento');
        $diagnosisCodes = $diagnosticosPorProcedimiento->flatten()->pluck('cie10')->filter()->unique();
        $cie4 = Cie_10_4::whereIn('id', $diagnosisCodes)->get()->keyBy('id');
        $cie3 = Cie_10_3::whereIn('id', $diagnosisCodes)->get()->keyBy('id');
        $detallesPorEvolucion = [];
        $contenidoPorEvolucion = [];
        foreach ($evoluciones as $evolucion) {
            $vinculo = $recetasPorEvolucion->get($evolucion->id);
            $recetaEvolucion = $vinculo ? $recetas->get($vinculo->id_receta) : null;
            $detallesPorEvolucion[$evolucion->id] = $recetaEvolucion ? $recetaEvolucion->detalles : collect();

            $contenido = [];
            if (trim(strip_tags((string) $evolucion->motivo)) !== '') {
                $contenido[] = 'MOTIVO: ' . trim(strip_tags((string) $evolucion->motivo));
            }
            if (trim(strip_tags((string) $evolucion->cuadro_clinico)) !== '') {
                $contenido[] = 'EVOLUCION: ' . trim(strip_tags((string) $evolucion->cuadro_clinico));
            }

            $examen = $examenesFisicos->get($evolucion->id);
            if (trim(strip_tags((string) $examen)) !== '') {
                $contenido[] = 'EXAMEN FISICO: ' . trim(strip_tags((string) $examen));
            }

            $diagnosticos = $diagnosticosPorProcedimiento->get($evolucion->hc_id_procedimiento, collect());
            $diagnosticoTexto = $diagnosticos->map(function ($diagnostico) use ($cie4, $cie3) {
                $catalogo = $cie4->get($diagnostico->cie10) ?: $cie3->get($diagnostico->cie10);
                return $diagnostico->cie10 . ($catalogo ? ' - ' . $catalogo->descripcion : '');
            })->implode('; ');
            if ($diagnosticoTexto !== '') {
                $contenido[] = 'DIAGNOSTICOS: ' . $diagnosticoTexto;
            }
            $contenidoPorEvolucion[$evolucion->id] = implode("\n", $contenido);
        }
        $detalles = $recetaBase ? $recetaBase->detalles : collect();
        $emergencySource = app(HospitalEpisodeEmergencySource::class)->forForm($solicitud005->id, '005');

        // Cada hoja se firma con el profesional que creó esa evolución. La
        // prueba QR contiene una huella del registro y una firma HMAC del
        // servidor; no reutiliza la sesión ni una imagen de firma manuscrita.
        $emergencyEvolutions = collect($emergencySource['evolutions'] ?? []);
        $authorIds = $evoluciones->pluck('id_usuariocrea')->filter()
            ->merge($emergencyEvolutions->pluck('recorded_by')->filter())
            ->map(function ($id) { return (string) $id; })->unique()->values();
        $authors = $authorIds->isEmpty()
            ? collect()
            : User::withoutGlobalScopes()->whereIn('id', $authorIds)->get()
                ->keyBy(function ($user) { return (string) $user->id; });
        $specialties = $authorIds->isEmpty()
            ? collect()
            : DB::table('user_espe as ue')->join('especialidad as e', 'e.id', '=', 'ue.espid')
                ->whereIn('ue.usuid', $authorIds)->orderBy('ue.id')
                ->get(['ue.usuid', 'e.nombre'])->groupBy(function ($row) { return (string) $row->usuid; });
        $authorData = function ($authorId) use ($authors, $specialties) {
            $authorId = (string) $authorId;
            $author = $authors->get($authorId);
            $name = $author ? trim(implode(' ', array_filter([
                $author->nombre1, $author->nombre2, $author->apellido1, $author->apellido2,
            ], function ($part) {
                $value = strtoupper(trim((string) $part));
                return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
            }))) : '';
            $authorSpecialties = $specialties->get($authorId, collect())->pluck('nombre')
                ->filter()->unique()->implode(', ');

            return [
                'author_id' => $authorId,
                'name' => $name !== '' ? $name : 'PROFESIONAL NO IDENTIFICADO',
                'specialty' => $authorSpecialties !== '' ? $authorSpecialties : 'Especialidad no registrada',
                'register' => $author ? trim((string) $author->registro_doctor) : '',
            ];
        };
        $recordSignatures = app(ClinicalRecordSignatureService::class);
        $qr = app(ClinicalElectronicSignatureQr::class);
        $signaturesByEvolution = [];
        foreach ($evoluciones as $evolution) {
            $proof = $recordSignatures->issueEvolution($evolution);
            $signaturesByEvolution[(string) $evolution->id] = array_merge(
                $authorData($evolution->id_usuariocrea),
                [
                    'status' => 'VALIDADO POR AITROL',
                    'fingerprint' => $proof['fingerprint'],
                    'validation_url' => $proof['validation_url'],
                    'qr_source' => $qr->dataUri($proof['validation_url']),
                ]
            );
        }
        $signaturesByEmergency = [];
        foreach ($emergencyEvolutions as $emergencyEvolution) {
            $proof = $recordSignatures->issueEmergency($emergencyEvolution['id']);
            if (!$proof) continue;
            $signaturesByEmergency[(string) $emergencyEvolution['id']] = array_merge(
                $authorData($emergencyEvolution['recorded_by']),
                [
                    'status' => 'VALIDADO POR AITROL',
                    'fingerprint' => $proof['fingerprint'],
                    'validation_url' => $proof['validation_url'],
                    'qr_source' => $qr->dataUri($proof['validation_url']),
                ]
            );
        }
        $view = \View::make('hospital.habitacion.formulario005_pdf', [
            'solicitud005' => $solicitud005,
            'evoluciones' => $evoluciones,
            'historia' => $historia,
            'detalles' => $detalles,
            'detallesPorEvolucion' => $detallesPorEvolucion,
            'contenidoPorEvolucion' => $contenidoPorEvolucion,
            'receta' => $recetaBase,
            'empresa' => $empresa,
            'emergencySource' => $emergencySource,
            'signaturesByEvolution' => $signaturesByEvolution,
            'signaturesByEmergency' => $signaturesByEmergency,
        ])->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        return $pdf->stream('formulario005_pdf.pdf');
    }
    public function store_ordenes(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = $request['procedimiento'];
            $pac             = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $id_asiento_cabecera = 0;
            $factura_venta = [
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'],
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'estado_pago'         => '0',
                'id_paciente'         => $request['identificacion_paciente'],
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['total1'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'ci_vendedor'         => $request['cedula_vendedor'],
                'vendedor'            => $request['vendedor'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['totalc'],
                'valor_contable'      => $request['total1'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_venta = Ct_Ven_Orden::insertGetId($factura_venta);
            //$id_venta = 0;
            $arr_total      = [];

            for ($i = 0; $i < count($request->input("nombre")); $i++) {
                if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                    $arr = [
                        'nombre'     => $request->input("nombre")[$i],
                        'cantidad'   => $request->input("cantidad")[$i],
                        'codigo'     => $request->input("codigo")[$i],
                        'precio'     => $request->input("precio")[$i],
                        'descpor'    => $request->input("descpor")[$i],
                        'copago'     => $request->input("copago")[$i],
                        'descuento'  => $request->input("desc")[$i],
                        'precioneto' => $request->input("precioneto")[$i],
                        'detalle'    => $request->input("descrip_prod")[$i],
                        'iva'        => $request->input("iva")[$i],

                    ];
                    array_push($arr_total, $arr);
                }
            }
            foreach ($arr_total as $valor) {
                $detalle = [
                    'id_ct_ven_orden'      => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['copago'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['precioneto'],
                    'check_iva'            => $valor['iva'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];

                Ct_Ven_Orden_Detalle::create($detalle);
            }

            return ['id' => $id_venta];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function detalle_planilla($id_proc)
    {
    }

}
