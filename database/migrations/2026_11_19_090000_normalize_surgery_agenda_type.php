<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeSurgeryAgendaType extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('agenda') || ! Schema::hasColumn('agenda', 'proc_consul')) {
            return;
        }
        $hasHospitalType = Schema::hasColumn('agenda', 'ho_tipo');
        $hasSurgeryDetail = Schema::hasTable('agenda_cirugias');
        if (! $hasHospitalType && ! $hasSurgeryDetail) {
            return;
        }

        $agendas = DB::table('agenda')->where(function ($surgery) use ($hasHospitalType, $hasSurgeryDetail) {
            if ($hasHospitalType) {
                $surgery->where('ho_tipo', 5);
            }
            if ($hasSurgeryDetail) {
                $method = $hasHospitalType ? 'orWhereExists' : 'whereExists';
                $surgery->{$method}(function ($detail) {
                    $detail->select(DB::raw(1))->from('agenda_cirugias as surgery_detail')
                        ->whereColumn('surgery_detail.id_agenda', 'agenda.id');
                });
            }
        })->where(function ($type) {
            $type->whereNull('proc_consul')->orWhere('proc_consul', '<>', 5);
        });

        $values = ['proc_consul' => 5];
        if (Schema::hasColumn('agenda', 'updated_at')) {
            $values['updated_at'] = date('Y-m-d H:i:s');
        }
        $agendas->update($values);
    }

    public function down()
    {
        // No se revierte: no es posible distinguir con seguridad el tipo
        // incorrecto que tenía cada cirugía antes de normalizarla.
    }
}
