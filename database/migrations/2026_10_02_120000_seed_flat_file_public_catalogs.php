<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedFlatFilePublicCatalogs extends Migration
{
    public function up()
    {
        $actor = $this->auditActor();

        $this->seedInsuranceTypes($actor);
        $this->seedReferralCodeTypes($actor);
        $this->seedDependencies();
    }

    private function seedInsuranceTypes($actor)
    {
        if (! Schema::hasTable('tipo_seguro')) {
            return;
        }

        $rows = [
            ['nombre' => 'ACTIVO', 'tipo' => 'SG', 'tipo_principal' => 'ACTIVO'],
            ['nombre' => 'ACTIVO (CONYUGE)', 'tipo' => 'CY', 'tipo_principal' => 'ACTIVO'],
            ['nombre' => 'HIJO DE 0 - 1 AÑO', 'tipo' => 'NI', 'tipo_principal' => 'ACTIVO'],
            ['nombre' => 'HIJO DE 2 - 6 AÑOS', 'tipo' => 'N6', 'tipo_principal' => 'ACTIVO'],
            ['nombre' => 'HIJO DE 7 - 17 AÑOS', 'tipo' => 'AG', 'tipo_principal' => 'ACTIVO'],
            ['nombre' => 'JUBILADO', 'tipo' => 'JU', 'tipo_principal' => 'JUBILADO'],
            ['nombre' => 'JUBILADO CAMPESINO', 'tipo' => 'JC', 'tipo_principal' => 'SSC-JUBILADO'],
            ['nombre' => 'MONTEPIO', 'tipo' => 'MO', 'tipo_principal' => 'MONTEPIO'],
            ['nombre' => 'SSC', 'tipo' => 'CA', 'tipo_principal' => 'SSC'],
        ];

        foreach ($rows as $row) {
            $existing = DB::table('tipo_seguro')
                ->whereRaw('UPPER(TRIM(nombre)) = ?', [mb_strtoupper($row['nombre'], 'UTF-8')])
                ->first();

            $values = array_merge($row, [
                'estado' => 1,
                'id_usuariomod' => $actor,
                'ip_modificacion' => '127.0.0.1',
                'updated_at' => now(),
            ]);

            if ($existing) {
                DB::table('tipo_seguro')->where('id', $existing->id)->update($values);
            } else {
                DB::table('tipo_seguro')->insert(array_merge($values, [
                    'id_usuariocrea' => $actor,
                    'ip_creacion' => '127.0.0.1',
                    'created_at' => now(),
                ]));
            }
        }
    }

    private function seedReferralCodeTypes($actor)
    {
        if (! Schema::hasTable('codigo_ derivacion')) {
            return;
        }

        $rows = [
            ['codigo' => 'CVAF', 'nombre' => 'CODIGO VALIDACION FISICA'],
            ['codigo' => 'CVAV', 'nombre' => 'CODIGO VALIDACION AMBULATORIA VIRTUAL'],
            ['codigo' => 'CVP', 'nombre' => 'CODIGO VALIDACION PROVINCIAS'],
            ['codigo' => 'CVH', 'nombre' => 'CODIGO VALIDACION HOSPITALARIA'],
            ['codigo' => 'CVEC', 'nombre' => 'CODIGO VALIDACION EMERGENCIA CONVENIO'],
            ['codigo' => 'CVENC', 'nombre' => 'CODIGO VALIDACION EMERGENCIA NO CONVENIO'],
            ['codigo' => 'CVCEC', 'nombre' => 'CODIGO VALIDACION DERIVADO DE CALL CENTER'],
        ];

        foreach ($rows as $row) {
            $existing = DB::table('codigo_ derivacion')
                ->whereRaw("UPPER(REPLACE(REPLACE(TRIM(codigo), CHAR(13), ''), CHAR(10), '')) = ?", [$row['codigo']])
                ->first();

            $values = array_merge($row, [
                'estado' => 1,
                'id_usuariomod' => $actor,
                'ip_modificacion' => '127.0.0.1',
                'updated_at' => now(),
            ]);

            if ($existing) {
                DB::table('codigo_ derivacion')->where('id', $existing->id)->update($values);
            } else {
                DB::table('codigo_ derivacion')->insert(array_merge($values, [
                    ' id_usuariocrea' => $actor,
                    'ip_creacion' => '127.0.0.1',
                    'created_at' => now(),
                ]));
            }
        }
    }

    private function seedDependencies()
    {
        if (! Schema::hasTable('codigo_ dependencia')) {
            return;
        }

        $names = [
            'GASTROENTEROLOGIA (HO)',
            'NEUROLO-ENFER.DEGENERATIVAS',
            'ODONTOLOGIA (Protesis)',
            'NEUROFISIOLOGIA',
            'BODEGA DE U.T.I',
            'CIRUGIA MENOR',
            'DEPENDENCIA DE PRUEBA',
            'ANESTESIOLOGIA (CE)',
            'BODEGA DE CARRETAS',
            'MEDICINA GENERAL IZQUIETA',
            'SALUD OCUPACIONAL',
            'Fomento y Prevención (CO)',
            'CLINICA DE HERIDAS (CE)',
            'YESOS (CE)',
            'TRAUMAPEDIATRIA (CE)',
            'PEDIATRIA (CE)',
            'TERAPIA DEL DOLOR (CE)',
            'INTERNOS/RESIDENTES/POSTGRADO',
            'ENDOSCOPIA (CE)',
            'CARDIOLOGIA (CE)',
            'HEMODINAMICA (CE)',
            'GINECO-OBSTETRICIA (CE)',
            'COMITE DE TUMORES (CE)',
            'TRANSPLANTE RENAL(CE)',
            'CLINICA DE MAMA (CE)',
            'ENFERMERIA CURACIONES (CE)',
            'FISIATRIA (CE)',
            'MEDICINA PREVENT Y EDUC.SAL(CE',
            'ODONTOLOGIA (CE)',
            'OFTALMOLOGIA (CE)',
            'OTORRINOLARINGOLOGIA (CE)',
            'TRAUMATOLOGIA ORTOPEDIA (CE)',
            'ULTRASONIDO  (CE)',
            'EMERGENCIAS (EM)',
            'URGENCIAS PEDIATRIAS (CE)',
            'ORL EMERGENCIAS',
            'URGENCIAS OFTALMICAS',
            'AREA DE CONTINGENCIA AH1N1(EM)',
            'URGENCIAS DE TRAUMATOLOGIA',
            'GINECOLOGIA (CE)',
            'URGENCIAS QUEMADOS',
            'COLPOSCOPIAS GINECOLOGICAS(CE)',
            'PEDIATRIA-TRAUMATOLOGIA',
            'PEDIATRIA-CIRUGIA',
            'PEDIATRIA-CARDIOLOGIA',
            'CAA-DISPENSARIO 6 HTMC-NO USAR',
            'CAA-DISPENSARIO 31 HTMC-NOUSAR',
            'CAA-DISPENSARIO 24 HTMC-NOUSAR',
            'CAA-DISPENSARI NORTE HTMC-USAR',
            'CAA-DIS VALDIVIA(SUR)HTMC-NOUS',
            'PEDIATRIA-NIÑOS ESPECIALES',
            'PEDIATRIA-NEUMOLOGIA',
            'PEDIATRIA-NEUROLOGIA',
            'MEDICO DOMICILIO (CE)',
            'LABORATORIO CLINICO',
            'MEDICINA NUCLEAR (CE)',
            'MASTOLOGIA (CE)',
            'IMAGENOLOGIA',
            'GENETICA (CE)',
            'RADIOTERAPIA (CE)',
            'LABORATORIO HISTOCOMPATIBILIDA',
            'MICROSCOPIA ELECTRONICA (CE)',
            'FARMACIA (CE)',
            'NEFRONUTRICION',
            'NEFROPSICOLOGIA',
            'ONCOPSICOLOGIA',
            'CUIDADOS A PACIENTES CRONICOS',
            'INSTRUMENTACION UROLOGIA(CE)',
            'LAPAROSCOPIA GINECOLOGICA (CE)',
            'NEUMOLOGIA (CE)',
            'GASTROENTEROLOGIA (CE)',
            'HEMATOLOGIA (CE)',
            'NEFROLOGIA (CE)',
            'NEUROLOGIA (CE)',
            'ENDOCRINOLOGIA (CE)',
            'ONCOLOGIA (CE)',
            'PSICOLOGIA (CE)',
            'PSIQUIATRIA (CE)',
            'DERMATOLOGIA (CE)',
            'CLINICA CADERA (CE)',
            'NEUROPSICOLOGIA (CE)',
            'MEDICINA GENERAL (CE)',
            'COLOPROCTOLOGIA (CE)',
            'CIRUGIA MAXILOFACIAL (CE)',
            'CARDIOTORACICA (CE)',
            'CIRUGIA VASCULAR (CE)',
            'CIRUGIA GENERAL (CE)',
            'CIRUGIA PULMONAR (CE)',
            'NEUROCIRUGIA (CE)',
            'UROLOGIA (CE)',
            'QUEMADOS/CIRUGIA REPARAT. (CE)',
            'CIRUGIA PLASTICA (CE)',
            'LITOTRIPSIA (CE)',
            'PATOLOGIA CLINICA (CE)',
            'OBSTETRICIA (CE)',
            'PROCTOLOGIA (CE)',
            'PERINATOLOGIA (CE)',
            'NEONATOLOGIA (CE)',
            'ANESTESIOLOGIA DOLOR (CE)',
            'MEDICINA PERSONAL(CE)',
            'OTONEUROLOGIA (CE)',
            'NUTRICION Y DIETETICA',
            'ATENCION DOMICILIARIA (CE)',
            'MEDICINA FAMILIAR',
            'MEDICINA INTERNA (CE)',
            'ALERGOLOGIA (CE)',
            'REUMATOLOGIA (CE)',
            'GERIATRIA (CE)',
            'CLINICA DE VIH/SIDA (CE)',
            'INFECTOLOGIA(CE)',
            'CLINICA PAT.BENIGNA MAMA (CE)',
            'CLINICA DOLOR(CE)',
            'CALIFICACION MEDICA(CE)',
            'CLINICA HIGADO(CE)',
            'ONCOLOGIA-OTORRINOLARINGOLOGIA',
            'ALERGIAS E INMUNOLOGIA(CE)',
            'ELECTROMIOGRAFIA (CE)',
            'CENTRO GASTRICO (CE)',
            'CENTRO DE DIALISIS',
            'ONCOLOGIA - PAC.NUEVOS HCAM',
            'ONCO-SEC. B HCAM - DR.FCHECA',
            'ONCO-SEC. C HCAM - DR.ELEON',
            'ONCO-SEC. D HCAM - DR.PGAVILA',
            'ONCO-SEC. E HCAM - DR.MALVAR',
            'ONCO-SEC. R HCAM - DR.MARGUE',
            'ONCO-SEC. S HCAM - DR.MGUERRON',
            'ONCO-SEC. T HCAM - DR.RANDRADE',
            'ONCO-SEC. F HCAM - DR.CSANCHEZ',
            'ONCO-STAFF ONCOLOGIA HCAM',
            'ONCO-MAQUINAS Y SIMULACION',
            'ONCO-CONTROL RADIO-TERAPIA',
            'CIRUGIA PEDIATRICA',
            'ONCOLOGIA - GINECOLOGICA',
            'CLINICA DE CANCER DE TIROIDES',
            'UROLOGIA ESTUDIOS CLINICOS(63)',
            'TRAUMATOLOGIA ESTUDIOS CLINICO',
            'SERVICIO DE AUDIOMETRIA (CE)',
            'DIALISIS PERITONEAL',
            'ADMISION HOSPITALARIA (CE)',
            'ADMIS CONSULTA EXT.HTMC-NOUSAR',
            'CLINICA DE HERIDAS (HO)',
            'YESOS (HO)',
            'PEDIATRIA GENERAL (HO)',
            'SALA DE PARTOS (HO)',
            'HOSPITAL DEL DIA',
            'CARDIOLOGIA (HO)',
            'GINECO_OBSTETRICIA (HO)',
            'TRANSPLANTE RENAL(HO)',
            'ENFERMERIA CURACIONES',
            'MEDICINA FISICA REHABILITACION',
            'MEDICINA PREVENTIVA Y EDUC.SAL',
            'ESTOMATOLOGIA (HO)',
            'OFTALMOLOGIA (HO)',
            'OTORRINOLARINGOLOGIA (HO)',
            'TRAUMATOLOGIA/ORTOPEDIA (HO)',
            'ULTRASONIDO',
            'EMERGENCIAS (URGENCIAS)',
            'GINECOLOGIA (HO)',
            'MEDICINA PREVENTIVA (HO)',
            'TRAUMATOLOGIA NORTE (HO)',
            'TRAUMATOLOGIA ANEXO (HO)',
            'MEDICO DOMICILIO',
            'LABORATORIO GENERAL (HO)',
            'MEDICINA NUCLEAR',
            'MASTOLOGIA (HO)',
            'IMAGENOLOGIA (HO)',
            'GENETICA (HO)',
            'RADIOTERAPIA (HO)',
            'MICROSCOPIA ELECTRONICA (HO)',
            'FARMACIA (HO)',
            'NEFROPSICOLOGIA(HO)',
            'LAPAROSCOPiA GINECOLOGICA(HO)',
            'HEMODINAMICA (HO)',
            'NEUMOLOGIA (HO)',
            'NEUROLO-DEMENCIA Y CONDUCTA',
            'HEMATOLOGIA (HO)',
            'ONCOLOGIA (HO)',
            'PSICOLOGIA (HO)',
            'PSIQUIATRIA (HO)',
            'DERMATOLOGIA (HO)',
            'NEUROPSICOLOGIA (HO)',
            'UNIDAD DE CUIDADOS NEUROLOGICO',
            'MEDICINA GENERAL (HO)',
            'COLOPROCTOLOGIA (HO)',
            'CIRUGIA MAXILOFACIAL (HO)',
            'CARDIOTORACICA (HO)',
            'CIRUGIA VASCULAR (HO)',
            'CIRUGIA GENERAL (HO)',
            'ANESTESIOLOGIA (HO)',
            'NEUROCIRUGIA (HO)',
            'UROLOGIA (HO)',
            'QUEMADOS/CIRUGIA REPARAT. (HO)',
            'CIRUGIA PLASTICA (HO)',
            'TERAPIA INTENSIVA (HO)',
            'PATOLOGIA CLINICA (HO)',
            'CUIDADOS INTENS.CORONARIOS(HO)',
            'CIRUGIA GENERAL NORTE (HO)',
            'OBSTETRICIA (HO)',
            'PERINATOLOGIA',
            'NEONATOLOGIA (HO)',
            'OBSERVACION(HO)',
            'CENTRO QUIRURGICO',
            'ANASTESIOLOGIA DOLOR (HO)',
            'SALUD DE PERSONAL (EM)',
            'CENTRO DE RECUPERACION',
            'NUTRICION Y DIETETICA (HO)',
            'ATENCION DOMICILIARIA',
            'MEDICINA INTERNA (HO)',
            'ALERGOLOGIA (HO)',
            'REUMATOLOGIA (HO)',
            'GERIATRIA (HO)',
            'INMUNOLOGIA (HO)',
            'INFECTOLOGIA (HO)',
            'CLINICA DOLOR(HO)',
            'OTORRINO-ONCOLOGIA(HO)',
            'Alergias e Inmunolog¡a (HO)',
            'CIRUGIA MAXOLOFACIAL (HO)',
            'URGENCIAS PEDIATRICAS',
            'TERAPIA INTENSIVA PEDIATRICA',
            'TERAPIA INTENSIVA NEONATOLOGIA',
            'CUIDADO INTERMEDIO NEONATOLOGI',
            'TERAPIA INTENSIVA QUEMADOS',
            'CUIDADO INTERMEDIO QUEMADOS',
            'RECUPERACION',
            'PEDIATRIA         HTMC-NO USAR',
            'CARDIOLOGIA       HTMC-NO USAR',
            'ENFERMER.CURACION HTMC-NO USAR',
            'PROCTOLOGIA       HTMC-NO USAR',
            'ODONTOLOGIA       HTMC-NO USAR',
            'OFTALMOLOGIA      HTMC-NO USAR',
            'OTORRINOLARINGOLO HTMC-NO USAR',
            'TRAUMAT/ORTOPEDIA HTMC-NO USAR',
            'MED.FIS.REHABILI. HTMC-NO USAR',
            'EMERGENCIAS       HTMC-NO USAR',
            'PATOLOGIA         HTMC-NO USAR',
            'PATOLOGIA CLINICA HTMC-NO USAR',
            'GINECO Y OBSTETRI HTMC-NO USAR',
            'MEDICINA NUCLEAR  HTMC-NO USAR',
            'IMAGENOLOGIA      HTMC-NO USAR',
            'GENETICA          HTMC-NO USAR',
            'FARMACIA          HTMC-NO USAR',
            'MEDICINA INTERNA  HTMC-NO USAR',
            'NEUMOLOGIA        HTMC-NO USAR',
            'GASTROENTEROLOGIA HTMC-NO USAR',
            'HEMATOLOGIA       HTMC-NO USAR',
            'NEFROLOGIA        HTMC-NO USAR',
            'NEUROLOGIA        HTMC-NO USAR',
            'ENDOCRINOLOGIA    HTMC-NO USAR',
            'ONCOLOGIA         HTMC-NO USAR',
            'RADIOTERAPIA      HTMC-NO USAR',
            'PSIQUIAT Y PSICOL HTMC-NO USAR',
            'DERMATOLOGIA      HTMC-NO USAR',
            'CARDIOTORAXICA    HTMC-NO USAR',
            'CIRUGIA VASCULAR  HTMC-NO USAR',
            'CIRUGIA GENERAL   HTMC-NO USAR',
            'ANESTESIOLOGIA    HTMC-NO USAR',
            'NEUROCIRUGIA      HTMC-NO USAR',
            'UROLOGIA          HTMC-NO USAR',
            'CIRUGIA PLASTICA  HTMC-NO USAR',
            'PERINATOLOGIA     HTMC-NO USAR',
            'NEONATOLOGIA      HTMC-NO USAR',
            'MEDICINA CLINICA  HTMC-NO USAR',
            'MEDICINA GENERAL  HTMC-NO USAR',
            'ALERGOLOGIA       HTMC-NO USAR',
            'REUMATOLOGIA      HTMC-NO USAR',
            'GERIATRIA         HTMC-NO USAR',
            'INFECTOLOGIA      HTMC-NO USAR',
            'AMBULANCIA (CE)',
            'UNIDAD DE DIALISI HTMC-NO USAR',
            'UCI-UNI.CUID-INT  HTMC-NO USAR',
            'UCN-UN.CUI.NEUROL.HTMC-NO USAR',
            'PSTOPER.TRAUMATOL HTMC-NO USAR',
            'HO EXTERNOS       HTMC-NO USAR',
            'CIRUGIA CLINICA DE HERIDAS',
            'CIRUGIA PEDIATRICA (CE)',
            'CLINICA MARCAPASOS - CARDIOLOG',
            'CLINICA DE OSTEOPOROSIS',
            'ODONTOLOGIA PREVENTIVA',
            'CIRUGIA DEL DIA OFTALMOLOGIA',
            'CIRUGIA DEL DIA OTORRINOLARING',
            'CIRUGIA DEL DIA TRAUMATOLOGIA',
            'CIRUGIA DEL DIA GINECOLOGIA',
            'CIRUGIA DEL DIA MASTOLOGIA',
            'ENDOSCOPIAS',
            'CLINICA-PROCEDIMIENTOS DERMATO',
            'GERONTORLOGIA MEDICINA GENERAL',
            'CIRUGIA DEL DIA PROCTOLOGIA',
            'CIRUGIA DEL DIA MAXILO FACIAL',
            'CIRUGIA DEL DIA CIRG.VASCULAR',
            'CIRUGIA DEL DIA CIRG. GENERAL',
            'CIRUGIA DEL DIA UROLOGIA',
            'PEDIATRIA-HO      HTMC-NO USAR',
            'CARDIOLOGIA-HO    HTMC-NO USAR',
            'PROCTOLOGIA-HO    HTMC-NO USAR',
            'OFTALMOLOGIA-HO   HTMC-NO USAR',
            'OTORRINOLARING-HO HTMC-NO USAR',
            'TRAUM.ORTOPEDI-HO HTMC-NO USAR',
            'GINECO.OBSTET-HO  HTMC-NO USAR',
            'MEDICINA INTER-HO HTMC-NO USAR',
            'NEUMOLOGIA-HO     HTMC-NO USAR',
            'GASTROENTER-HO    HTMC-NO USAR',
            'HEMATOLOGIA-HO    HTMC-NO USAR',
            'NEFROLOGIA-HO     HTMC-NO USAR',
            'NEUROLOGIA-HO     HTMC-NO USAR',
            'ENDOCRINOLOGIA-HO HTMC-NO USAR',
            'ONCOLOGIA-HO      HTMC-NO USAR',
            'PSIQUIATRIA-HO    HTMC-NO USAR',
            'DERMATOLOGIA-HO   HTMC-NO USAR',
            'CARDIOTORACICO-HO HTMC-NO USAR',
            'CIR. GENERAL-HO   HTMC-NO USAR',
            'NEUROCIRUGIA-HO   HTMC-NO USAR',
            'UROLOGIA-HO       HTMC-NO USAR',
            'CIRUGIA PLASTI-HO HTMC-NO USAR',
            'GINECOLOGIA (CE)REMPLAZO',
            'PROCEDIM. ESPEC. DERMATOLOGIA',
            'PEDIATRIA (HO) DIA',
            'CIRUGIAS OFTALMOLOGIA (HO) DIA',
            'CIRUGIA OTORRINO (HO) DIA',
            'TRAUMATOLOGIA ORTOPEDI (HO)DIA',
            'CIRUGIA GINECOLOGIA (HO)   DIA',
            'HEMODINAMICA DEL DIA (HO)',
            'CIRUGIA HEMATOLOGIA (HO)  DIA',
            'HEMODIALISIS',
            'CIRUGIA CARDIOTORACICA(HO)DIA',
            'CIRUGIA VASCULAR (HO)     DIA',
            'CIRUGIA GENERAL (HO)      DIA',
            'NEUROCIRUGIA (HO)         DIA',
            'CIRUGIA UROLOGICAS (HO)   DIA',
            'QUEMADOS (HO) DIA',
            'CIRUGIA MAXILOFACIAL(HO)  DIA',
            'QUEMADOS',
            'PARA EMITIR RECETAS EXTERNAS',
            'PROCEDIMIENTO MEDICO ESPECIAL',
            'CLINICA LASER(CE)',
            'CLINICA RETINA(CE)',
            'CLINICA CORNEA(CE)',
            'CLINICA GLAUCOMA(CE)',
            'CLINICA NEUROFTALMOLOGICA(CE)',
            'ECOBIOMETRIAS (CE)',
            'DIAGNOSTICO ODONTOLOGICO (CE)',
            'ENDODONCIA ODONTOLOGICA (CE)',
            'REHABILITACION ORAL     (CE)',
            'EMERGENCIAS ODONTOLàGICAS (CE)',
            'PERIODONCIA',
            'CIRUGIA DENTO-ALVEOLAR',
            'SOMATOPROTESIS',
            'PRUEBAS INFORMATICA',
            'GERENCIA DEL HOSPITAL CAM',
            'NEUROLOGIA-PARKINSON (CE)',
            'NEUROLOGIA-VASCULARES (CE)',
            'NEUROLOGIA-CEFALEAS (CE)',
            'NEUROLOGIA-EPILEPSIAS (CE)',
            'NEUROLOGIA-VERTIGO (CE)',
            'PEDIATRIA-ODONTOLOGIA',
            'ACUPUNTURA',
            'PEDIATRIA NORTE (HO)',
            'CUID.INTES.CARDIOTORACICOS(HO)',
            'NEUROLO-DISTUR.RESPIRATO-SUE¥O',
        ];

        foreach ($names as $name) {
            $existing = DB::table('codigo_ dependencia')
                ->whereRaw('UPPER(TRIM(nombre_cod)) = ?', [mb_strtoupper($name, 'UTF-8')])
                ->first();

            if ($existing) {
                DB::table('codigo_ dependencia')->where('id', $existing->id)->update([
                    'estado' => 1,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('codigo_ dependencia')->insert([
                    'nombre_cod' => $name,
                    'codigo' => null,
                    'estado' => 1,
                    'ip_creacion' => '127.0.0.1',
                    'ip_modificacion' => '127.0.0.1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function auditActor()
    {
        if (! Schema::hasTable('users')) {
            throw new RuntimeException('No se puede poblar los catálogos de archivo plano porque no existe la tabla users.');
        }

        $actor = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)
            ->orderBy('id')->value('id');

        if (! $actor) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }
        if (! $actor) {
            throw new RuntimeException('No se puede poblar los catálogos de archivo plano porque no existe un usuario de auditoría.');
        }

        return (string) $actor;
    }

    public function down()
    {
        // Los catálogos pueden quedar referenciados por cabeceras existentes.
    }
}
