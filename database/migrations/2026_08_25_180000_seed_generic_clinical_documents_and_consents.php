<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedGenericClinicalDocumentsAndConsents extends Migration
{
    public function up()
    {
        $user = DB::table('users')->where('estado', 1)->orderBy('id')->first();
        $role = DB::table('tipousuario')->where('estado', 1)->where('nombre', 'like', '%DOCTOR%')->orderBy('id')->first();
        if (! $user || ! $role) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $documents = [
            ['V3_ID_PAC', 'COPIA DE IDENTIFICACIÓN DEL PACIENTE', 4],
            ['V3_ID_ACO', 'COPIA DE IDENTIFICACIÓN DEL ACOMPAÑANTE', 0],
            ['V3_REF_053', 'FORMULARIO 053 REFERENCIA O DERIVACIÓN', 0],
            ['V3_VAL', 'CÓDIGO DE VALIDACIÓN', 0],
            ['V3_ACT_ENT', 'ACTA DE ENTREGA', 0, 'acta_servicio'],
            ['V3_CON_INF', 'CONSENTIMIENTO INFORMADO', 0, 'consentimiento_general'],
            ['V3_F007', 'FORMULARIO 007', 0],
            ['V3_F012', 'FORMULARIO 012 ORDEN DE PROCEDIMIENTO', 0],
            ['V3_CON_PRO', 'CONSENTIMIENTOS DE PROCEDIMIENTOS', 0],
            ['V3_SOL_AT', 'SOLICITUD DE ATENCIÓN', 0, 'solicitud_atencion'],
            ['V3_COB_PAC', 'COBERTURA DE SALUD DEL PACIENTE', 1],
            ['V3_COB_PRI', 'COBERTURA DE SALUD PRINCIPAL', 3],
            ['V3_INF_MED', 'ACTA DE ENTREGA DE INFORME MÉDICO', 0, 'entrega_informe'],
            ['V3_SEG_PRI', 'CARTA DE SEGURO PRIVADO', 0, 'financiamiento_privado'],
        ];
        foreach ($documents as $sequence => $item) {
            if (DB::table('documento')->where('tipo_documento', $item[0])->exists()) {
                DB::table('documento')->where('tipo_documento', $item[0])->update(['formato' => isset($item[3]) ? $item[3] : null, 'sistema' => isset($item[3]) ? 1 : 0, 'updated_at' => $now]);

                continue;
            }
            DB::table('documento')->insert(['secuencia' => $sequence + 1, 'nombre' => $item[1].' (*)', 'nombre_corto' => $item[1],
                'proc_consul' => 2, 'tipo_seguro' => null, 'id_seguro' => null, 'id_subseguro' => null, 'id_tipo_usuario' => $role->id,
                'id_sala' => null, 'est_doc_tarea' => 0, 'id_empresa' => null, 'link' => null, 'tipo_documento' => $item[0], 'msp' => $item[2],
                'estado' => 1, 'formato' => isset($item[3]) ? $item[3] : null, 'principal' => null, 'rpt_prin' => null, 'sistema' => isset($item[3]) ? 1 : 0, 'id_usuariocrea' => $user->id,
                'id_usuariomod' => $user->id, 'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now]);
        }
        foreach (DB::table('procedimiento')->where('estado', 1)->get() as $procedure) {
            $exists = DB::table('formato_consentimiento')->where('id_procedimiento', $procedure->id)->whereNull('id_empresa')->exists();
            if ($exists) {
                continue;
            }
            DB::table('formato_consentimiento')->insert(['id_procedimiento' => $procedure->id,
                'nombre' => substr('Consentimiento informado - '.$procedure->nombre, 0, 75), 'estado' => 1, 'auditoria' => 'V3-GENERICO',
                'id_empresa' => null, 'consiste' => 'Autorización informada para realizar el procedimiento indicado por el profesional tratante.',
                'realiza' => 'El procedimiento será realizado por personal clínico autorizado, siguiendo protocolos institucionales vigentes.',
                'duracion_estimada' => 'La duración depende del procedimiento y de la condición clínica del paciente.',
                'beneficios' => 'Apoyar el diagnóstico, tratamiento o seguimiento de la condición clínica.',
                'riesgos_frecuentes' => 'Molestia, dolor leve, sangrado menor o reacciones transitorias relacionadas con el procedimiento.',
                'riesgos_infrecuentes' => 'Complicaciones que pueden requerir observación, tratamiento adicional o intervención especializada.',
                'riesgos_adicionales' => 'Los riesgos particulares deberán ser explicados por el profesional según la situación del paciente.',
                'alternativas' => 'Observación, tratamiento médico u otras alternativas disponibles según criterio profesional.',
                'manejo' => 'La institución dispone de protocolos para reconocer y tratar posibles complicaciones.',
                'consecuencias' => 'No realizar el procedimiento puede retrasar el diagnóstico o tratamiento recomendado.',
                'check_labs' => 0, 'ip_creacion' => '127.0.0.1', 'id_usuariocrea' => $user->id, 'id_usuariomod' => $user->id, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down()
    {
        DB::table('documento')->where('tipo_documento', 'like', 'V3_%')->delete();
        DB::table('formato_consentimiento')->where('auditoria', 'V3-GENERICO')->delete();
    }
}
