<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedV3AgendaDocuments extends Migration
{
    public function up()
    {
        $user = DB::table('users')->where('estado', 1)->orderBy('id')->first();
        $role = DB::table('tipousuario')->where('estado', 1)
            ->where('nombre', 'like', '%DOCTOR%')->orderBy('id')->first();

        // En una instalación nueva los usuarios se crean en la fase de seeds,
        // después de aplicar todas las migraciones. La carga se repetirá de
        // forma idempotente desde V3ClinicalDocumentsSeeder cuando ya existan.
        if (! $user || ! $role) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $documents = [
            ['V3_ID_PAC', 'COPIA DE IDENTIFICACIÓN DEL PACIENTE', 4, null],
            ['V3_ID_ACO', 'COPIA DE IDENTIFICACIÓN DEL ACOMPAÑANTE', 0, null],
            ['V3_REF_053', 'FORMULARIO 053 REFERENCIA O DERIVACIÓN', 0, null],
            ['V3_VAL', 'CÓDIGO DE VALIDACIÓN', 0, null],
            ['V3_ACT_ENT', 'ACTA DE ENTREGA', 0, 'acta_servicio'],
            ['V3_CON_INF', 'CONSENTIMIENTO INFORMADO', 0, 'consentimiento_general'],
            ['V3_F007', 'FORMULARIO 007', 0, null],
            ['V3_F012', 'FORMULARIO 012 ORDEN DE PROCEDIMIENTO', 0, null],
            ['V3_CON_PRO', 'CONSENTIMIENTOS DE PROCEDIMIENTOS', 0, null],
            ['V3_SOL_AT', 'SOLICITUD DE ATENCIÓN', 0, 'solicitud_atencion'],
            ['V3_COB_PAC', 'COBERTURA DE SALUD DEL PACIENTE', 1, null],
            ['V3_COB_PRI', 'COBERTURA DE SALUD PRINCIPAL', 3, null],
            ['V3_INF_MED', 'ACTA DE ENTREGA DE INFORME MÉDICO', 0, 'entrega_informe'],
            ['V3_SEG_PRI', 'CARTA DE SEGURO PRIVADO', 0, 'financiamiento_privado'],
        ];

        foreach ($documents as $sequence => $item) {
            $values = [
                'secuencia' => $sequence + 1,
                'nombre' => $item[1].' (*)',
                'nombre_corto' => $item[1],
                'proc_consul' => 2,
                'tipo_seguro' => null,
                'id_seguro' => null,
                'id_subseguro' => null,
                'id_tipo_usuario' => $role->id,
                'id_sala' => null,
                'est_doc_tarea' => 0,
                'id_empresa' => null,
                'link' => null,
                'msp' => $item[2],
                'estado' => 1,
                'formato' => $item[3],
                'principal' => null,
                'rpt_prin' => null,
                'sistema' => $item[3] ? 1 : 0,
                'id_usuariomod' => $user->id,
                'ip_modificacion' => '127.0.0.1',
                'updated_at' => $now,
            ];

            $existing = DB::table('documento')->where('tipo_documento', $item[0])->first();
            if ($existing) {
                DB::table('documento')->where('id', $existing->id)->update($values);

                continue;
            }

            DB::table('documento')->insert(array_merge($values, [
                'tipo_documento' => $item[0],
                'id_usuariocrea' => $user->id,
                'ip_creacion' => '127.0.0.1',
                'created_at' => $now,
            ]));
        }
    }

    public function down()
    {
        DB::table('documento')->whereIn('tipo_documento', [
            'V3_ID_PAC', 'V3_ID_ACO', 'V3_REF_053', 'V3_VAL', 'V3_ACT_ENT', 'V3_CON_INF', 'V3_F007',
            'V3_F012', 'V3_CON_PRO', 'V3_SOL_AT', 'V3_COB_PAC', 'V3_COB_PRI', 'V3_INF_MED', 'V3_SEG_PRI',
        ])->delete();
    }
}
