<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Support\RequestQueryCache;
use Sis_medico\Cama;
use Sis_medico\Empresa;
use Sis_medico\Sala;

class ClinicalHospitalDestinationService
{
    public static function areas()
    {
        return [2 => 'EMERGENCIA', 3 => 'HOSPITALIZACION', 5 => 'CIRUGIA', 6 => 'UCI', 7 => 'UCIN'];
    }

    public function available()
    {
        return Schema::hasTable('clinical_hospital_destinations')
            && Schema::hasTable('clinical_hospital_bed_locations');
    }

    public function companyId()
    {
        $id = session('id_empresa');
        $active = $id && RequestQueryCache::remember(__METHOD__.':active:'.$id, function () use ($id) {
            return Empresa::where('id', $id)->where('estado', 1)->exists();
        }, null, false, ['empresa']);
        abort_unless($active, 403,
            'Seleccione una empresa activa.');
        abort_unless(app(TenantContext::class)->availableFor(auth()->user())->contains(function ($company) use ($id) {
            return (string) $company->id === (string) $id;
        }), 403, 'No tiene acceso a la empresa seleccionada.');
        return (string) $id;
    }

    public function salas(array $steps = [2, 3, 5, 6, 7])
    {
        $query = $this->salasQuery($steps);
        if (!$query) return collect();
        $salas = $query->orderBy('sala.nombre_sala')->get(['sala.*', 'd.step as hospital_step',
                'h.nombre_hospital', 'h.direccion as direccion_hospital']);
        return $salas->each(function ($sala) {
            $sala->nombre_sala = function_exists('mb_strtoupper')
                ? mb_strtoupper($sala->nombre_sala, 'UTF-8') : strtoupper($sala->nombre_sala);
        });
    }

    /** Destinos autorizados como subconsulta; null si falta la configuración hospitalaria. */
    public function salasQuery(array $steps = [2, 3, 5, 6, 7])
    {
        $companyId = $this->companyId();
        if (!$this->available()) return null;
        return Sala::join('clinical_hospital_destinations as d', 'd.sala_id', '=', 'sala.id')
            ->join('hospital as h', 'h.id', '=', 'sala.id_hospital')
            ->where('d.company_id', $companyId)->whereIn('d.step', $steps)
            ->whereColumn('d.hospital_id', 'sala.id_hospital')
            ->where('sala.estado', 1)->where('h.estado', 1);
    }

    public function assertSala($salaId, $step, $companyId = null)
    {
        $active = $this->companyId();
        abort_unless($companyId === null || (string) $companyId === $active, 403,
            'El destino no corresponde a la empresa activa.');
        if (!$this->available()) throw new \InvalidArgumentException('Configure los destinos hospitalarios antes de ingresar o trasladar pacientes.');
        $sala = $this->salas([(int) $step])->first(function ($sala) use ($salaId) {
            return (string) $sala->id === (string) $salaId;
        });
        if (!$sala) throw new \InvalidArgumentException('La sala no esta configurada para el area y la empresa seleccionadas.');
        return $sala;
    }

    public function roomIds($salaId = null)
    {
        $companyId = $this->companyId();
        if (!$this->available()) return collect();
        $query = DB::table('clinical_hospital_bed_locations')->where('company_id', $companyId);
        if ($salaId !== null) $query->where('sala_id', $salaId);
        return $query->pluck('habitacion_id');
    }

    public function assertBed(Cama $bed, $salaId = null)
    {
        if (!$bed->habitacion || (int) $bed->habitacion->estado === 4) {
            throw new \InvalidArgumentException('La habitacion de la cama no esta disponible.');
        }
        if (!$this->roomIds($salaId)->contains($bed->id_habitacion)) {
            throw new \InvalidArgumentException('La cama no pertenece al destino hospitalario configurado para este traslado.');
        }
        return $bed;
    }

    /** Revalida la selección del formulario; la ocupación se confirma en recepción. */
    public function assertAvailableBed($bedId, $salaId)
    {
        $bed = Cama::with('habitacion.piso')->find($bedId);
        if (!$bed) throw new \InvalidArgumentException('La cama seleccionada ya no existe. Actualice la pantalla.');
        $this->assertBed($bed, $salaId);
        $transaction = \Sis_medico\CamaTransaccion::where('id_cama', $bed->id)->orderByDesc('id')->first();
        if ((int) $bed->estado !== 1 || !$transaction || $transaction->estado_cama !== 'disponible'
            || $transaction->id_paciente
            || \Sis_medico\Ho_Hospitalizacion::where('id_cama', $bed->id)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('La cama seleccionada ya no está disponible. Actualice y seleccione otra cama.');
        }
        return $bed;
    }

    public function configureSala($salaId, $step, $actorId)
    {
        if (!isset(self::areas()[(int) $step]) || !$this->available()) {
            throw new \InvalidArgumentException('El area o las migraciones hospitalarias no estan disponibles.');
        }
        $companyId = $this->companyId();
        return DB::transaction(function () use ($salaId, $step, $actorId, $companyId) {
            $sala = Sala::where('id', $salaId)->where('estado', 1)->lockForUpdate()->firstOrFail();
            $hospitalId = app(CurrentHospitalResolver::class)->id(['company_id' => $companyId]);
            if (!$hospitalId || (int) $sala->id_hospital !== (int) $hospitalId) {
                throw new \InvalidArgumentException('La sala debe pertenecer al hospital configurado para la empresa activa.');
            }
            $existing = DB::table('clinical_hospital_destinations')->where('company_id', $companyId)
                ->where('sala_id', $salaId)->lockForUpdate()->first();
            if ($existing && (int) $existing->step !== (int) $step
                && DB::table('ho_traspaso_sala008')->where('id_sala', $salaId)->whereIn('estado', [1, 2])->exists()) {
                throw new \InvalidArgumentException('No cambie el area de una sala con pacientes o traslados vigentes.');
            }
            $now = date('Y-m-d H:i:s');
            DB::table('clinical_hospital_destinations')->updateOrInsert(
                ['company_id' => $companyId, 'sala_id' => $salaId],
                ['hospital_id' => $hospitalId, 'step' => $step, 'updated_by' => $actorId,
                    'created_at' => $existing ? $existing->created_at : $now, 'updated_at' => $now]
            );
        });
    }

    public function configureRoom($roomId, $salaId, $actorId)
    {
        $companyId = $this->companyId();
        $sala = $this->salas([2, 3, 6, 7])->firstWhere('id', $salaId);
        if (!$sala) throw new \InvalidArgumentException('Seleccione un destino de camas configurado.');
        return DB::transaction(function () use ($roomId, $salaId, $actorId, $companyId) {
            \Sis_medico\Habitacion::where('id', $roomId)->lockForUpdate()->firstOrFail();
            $beds = Cama::where('id_habitacion', $roomId)->orderBy('id')->lockForUpdate()->pluck('id');
            $existing = DB::table('clinical_hospital_bed_locations')->where('habitacion_id', $roomId)->lockForUpdate()->first();
            if ($existing && (string) $existing->company_id !== $companyId) {
                throw new \InvalidArgumentException('La habitacion ya pertenece a otra empresa.');
            }
            $active = \Sis_medico\Ho_Hospitalizacion::whereIn('id_cama', $beds)->where('estado', 1)->get();
            if ($active->isNotEmpty() && (!$existing || (int) $existing->sala_id !== (int) $salaId)) {
                $matches = 0;
                if (!$existing && Schema::hasTable('clinical_encounters') && Schema::hasTable('ho_solicitud')) {
                    $matches = DB::table('ho_hospitalizacion as stay')
                        ->join('ho_traspaso_sala008 as transfer', 'transfer.id', '=', 'stay.id_traspaso')
                        ->join('ho_solicitud as root', 'root.id', '=', 'transfer.id_solicitud')
                        ->join('clinical_encounters as encounter', 'encounter.id', '=', 'root.encounter_id')
                        ->whereIn('stay.id', $active->pluck('id'))->where('transfer.id_sala', $salaId)
                        ->where('transfer.estado', 2)->where('root.estado', 1)->whereNull('root.fecha_salida')
                        ->where('encounter.id_empresa', $companyId)->whereNull('encounter.ended_at')
                        ->whereColumn('encounter.id_paciente', 'stay.id_paciente')->count();
                }
                if ($matches !== $active->count()) {
                    throw new \InvalidArgumentException('No reasigne habitaciones con pacientes activos. La asignacion inicial debe coincidir con todos sus ingresos vigentes.');
                }
            }
            $now = date('Y-m-d H:i:s');
            DB::table('clinical_hospital_bed_locations')->updateOrInsert(['habitacion_id' => $roomId],
                ['company_id' => $companyId, 'sala_id' => $salaId, 'updated_by' => $actorId,
                    'created_at' => $existing ? $existing->created_at : $now, 'updated_at' => $now]);
        });
    }
}
