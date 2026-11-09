<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Equipo;
use Sis_medico\Equipo_Historia;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Log_movimiento;
use Sis_medico\ModuloBodega;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Planilla_Procedimiento;
use Sis_medico\Producto;
use Sis_medico\hc_procedimientos;

class V3NursingSupplyService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        return array(
            'desde' => $this->date($request->input('desde', $request->input('fecha', $today)), $today),
            'hasta' => $this->date($request->input('hasta', $request->input('fechafin', $today)), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'cedula' => trim((string) $request->input('cedula', '')),
        );
    }

    public function paginate(array $filters, $companyId)
    {
        $query = DB::table('agenda as a')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')
            ->leftJoin('sala as sa', 'sa.id', '=', 'a.id_sala')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->where('a.id_empresa', $companyId)->where('a.estado_cita', 4)->whereIn('a.proc_consul', array(1, 4));
        $this->applyFilters($query, $filters);
        return $query->select(array('a.*', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2', 'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2', 'p.fecha_nacimiento', 'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 's.nombre as insurance_name', 'sa.nombre_sala as room_name', 'pr.nombre as procedure_name'))
            ->orderBy('a.fechaini', 'desc')->paginate(25);
    }

    public function detail($agendaId, $companyId)
    {
        $agenda = DB::table('agenda as a')->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')
            ->leftJoin('sala as sa', 'sa.id', '=', 'a.id_sala')->where('a.id', $agendaId)->where('a.id_empresa', $companyId)
            ->where('a.estado_cita', 4)->whereIn('a.proc_consul', array(1, 4))
            ->select(array('a.*', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2', 'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2', 'p.fecha_nacimiento', 'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 's.nombre as insurance_name', 'sa.nombre_sala as room_name'))->first();
        if (!$agenda) abort(404, 'La atención no existe o no pertenece a la empresa activa.');

        $procedures = hc_procedimientos::query()->join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $agendaId)->select('hc_procedimientos.*')->orderBy('hc_procedimientos.id')->get();
        $procedureIds = $procedures->pluck('id')->all();
        $supplies = $procedureIds ? Movimiento_Paciente::with(array('movimiento.producto', 'usuario_crea', 'bodega'))
            ->whereIn('id_hc_procedimientos', $procedureIds)->orderBy('created_at', 'desc')->get()->groupBy('id_hc_procedimientos') : collect();
        $finalNames = $procedureIds ? DB::table('hc_procedimiento_final as f')->leftJoin('procedimiento as p', 'p.id', '=', 'f.id_procedimiento')
            ->whereIn('f.id_hc_procedimientos', $procedureIds)->select('f.id_hc_procedimientos', 'p.nombre')->get()->groupBy('id_hc_procedimientos') : collect();
        $history = Historiaclinica::where('id_agenda', $agendaId)->orderBy('hcid')->first();
        $equipment = $history ? Equipo_Historia::with(array('equipo', 'usuario_crea'))->where('hcid', $history->hcid)->orderBy('created_at', 'desc')->get() : collect();
        $warehouses = $this->warehouseQuery($companyId)->orderBy('nombre')->get();
        $selectedWarehouse = ModuloBodega::where('estado', 1)->where('id_modulo', 1)->where('id_usuario', Auth::id())
            ->where('id_empresa', $companyId)->orderBy('id', 'desc')->first();
        $plans = $procedureIds ? Planilla::whereIn('id_hc_procedimiento', $procedureIds)->where('estado', 1)
            ->orderBy('id', 'desc')->get()->unique('id_hc_procedimiento')->keyBy('id_hc_procedimiento') : collect();
        return array(
            'agenda' => $agenda,
            'procedures' => $procedures,
            'supplies' => $supplies,
            'finalNames' => $finalNames,
            'history' => $history,
            'equipment' => $equipment,
            'warehouses' => $warehouses,
            'selectedWarehouseId' => $selectedWarehouse ? (string) $selectedWarehouse->id_bodega : '',
            'plans' => $plans,
            'categoryLabels' => $this->categoryLabels(),
        );
    }

    public function storeWarehouse($agendaId, $warehouseId, $companyId)
    {
        $this->agenda($agendaId, $companyId);
        $warehouse = $this->warehouse($warehouseId, $companyId);
        return DB::transaction(function () use ($warehouse, $companyId) {
            $selections = ModuloBodega::where('id_modulo', 1)->where('id_usuario', Auth::id())
                ->where('id_empresa', $companyId)->lockForUpdate()->orderBy('id', 'desc')->get();
            $selection = $selections->first();
            if (!$selection) $selection = new ModuloBodega;
            $selection->id_modulo = 1;
            $selection->id_bodega = $warehouse->id;
            $selection->id_empresa = $companyId;
            $selection->id_usuario = Auth::id();
            $selection->estado = 1;
            if (!$selection->exists) {
                $selection->id_usuariocrea = Auth::id();
                $selection->ip_creacion = $this->ip();
            }
            $selection->id_usuariomod = Auth::id();
            $selection->ip_modificacion = $this->ip();
            $selection->save();
            if ($selections->count() > 1) {
                ModuloBodega::whereIn('id', $selections->slice(1)->pluck('id')->all())->update(array(
                    'estado' => 0,
                    'id_usuariomod' => Auth::id(),
                    'ip_modificacion' => $this->ip(),
                ));
            }
            return $warehouse;
        });
    }

    public function updateObservation($agendaId, $observation, $companyId)
    {
        $this->agenda($agendaId, $companyId);
        $history = $this->history($agendaId);
        $history->observaciones_enfermeria = trim((string) $observation);
        $history->save();
        return $history;
    }

    public function storeEquipment($agendaId, $code, $companyId)
    {
        $this->agenda($agendaId, $companyId);
        $history = $this->history($agendaId);
        $equipment = Equipo::where('serie', trim((string) $code))->where('estado', 1)->first();
        if (!$equipment) throw new \DomainException('No se encontró un equipo activo con ese código o serie.');
        return DB::transaction(function () use ($history, $equipment) {
            $duplicate = Equipo_Historia::where('hcid', $history->hcid)->where('id_equipo', $equipment->id)
                ->lockForUpdate()->first();
            if ($duplicate) throw new \DomainException('El equipo ya está registrado en esta atención.');
            return Equipo_Historia::create(array(
                'id_equipo' => $equipment->id,
                'hcid' => $history->hcid,
                'ip_creacion' => $this->ip(),
                'ip_modificacion' => $this->ip(),
                'id_usuariocrea' => Auth::id(),
                'id_usuariomod' => Auth::id(),
            ));
        });
    }

    public function destroyEquipment($agendaId, $equipmentHistoryId, $companyId)
    {
        $this->agenda($agendaId, $companyId);
        $history = $this->history($agendaId);
        $record = Equipo_Historia::where('id', $equipmentHistoryId)->where('hcid', $history->hcid)->first();
        if (!$record) throw new \DomainException('El equipo no pertenece a esta atención.');
        return DB::transaction(function () use ($record) { return $record->delete(); });
    }

    public function storeSupply(Request $request, $agendaId, $procedureId, $companyId)
    {
        $agenda = $this->agenda($agendaId, $companyId);
        $procedure = $this->procedure($agendaId, $procedureId);
        if ((int) $procedure->estado !== 1) throw new \DomainException('No se pueden registrar insumos en un procedimiento inactivo.');
        $this->assertOpenPlan($procedureId);
        $warehouse = $this->warehouse($request->input('id_bodega'), $companyId);
        $code = trim((string) $request->input('codigo'));
        if ($code === '') throw new \DomainException('Escriba o escanee el código del insumo.');

        return DB::transaction(function () use ($request, $agenda, $procedure, $warehouse, $code) {
            $series = $this->lockAvailableStock($code, $warehouse->id, $agenda->id_empresa);
            $legacyRequest = clone $request;
            $legacyRequest->merge(array(
                'codigo' => $series->serie,
                'id_bodega' => $warehouse->id,
                'id_hc_procedimientos' => $procedure->id,
                'id_agenda' => $agenda->id,
            ));
            $result = app(\Sis_medico\Http\Controllers\Insumos\TransitoController::class)->serie_enfermero($legacyRequest);
            if ((string) $result !== 'ok') {
                $message = trim((string) $result);
                throw new \DomainException($this->legacySupplyError($message));
            }
            $this->syncInventoryCommitment($series->fresh());
            return true;
        });
    }

    public function destroySupply($agendaId, $procedureId, $movementId, $companyId)
    {
        $agenda = $this->agenda($agendaId, $companyId);
        $this->procedure($agendaId, $procedureId);
        $this->assertOpenPlan($procedureId);
        return DB::transaction(function () use ($agenda, $procedureId, $movementId) {
            $movement = Movimiento_Paciente::with('movimiento.producto')->where('id', $movementId)
                ->where('id_hc_procedimientos', $procedureId)->lockForUpdate()->first();
            if (!$movement) throw new \DomainException('El insumo no pertenece a este procedimiento.');
            $detail = Planilla_Detalle::where('id_movimiento_paciente', $movement->id)->where('estado', 1)->lockForUpdate()->first();
            if (!$detail) throw new \DomainException('El detalle de la planilla ya no está disponible.');
            $detailPlan = Planilla::where('id', $detail->id_planilla_cabecera)->lockForUpdate()->first();
            if ($detailPlan && (int) $detailPlan->aprobado === 1) throw new \DomainException('La planilla está aprobada y ya no admite modificaciones.');
            $series = InvInventarioSerie::where('serie', $detail->serie)->where('id_bodega', $detail->id_bodega)
                ->where('id_empresa', $agenda->id_empresa)->where('estado', 1)->lockForUpdate()->first();
            if ($series) $this->releaseCommitment($series, max(1, (int) $detail->cantidad));
            $detail->estado = 0;
            $detail->check = 0;
            $detail->id_usuariomod = Auth::id();
            $detail->ip_modificacion = $this->ip();
            $detail->save();
            if ($movement->movimiento && $movement->movimiento->producto) {
                Log_movimiento::create(array(
                    'id_producto' => $movement->movimiento->producto->id,
                    'id_encargado' => Auth::id(),
                    'id_movimiento' => $movement->id_movimiento,
                    'observacion' => 'Insumo retirado de la atención desde V3',
                    'tipo' => 2,
                    'ip_creacion' => $this->ip(),
                    'ip_modificacion' => $this->ip(),
                    'id_usuariocrea' => Auth::id(),
                    'id_usuariomod' => Auth::id(),
                ));
            }
            $movement->delete();
            return true;
        });
    }

    public function moveSupply($agendaId, $procedureId, $movementId, $targetProcedureId, $companyId)
    {
        $agenda = $this->agenda($agendaId, $companyId);
        $source = $this->procedure($agendaId, $procedureId);
        $target = $this->procedure($agendaId, $targetProcedureId);
        if ((int) $source->id === (int) $target->id) throw new \DomainException('Seleccione un procedimiento diferente.');
        if ((int) $target->estado !== 1) throw new \DomainException('El procedimiento de destino está inactivo.');
        $this->assertOpenPlan($source->id);
        return DB::transaction(function () use ($agenda, $source, $target, $movementId) {
            $movement = Movimiento_Paciente::where('id', $movementId)->where('id_hc_procedimientos', $source->id)->lockForUpdate()->first();
            if (!$movement) throw new \DomainException('El insumo no pertenece al procedimiento de origen.');
            $detail = Planilla_Detalle::where('id_movimiento_paciente', $movement->id)->where('estado', 1)->lockForUpdate()->first();
            if (!$detail) throw new \DomainException('El detalle de la planilla ya no está disponible.');
            $sourcePlan = Planilla::where('id', $detail->id_planilla_cabecera)->lockForUpdate()->first();
            if ($sourcePlan && (int) $sourcePlan->aprobado === 1) throw new \DomainException('La planilla de origen ya está aprobada.');
            $targetPlan = $this->ensurePlan($agenda, $target);
            if ((int) $targetPlan->aprobado === 1) throw new \DomainException('La planilla de destino ya está aprobada.');
            $movement->id_hc_procedimientos = $target->id;
            $movement->id_usuariomod = Auth::id();
            $movement->ip_modificacion = $this->ip();
            $movement->save();
            $detail->id_planilla_cabecera = $targetPlan->id;
            $detail->id_usuariomod = Auth::id();
            $detail->ip_modificacion = $this->ip();
            $detail->save();
            return true;
        });
    }

    private function agenda($agendaId, $companyId)
    {
        $agenda = Agenda::where('id', $agendaId)->where('id_empresa', $companyId)
            ->where('estado_cita', 4)->whereIn('proc_consul', array(1, 4))->first();
        if (!$agenda) throw new \DomainException('La atención no existe, no está atendida o no pertenece a la empresa activa.');
        return $agenda;
    }

    private function history($agendaId)
    {
        $history = Historiaclinica::where('id_agenda', $agendaId)->orderBy('hcid')->first();
        if (!$history) throw new \DomainException('La atención todavía no tiene una historia clínica asociada.');
        return $history;
    }

    private function procedure($agendaId, $procedureId)
    {
        $procedure = hc_procedimientos::query()->join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $agendaId)->where('hc_procedimientos.id', $procedureId)
            ->select('hc_procedimientos.*')->first();
        if (!$procedure) throw new \DomainException('El procedimiento no pertenece a esta atención.');
        return $procedure;
    }

    private function warehouseQuery($companyId)
    {
        $query = Bodega::where('id_empresa', $companyId)->where('estado', 1);
        $user = Auth::user();
        if (!$user || !in_array((int) $user->id_tipo_usuario, array(1, 7), true)) $query->where('mostrar', 1);
        return $query;
    }

    private function warehouse($warehouseId, $companyId)
    {
        if (trim((string) $warehouseId) === '') throw new \DomainException('Seleccione la bodega de descargo.');
        $warehouse = $this->warehouseQuery($companyId)->where('id', $warehouseId)->first();
        if (!$warehouse) throw new \DomainException('La bodega no está activa o no está autorizada para este usuario.');
        return $warehouse;
    }

    private function assertOpenPlan($procedureId)
    {
        $plan = Planilla::where('id_hc_procedimiento', $procedureId)->where('estado', 1)->orderBy('id', 'desc')->first();
        if ($plan && (int) $plan->aprobado === 1) throw new \DomainException('La planilla está aprobada y ya no admite modificaciones.');
    }

    private function lockAvailableStock($code, $warehouseId, $companyId)
    {
        $series = InvInventarioSerie::where('serie', $code)->where('id_bodega', $warehouseId)
            ->where('id_empresa', $companyId)->where('estado', '<>', 0)->lockForUpdate()->first();
        if (!$series) {
            $product = Producto::where('codigo', $code)->where('estado', 1)->first();
            if ($product) {
                $series = InvInventarioSerie::where('id_producto', $product->id)->where('id_bodega', $warehouseId)
                    ->where('id_empresa', $companyId)->where('estado', '<>', 0);
                if ((int) $product->usos > 0) {
                    $series->whereRaw('(GREATEST(COALESCE(existencia_uso, 0), COALESCE(cantidad_usos_kardex, 0)) - COALESCE(comprometido_uso, 0)) > 0');
                } else {
                    $series->whereRaw('(COALESCE(existencia, 0) - COALESCE(comprometido, 0)) > 0');
                }
                $series = $series->orderByRaw('CASE WHEN fecha_vence >= ? THEN 0 WHEN fecha_vence IS NULL THEN 1 ELSE 2 END', array(date('Y-m-d')))
                    ->orderBy('fecha_vence')->orderBy('id')->lockForUpdate()->first();
            }
        }
        if (!$series) throw new \DomainException($this->stockSuggestion($code, $warehouseId, $companyId));
        $product = $series->producto;
        $available = $product && (int) $product->usos > 0
            ? max((float) $series->existencia_uso, (float) $series->cantidad_usos_kardex) - (float) $series->comprometido_uso > 0
            : (float) $series->existencia - (float) $series->comprometido > 0;
        if (!$available) throw new \DomainException('No hay stock disponible en la bodega seleccionada.');
        return $series;
    }

    private function stockSuggestion($code, $warehouseId, $companyId)
    {
        $product = Producto::where('codigo', $code)->first();
        $query = InvInventarioSerie::query()->join('bodega as b', 'b.id', '=', 'inv_inventario_serie.id_bodega')
            ->where('inv_inventario_serie.id_empresa', $companyId)->where('inv_inventario_serie.estado', '<>', 0)
            ->where('b.estado', 1)->where('b.id_empresa', $companyId)->where('b.id', '<>', $warehouseId)
            ->where(function ($stock) {
                $stock->whereRaw('(COALESCE(inv_inventario_serie.existencia, 0) - COALESCE(inv_inventario_serie.comprometido, 0)) > 0')
                    ->orWhereRaw('(GREATEST(COALESCE(inv_inventario_serie.existencia_uso, 0), COALESCE(inv_inventario_serie.cantidad_usos_kardex, 0)) - COALESCE(inv_inventario_serie.comprometido_uso, 0)) > 0');
            });
        if ($product) $query->where('inv_inventario_serie.id_producto', $product->id);
        else $query->where('inv_inventario_serie.serie', $code);
        $names = $query->distinct()->pluck('b.nombre')->all();
        return empty($names)
            ? 'No existe stock disponible para ese código en la bodega seleccionada.'
            : 'No hay stock en la bodega seleccionada. Disponible en: '.implode(', ', $names).'.';
    }

    private function releaseCommitment(InvInventarioSerie $series, $quantity)
    {
        $product = $series->producto;
        $uses = $product ? (int) $product->usos : 0;
        $series->comprometido_uso = max(0, (float) $series->comprometido_uso - $quantity);
        $series->comprometido = $uses > 0 ? (int) ceil($series->comprometido_uso / $uses) : (int) ceil($series->comprometido_uso);
        $series->save();
        if ($series->inventario) {
            $committedUses = (float) InvInventarioSerie::where('id_inv_inventario', $series->id_inv_inventario)->where('estado', 1)->sum('comprometido_uso');
            $series->inventario->comprometido_uso = $committedUses;
            $series->inventario->comprometido = $uses > 0 ? (int) ceil($committedUses / $uses) : (int) ceil($committedUses);
            $series->inventario->save();
        }
    }

    private function syncInventoryCommitment(InvInventarioSerie $series)
    {
        if (!$series || !$series->inventario) return;
        $uses = $series->producto ? (int) $series->producto->usos : 0;
        $committedUses = (float) InvInventarioSerie::where('id_inv_inventario', $series->id_inv_inventario)
            ->where('estado', 1)->sum('comprometido_uso');
        $series->inventario->comprometido_uso = $committedUses;
        $series->inventario->comprometido = $uses > 0 ? (int) ceil($committedUses / $uses) : (int) ceil($committedUses);
        $series->inventario->save();
    }

    private function ensurePlan(Agenda $agenda, hc_procedimientos $procedure)
    {
        $plan = Planilla::where('id_hc_procedimiento', $procedure->id)->where('estado', 1)->orderBy('id', 'desc')->first();
        if ($plan) return $plan;
        $mainProcedure = Hc_Procedimiento_Final::query()->join('procedimiento as p', 'p.id', '=', 'hc_procedimiento_final.id_procedimiento')
            ->where('hc_procedimiento_final.id_hc_procedimientos', $procedure->id)->whereNotNull('p.id_grupo_procedimiento')
            ->select('hc_procedimiento_final.id_procedimiento')->first();
        $template = $mainProcedure ? Planilla_Procedimiento::where('id_procedimiento', $mainProcedure->id_procedimiento)->first() : null;
        if (!$template) $template = Planilla_Procedimiento::where('id_procedimiento', 68)->first();
        if (!$template) throw new \DomainException('El procedimiento de destino no tiene una plantilla de insumos configurada.');
        $patient = DB::table('paciente')->where('id', $agenda->id_paciente)->first();
        $patientName = $patient ? trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2) : $agenda->id_paciente;
        return Planilla::create(array(
            'fecha' => date('Y-m-d H:i:s'),
            'id_planilla' => $template->id_planilla,
            'id_agenda' => $agenda->id,
            'id_movimiento' => null,
            'id_hc_procedimiento' => $procedure->id,
            'id_usuariocrea' => Auth::id(),
            'id_usuariomod' => Auth::id(),
            'ip_creacion' => $this->ip(),
            'ip_modificacion' => $this->ip(),
            'estado' => 1,
            'observacion' => 'Paciente: '.$patientName,
        ));
    }

    private function categoryLabels()
    {
        return array(1 => 'Honorarios médicos', 2 => 'Medicinas', 3 => 'Insumos', 4 => 'Laboratorio', 5 => 'Gastos administrativos', 6 => 'Imagen', 7 => 'Servicios institucionales', 8 => 'Equipos especiales', 0 => 'Otros');
    }

    private function legacySupplyError($message)
    {
        $messages = array(
            'NO EXISTE EN INVENTARIO SERIE' => 'El código o la serie no existe en el inventario de la empresa activa.',
            'NO HAY STOCK' => 'No hay stock disponible en la bodega seleccionada.',
            'caducado' => 'El lote encontrado está caducado y no puede registrarse.',
            'NO TIENE PLANTILLA' => 'El procedimiento no tiene una plantilla de insumos configurada.',
            'NO TIENE PROCEDIMIENTO PRINCIPAL' => 'El procedimiento no tiene un procedimiento principal configurado.',
            'NO EXISTE LA PLANILLA' => 'No se pudo crear o localizar la planilla del procedimiento.',
            'inconsistencia' => 'El inventario presenta una inconsistencia para el código ingresado.',
        );
        return isset($messages[$message]) ? $messages[$message]
            : ($message !== '' ? $message : 'No fue posible registrar el insumo.');
    }

    private function ip()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    private function applyFilters($query, array $filters)
    {
        $from = $filters['desde']; $to = $filters['hasta'];
        if ($from > $to) { $copy = $from; $from = $to; $to = $copy; }
        $query->whereBetween('a.fechaini', array($from.' 00:00:00', $to.' 23:59:59'));
        if ($filters['cedula'] !== '') $query->where('a.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        if ($filters['paciente'] !== '') foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
            if ($term === '') continue; $like = '%'.$this->escapeLike($term).'%';
            $query->where(function ($part) use ($like) { $part->where('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like); });
        }
    }

    private function date($value, $default) { $value = str_replace('/', '-', trim((string) $value)); $date = \DateTime::createFromFormat('Y-m-d', $value); return $date && $date->format('Y-m-d') === $value ? $value : $default; }
    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
