<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Convenio;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_detalle;
use Sis_medico\Ho_Orden_Examenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Log_usuario;
use Sis_medico\Seguro;

class EmergencyLaboratoryOrderService
{
    public function create($requestId, $publicOrder, $actorId, $ip)
    {
        return DB::transaction(function () use ($requestId, $publicOrder, $actorId, $ip) {
            $request = Ho_Solicitud::where('id', $requestId)->lockForUpdate()->firstOrFail();
            $encounter = $request->encounter_id ? \Sis_medico\ClinicalEncounter::find($request->encounter_id) : null;
            $company = $request->encounter_id
                ? ($encounter ? Empresa::find($encounter->id_empresa) : null)
                : Empresa::where('prioridad', 1)->first();
            if (!$company) {
                throw new \DomainException('No existe una empresa válida para crear la orden de este episodio.');
            }

            $insurance = Seguro::find($request->id_seguro);
            $insuranceId = null;
            $level = null;
            if ($publicOrder) {
                $agreement = null;
                if ($insurance && (int) $insurance->tipo === 0) {
                    $agreement = Convenio::where('id_empresa', $company->id)
                        ->where('id_seguro', $insurance->id)->whereNotNull('id_nivel')->first();
                }
                if (!$agreement) {
                    $agreement = Convenio::where('id_empresa', $company->id)
                        ->whereNotNull('id_nivel')
                        ->whereHas('seguro', function ($query) {
                            $query->where('tipo', 0);
                        })->orderBy('id')->first();
                }
                if (!$agreement) {
                    throw new \DomainException('No existe un convenio público activo con nivel tarifario para la empresa del episodio. Configúralo antes de crear la orden.');
                }
                $insuranceId = $agreement->id_seguro;
                $level = $agreement->id_nivel;
            } else {
                if ($insurance && (int) $insurance->tipo !== 0) {
                    $insuranceId = $insurance->id;
                } else {
                    $privateInsurance = Seguro::where('tipo', '<>', 0)->orderBy('id')->first();
                    if (!$privateInsurance) {
                        throw new \DomainException('No existe un seguro privado activo para crear la orden.');
                    }
                    $insuranceId = $privateInsurance->id;
                }
            }

            $linkedOrderIds = Ho_Orden_Examenes::where('id_ho_solicitud', $requestId)
                ->pluck('id_examen_orden')->all();
            if (!empty($linkedOrderIds)) {
                $existingDraft = Examen_Orden::whereIn('id', $linkedOrderIds)
                    ->where('id_paciente', $request->id_paciente)->where('id_empresa', $company->id)
                    ->where('id_seguro', $insuranceId)->where('estado', '-1')
                    ->lockForUpdate()->orderBy('id', 'desc')->first();
                if ($existingDraft) {
                    return $existingDraft;
                }
            }

            $order = Examen_Orden::create(array(
                'id_paciente' => $request->id_paciente,
                'anio' => date('Y'),
                'mes' => date('m'),
                'id_seguro' => $insuranceId,
                'id_nivel' => $level,
                'est_amb_hos' => in_array((int) $request->estado_paso, [3, 5, 6, 7], true) ? 1 : 0,
                'id_doctor_ieced' => $actorId,
                'id_empresa' => $company->id,
                'cantidad' => 0,
                'valor' => 0,
                'total_valor' => 0,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'fecha_orden' => date('Y-m-d H:i:s'),
                'estado' => '-1',
            ));
            Ho_Orden_Examenes::create(array(
                'id_examen_orden' => $order->id,
                'id_ho_solicitud' => $requestId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
            ));
            app(ClinicalOrderService::class)->syncSolicitud($requestId);
            return $order;
        });
    }

    public function cancel($orderId, $actorId, $ip)
    {
        return DB::transaction(function () use ($orderId, $actorId, $ip) {
            $order = Examen_Orden::where('id', $orderId)->lockForUpdate()->firstOrFail();
            $order->estado = 0;
            $order->ip_modificacion = $ip;
            $order->id_usuariomod = $actorId;
            $order->save();
            $this->syncRequest($orderId);
            return $order;
        });
    }

    public function addExam($orderId, $examId, $actorId, $ip)
    {
        return DB::transaction(function () use ($orderId, $examId, $actorId, $ip) {
            $order = Examen_Orden::where('id', $orderId)->lockForUpdate()->firstOrFail();
            $detail = Examen_detalle::where('id_examen_orden', $orderId)->where('id_examen', $examId)
                ->lockForUpdate()->first();
            if (!$detail) {
                $exam = Examen::findOrFail($examId);
                $this->markEmergencyForm($orderId, $exam);
                $value = $exam->valor;
                $covered = 'NO';
                $level = Examen_Nivel::where('id_examen', $examId)->where('nivel', $order->id_nivel)->first();
                if ($level && $level->valor1 != 0) {
                    $value = $level->valor1;
                    $covered = 'SI';
                }
                Examen_detalle::create(array(
                    'id_examen_orden' => $orderId,
                    'id_examen' => $examId,
                    'valor' => $value,
                    'cubre' => $covered,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'cobrar_pac_pct' => $order->cobrar_pac_pct,
                    'valor_con_oda' => 0,
                ));
                $order = $this->recalculate($order, $actorId, $ip, false);
                $this->log($order, $examId, $actorId, $ip);
            }
            $this->syncRequest($orderId);
            return $this->totals($order->fresh());
        });
    }

    public function removeExam($orderId, $examId, $actorId, $ip)
    {
        return DB::transaction(function () use ($orderId, $examId, $actorId, $ip) {
            $order = Examen_Orden::where('id', $orderId)->lockForUpdate()->firstOrFail();
            $detail = Examen_detalle::where('id_examen_orden', $orderId)->where('id_examen', $examId)
                ->lockForUpdate()->first();
            if ($detail) {
                $detail->delete();
                $order = $this->recalculate($order, $actorId, $ip, true);
                $this->log($order, $examId, $actorId, $ip);
            }
            $this->syncRequest($orderId);
            return $this->totals($order->fresh());
        });
    }

    public function updatePatientHistory($requestId, array $data)
    {
        return DB::transaction(function () use ($requestId, $data) {
            $request = Ho_Solicitud::where('id', $requestId)->lockForUpdate()->firstOrFail();
            $patient = $request->paciente;
            $patient->ho_datos_paciente->update(array(
                'clinico' => isset($data['clinicos']) ? $data['clinicos'] : null,
                'ginecologico' => isset($data['ginecologico']) ? $data['ginecologico'] : null,
                'traumatologico' => isset($data['traumatologicos']) ? $data['traumatologicos'] : null,
                'farmacologico' => isset($data['farmacologico']) ? $data['farmacologico'] : null,
                'psiquiatrico' => isset($data['psiquiatrico']) ? $data['psiquiatrico'] : null,
            ));
            $patient->update(array(
                'antecedentes_quir' => isset($data['antecedentes_quir']) ? $data['antecedentes_quir'] : null,
                'antecedentes_fam' => isset($data['antecedentes_fam']) ? $data['antecedentes_fam'] : null,
            ));
            return $request->fresh();
        });
    }

    private function recalculate(Examen_Orden $order, $actorId, $ip, $resetDiscount)
    {
        $order = $order->fresh();
        $total = max(0, round($order->detalles->sum('valor'), 2));
        $discount = $resetDiscount ? 0 : $order->detalles->sum('valor_descuento');
        $surcharge = round(($total - $discount) * $order->recargo_p / 100, 2);
        $oda = round($order->detalles->sum('valor_con_oda'), 2);
        $order->update(array(
            'descuento_valor' => $discount,
            'recargo_valor' => $surcharge,
            'total_valor' => round($total - $discount + $surcharge, 2),
            'cantidad' => $order->detalles->count(),
            'valor' => $total,
            'ip_modificacion' => $ip,
            'id_usuariomod' => $actorId,
            'valor_con_oda' => $oda,
            'total_con_oda' => round($oda + $surcharge, 2),
        ));
        return $order;
    }

    private function markEmergencyForm($orderId, Examen $exam)
    {
        $link = Ho_Orden_Examenes::where('id_examen_orden', $orderId)->first();
        if (!$link) return;
        $request = Ho_Solicitud::find($link->id_ho_solicitud);
        if (!$request || (string) $request->estado_paso !== '1' || !$request->form008->count()) return;
        $field = ($exam->id == 1184 || $exam->id == 101) ? 'biometria'
            : ($exam->maquina == 2 ? 'quimica_sanguinea' : ($exam->id == 758 ? 'gasometria' : null));
        if ($field) $request->form008->first()->update(array($field => 'X'));
    }

    private function syncRequest($orderId)
    {
        $link = Ho_Orden_Examenes::where('id_examen_orden', $orderId)->first();
        if ($link) app(ClinicalOrderService::class)->syncSolicitud($link->id_ho_solicitud);
    }

    private function log(Examen_Orden $order, $examId, $actorId, $ip)
    {
        Log_usuario::create(array('id_usuario' => $actorId, 'ip_usuario' => $ip,
            'descripcion' => 'ACTUALIZA ORDEN PUBLICA', 'dato_ant1' => $order->id,
            'dato1' => $order->id_paciente, 'dato_ant4' => $examId));
    }

    private function totals(Examen_Orden $order)
    {
        return array('cantidad' => $order->cantidad, 'valor' => $order->valor,
            'descuento_valor' => $order->descuento_valor, 'recargo_valor' => $order->recargo_valor,
            'total_valor' => $order->total_valor);
    }
}
