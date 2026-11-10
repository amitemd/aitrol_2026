<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->backfillRoomCompaniesFromAppointments();
        $this->backfillHospitalCompanies();
        $this->backfillRoomsFromHospitals();
        $this->normalizeDefaultHospitals();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // El propietario inferido forma parte de los datos operativos. No se
        // borra durante un rollback porque no es posible restaurarlo sin
        // volver a mezclar informacion de distintas empresas.
    }

    private function backfillRoomCompaniesFromAppointments(): void
    {
        if (! $this->canScopeAppointmentsToRooms()) {
            return;
        }

        DB::table('sala')
            ->where(function ($query): void {
                $query->whereNull('id_empresa')->orWhere('id_empresa', '');
            })
            ->orderBy('id')
            ->chunkById(200, function (Collection $rooms): void {
                foreach ($rooms as $room) {
                    $companies = DB::table('agenda')
                        ->where('id_sala', $room->id)
                        ->whereNotNull('id_empresa')
                        ->where('id_empresa', '<>', '')
                        ->distinct()
                        ->pluck('id_empresa')
                        ->map(fn ($companyId): string => trim((string) $companyId))
                        ->filter()
                        ->unique()
                        ->values();

                    if ($companies->count() === 1) {
                        DB::table('sala')->where('id', $room->id)->update([
                            'id_empresa' => $companies->first(),
                        ]);
                    }
                }
            }, 'id');
    }

    private function backfillHospitalCompanies(): void
    {
        if (! Schema::hasTable('hospital') || ! Schema::hasColumn('hospital', 'id_empresa')) {
            return;
        }

        DB::table('hospital')->orderBy('id')->chunkById(100, function (Collection $hospitals): void {
            foreach ($hospitals as $hospital) {
                $owner = trim((string) ($hospital->id_empresa ?? ''));

                if ($owner === '') {
                    $candidates = $this->hospitalCompanyCandidates((int) $hospital->id);

                    if ($candidates->count() === 1) {
                        $owner = (string) $candidates->first();
                        DB::table('hospital')->where('id', $hospital->id)->update([
                            'id_empresa' => $owner,
                        ]);
                    }
                }

                $this->synchronizeHospitalContext((int) $hospital->id, $owner);
            }
        }, 'id');
    }

    private function backfillRoomsFromHospitals(): void
    {
        if (! Schema::hasTable('hospital') || ! Schema::hasTable('sala')
            || ! Schema::hasColumn('hospital', 'id_empresa')
            || ! Schema::hasColumn('sala', 'id_empresa')) {
            return;
        }

        DB::table('hospital')
            ->whereNotNull('id_empresa')
            ->where('id_empresa', '<>', '')
            ->orderBy('id')
            ->chunkById(100, function (Collection $hospitals): void {
                foreach ($hospitals as $hospital) {
                    DB::table('sala')
                        ->where('id_hospital', $hospital->id)
                        ->where(function ($query): void {
                            $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                        })
                        ->update(['id_empresa' => $hospital->id_empresa]);
                }
            }, 'id');
    }

    private function normalizeDefaultHospitals(): void
    {
        if (! Schema::hasTable('company_hospital_contexts')
            || ! Schema::hasTable('hospital')
            || ! Schema::hasColumn('hospital', 'id_empresa')) {
            return;
        }

        $companies = DB::table('company_hospital_contexts')
            ->where('active', 1)
            ->whereNotNull('company_id')
            ->where('company_id', '<>', '')
            ->distinct()
            ->pluck('company_id');

        foreach ($companies as $companyId) {
            $validHospitalIds = DB::table('company_hospital_contexts as context')
                ->join('hospital', 'hospital.id', '=', 'context.hospital_id')
                ->where('context.company_id', $companyId)
                ->where('context.active', 1)
                ->where('hospital.id_empresa', $companyId)
                ->orderByDesc('context.is_default')
                ->orderBy('context.hospital_id')
                ->pluck('context.hospital_id');

            DB::table('company_hospital_contexts')
                ->where('company_id', $companyId)
                ->where('active', 1)
                ->update(['is_default' => 0, 'updated_at' => now()]);

            if ($validHospitalIds->isNotEmpty()) {
                DB::table('company_hospital_contexts')
                    ->where('company_id', $companyId)
                    ->where('hospital_id', $validHospitalIds->first())
                    ->update(['is_default' => 1, 'updated_at' => now()]);
            }
        }
    }

    private function hospitalCompanyCandidates(int $hospitalId): Collection
    {
        $candidates = collect();

        if (Schema::hasTable('sala') && Schema::hasColumn('sala', 'id_empresa')) {
            $candidates = $candidates->merge(
                DB::table('sala')
                    ->where('id_hospital', $hospitalId)
                    ->whereNotNull('id_empresa')
                    ->where('id_empresa', '<>', '')
                    ->distinct()
                    ->pluck('id_empresa')
            );
        }

        if ($this->canScopeAppointmentsToRooms()) {
            $candidates = $candidates->merge(
                DB::table('agenda as appointment')
                    ->join('sala as room', 'room.id', '=', 'appointment.id_sala')
                    ->where('room.id_hospital', $hospitalId)
                    ->whereNotNull('appointment.id_empresa')
                    ->where('appointment.id_empresa', '<>', '')
                    ->distinct()
                    ->pluck('appointment.id_empresa')
            );
        }

        if (Schema::hasTable('company_hospital_contexts')) {
            $candidates = $candidates->merge(
                DB::table('company_hospital_contexts')
                    ->where('hospital_id', $hospitalId)
                    ->where('active', 1)
                    ->whereNotNull('company_id')
                    ->where('company_id', '<>', '')
                    ->distinct()
                    ->pluck('company_id')
            );
        }

        return $candidates
            ->map(fn ($companyId): string => trim((string) $companyId))
            ->filter()
            ->unique()
            ->values();
    }

    private function synchronizeHospitalContext(int $hospitalId, string $owner): void
    {
        if (! Schema::hasTable('company_hospital_contexts')) {
            return;
        }

        if ($owner === '') {
            DB::table('company_hospital_contexts')
                ->where('hospital_id', $hospitalId)
                ->update(['active' => 0, 'is_default' => 0, 'updated_at' => now()]);

            return;
        }

        DB::table('company_hospital_contexts')
            ->where('hospital_id', $hospitalId)
            ->where('company_id', '<>', $owner)
            ->update(['active' => 0, 'is_default' => 0, 'updated_at' => now()]);

        $context = DB::table('company_hospital_contexts')
            ->where('company_id', $owner)
            ->where('hospital_id', $hospitalId)
            ->first();

        if ($context === null) {
            DB::table('company_hospital_contexts')->insert([
                'company_id' => $owner,
                'hospital_id' => $hospitalId,
                'is_default' => 0,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        DB::table('company_hospital_contexts')->where('id', $context->id)->update([
            'active' => 1,
            'updated_at' => now(),
        ]);
    }

    private function canScopeAppointmentsToRooms(): bool
    {
        return Schema::hasTable('agenda')
            && Schema::hasTable('sala')
            && Schema::hasColumn('agenda', 'id_empresa')
            && Schema::hasColumn('agenda', 'id_sala')
            && Schema::hasColumn('sala', 'id_hospital');
    }
};
