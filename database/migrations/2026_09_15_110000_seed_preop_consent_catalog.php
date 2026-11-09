<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedPreopConsentCatalog extends Migration
{
    const SOURCE = 'V3-PREOP-CONSENT';

    public function up()
    {
        if (! Schema::hasTable('formato_consentimiento')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $items = [
            [
                'nombre' => 'CONSENTIMIENTO INFORMADO DEL PACIENTE',
                'consiste' => 'Autorización informada otorgada directamente por el paciente para el procedimiento indicado.',
            ],
            [
                'nombre' => 'CONSENTIMIENTO INFORMADO DEL REPRESENTANTE',
                'consiste' => 'Autorización informada otorgada por el representante autorizado del paciente.',
            ],
            [
                'nombre' => 'CONSENTIMIENTO INFORMADO PARA PROCEDIMIENTO QUIRÚRGICO',
                'consiste' => 'Autorización informada para realizar el procedimiento quirúrgico explicado por el profesional tratante.',
            ],
        ];

        foreach ($items as $item) {
            $exists = DB::table('formato_consentimiento')
                ->where('nombre', $item['nombre'])
                ->where('auditoria', self::SOURCE)
                ->exists();

            if ($exists) {
                DB::table('formato_consentimiento')
                    ->where('nombre', $item['nombre'])
                    ->where('auditoria', self::SOURCE)
                    ->update(['estado' => 1, 'updated_at' => $now]);

                continue;
            }

            DB::table('formato_consentimiento')->insert([
                'id_procedimiento' => null,
                'nombre' => $item['nombre'],
                'estado' => 1,
                'auditoria' => self::SOURCE,
                'consiste' => $item['consiste'],
                'realiza' => 'El procedimiento será realizado por personal clínico autorizado y conforme a los protocolos institucionales.',
                'duracion_estimada' => 'La duración depende del procedimiento y de la condición clínica del paciente.',
                'beneficios' => 'Realizar el tratamiento indicado y atender la condición clínica identificada.',
                'riesgos_frecuentes' => 'Dolor, sangrado, infección, reacciones a medicamentos o molestias transitorias.',
                'riesgos_infrecuentes' => 'Complicaciones que pueden requerir tratamiento adicional o intervención especializada.',
                'riesgos_adicionales' => 'El profesional debe explicar los riesgos particulares antes de solicitar la firma.',
                'alternativas' => 'Observación, tratamiento médico u otras alternativas disponibles según criterio profesional.',
                'manejo' => 'La institución aplicará los protocolos vigentes para prevenir y tratar complicaciones.',
                'consecuencias' => 'No realizar el procedimiento puede retrasar el diagnóstico o tratamiento recomendado.',
                'check_labs' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'ip_creacion' => '127.0.0.1',
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('formato_consentimiento')) {
            DB::table('formato_consentimiento')->where('auditoria', self::SOURCE)->delete();
        }
    }
}
