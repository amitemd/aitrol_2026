<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ImagenologiaCatalogSeeder extends Seeder
{
    private const USER_IP = '127.0.0.1';

    /**
     * Catalogo transcrito de la orden de imagenes de Borja Clinic.
     * Los asteriscos se conservan porque remiten a las condiciones de
     * contraste y sedacion impresas en el formulario original.
     */
    private const CATALOG = [
        'RADIOGRAFÍA' => [
            'Cráneo 1 posición',
            'Cráneo 2 posiciones',
            'Cráneo 3 posiciones AP-Lateral-Towne',
            'Silla turca dos posiciones (lateral)',
            'Silla turca dos posiciones (AP y lateral)',
            'Dacriocistografía comparativa',
            'Sialografía comparativa glándula parótida',
            'Sialografía comparativa de submaxilar',
            'Mastoides 3 proyecciones',
            'Articulación temporomaxilar',
            'Huesos propios nasales',
            'Huesos faciales 1 posición',
            'Huesos faciales 2 posiciones',
            'Senos paranasales 3 posiciones (WC lateral SPN)',
            'Senos paranasales 2 posiciones (Waters-Cadwell)',
            'Cavum adenoides 1 proyección (cuello)',
            'Cavum adenoides 2 proyecciones (cuello)',
            'Columna cervical 4 posiciones',
            'Columna cervical AP lateral oblicua (3 posiciones)',
            'Columna cervical AP - lateral (2 posiciones)',
            'Columna cervical funcional (2 posiciones)',
            'Columna dorsal 1 posición - 1 placa',
            'Columna dorsal 2 posiciones - 2 placas',
            'Columna dorsal 3 posiciones - 3 placas',
            'Columna lumbosacra 1 posición - 1 placa',
            'Columna lumbosacra 2 posiciones - 2 placas',
            'Columna lumbosacra 3 posiciones - 3 placas',
            'Columna dorsolumbar funcional',
            'Columna sacro y coxis AP - lateral',
            'Tórax 1 posición',
            'Tórax 2 posiciones',
            'Tórax 3 posiciones',
            'Tórax 4 posiciones',
            'Abdomen 1 posición',
            'Abdomen 2 posiciones',
            'Abdomen 1 posición de pie',
            'Hombro 1 posición',
            'Hombro 2 posiciones',
            'Hombro 3 posiciones',
            'Clavícula 1 posición',
            'Brazo AP y lateral',
            'Antebrazo AP y lateral',
            'Codo 1 posición',
            'Codo 2 posiciones (AP y lateral)',
            'Codo AP y lateral',
            'Muñeca 2 posiciones',
            'Muñeca 3 posiciones',
            'Mano 2 posiciones',
            'Dedos AP y lateral',
            'Edad ósea 1 placa',
            'Cadera 1 posición',
            'Cadera 2 posiciones',
            'Cadera 3 posiciones',
            'Cadera 4 posiciones',
            'Muslo 2 posiciones (AP lateral) (fémur)',
            'Pierna 2 posiciones (AP-lateral) (tibia-peroné)',
            'Rodilla 4 posiciones',
            'Rodilla 2 posiciones (AP y lateral)',
            'Pierna 1 posición (AP)',
            'Pierna 2 posiciones (AP y lateral)',
            'Tobillo 1 posición',
            'Tobillo 2 posiciones (AP y lateral)',
            'Tobillo 3 posiciones',
            'Pie 1 posición AP o lateral',
            'Pie 2 posiciones AP - oblicua',
            'Pie completo 3 posiciones',
            'Esofagograma**',
            'Tránsito gastrointestinal (esófago, estómago y duodeno)',
            'Tránsito gastrointestinal (intestino delgado)',
            'Enema de baritado de colon',
            'Enema de colon a doble contraste',
            'Uretrocistografía retrógrada**',
            'Uretrocistografía de micción**',
            'Colangiografía post-operatoria**',
            'Colangiografía trans-operatoria**',
            'Urograma excretor**',
            'Uretrocistografía masculina',
            'Uretrocistografía femenina',
            'Urograma excretorio',
            'Fistulografía**',
            'Histerosalpingografía',
            'Placa adicional (reimpresión de estudio)',
        ],
        'TOMOGRAFÍA' => [
            'Angiotac cerebral*',
            'Angiotac de cuello*',
            'Angiotac miembro superior (1)*',
            'Angiotac miembros superiores (2)*',
            'Angiotac torácica*',
            'Angiotac de arterias pulmonares',
            'Angiotac aorta abdominal*',
            'Angiotac abdominal*',
            'Angiotac aorta + miembros inferiores*',
            'Angiotac miembros inferiores (2)*',
            'Angiotac renal*',
            'Cráneo - 3D*',
            'Cráneo simple',
            'Cráneo simple + contrastada**',
            'Cara - 3D*',
            'Cara simple',
            'Cara simple + contraste IV',
            'TAC de senos paranasales simple',
            'Órbitas simple',
            'Órbitas simple + contrastada**',
            'Órbitas simple - 3D',
            'Silla turca simple',
            'Silla turca simple + contrastada**',
            'Conducto auditivo simple + contraste**',
            'Conducto auditivo simple',
            'Conducto auditivo simple - 3D',
            'Cuello - 3D*',
            'Cuello simple',
            'Cuello simple + contrastada**',
            'Hombro',
            'Húmero',
            'Codo',
            'Antebrazo',
            'Muñeca y mano',
            'Miembro superior',
            'Cadera',
            'Fémur',
            'Rodilla',
            'Pierna (tibia y peroné)',
            'Tobillo',
            'Pie',
            'Miembro inferior',
            'Tórax - 3D*',
            'Tórax simple',
            'Tórax simple + contrastada**',
            'Tórax simple con inspiración (alta resolución)',
            'Tórax simple con espiración (alta resolución)',
            'Tórax simple alta resolución',
            'Abdomen - 3D*',
            'Abdomen-pelvis contraste oral-IV y rectal',
            'Abdomen simple + contraste oral-IV',
            'Abdomen simple',
            'Abdomen simple + contraste oral',
            'Abdomen simple + contrastada IV**',
            'Colonoscopía virtual CT*',
            'Abdomen-pelvis simple + contraste oral',
            'Abdomen-pelvis simple + contraste IV',
            'Abdomen-pelvis simple',
            'Abdomen-pelvis simple + contraste oral + IV',
            'TAC de pelvis simple',
            'TAC de pelvis simple + contrastada**',
            'Pelvis - 3D*',
            'Fistulografía por tomografía',
            'Uro TAC simple y contrastada*',
            'Uro TAC simple*',
            'Articulación simple y contrastada*',
            'Articulación simple*',
            'Articulación simple - 3D*',
            'Columna cervical - 3D*',
            'Columna cervical simple y contrastada*',
            'Columna cervical simple*',
            'Columna dorsal simple y contrastada*',
            'Columna dorsal simple*',
            'Columna dorsal - 3D*',
            'Columna lumbar - 3D*',
            'Columna lumbar simple y contrastada*',
            'Columna lumbar simple*',
            'Columna total - 3D*',
            'Columna total simple y contrastada*',
            'Columna total simple*',
            'Columna ósea 3 vértebras - 3D*',
            'Panorámica de columna total por TAC',
            'Técnica de Farril (pacientes pediátricos)',
        ],
        'DENSITOMETRÍA' => [
            'Columna AP',
            'Muñeca dual',
            'Muñeca izquierda',
            'Muñeca derecha',
            'Cadera dual',
            'Cadera derecha',
            'Cadera izquierda',
            'Fémur izquierdo',
            'Fémur derecho',
            'Fémur dual',
            'Dual de fémur, antebrazo derecho',
            'Dual de fémur, antebrazo izquierdo',
            '% probabilidad fractura + cadera (izquierda-derecha)',
            'Todos los centros',
            'Scan cuerpo total',
            'Composición corporal (grasa/músculo)',
            'Densitometría más puntuación T',
            'Equilibrio masa magra/distribución grasa',
            'Metabolismo corporal',
        ],
        'MAMOGRAFÍA' => [
            'Mamografía bilateral',
            'Mamografía bilateral con implantes',
            'Mamografía unilateral',
            'Marcaje pre-quirúrgico',
            'Mamas (proyecciones adicionales 2 posiciones)',
            'Magnificación mamaria',
            'Mamografía 3D',
            'Galactografía',
        ],
        'ECOGRAFÍA' => [
            'Cerebro recién nacido (transfontanelar)',
            'Tiroides - paratiroides',
            'Ecocardiografía con interpretación',
            'Cardiológica',
            'Eco torácico',
            'Ultrasonido abdomen superior',
            'Ultrasonido abdomen inferior (pélvico)',
            'Obstétrico control',
            'Obstétrico - 1er trimestre marcadores',
            'Cromosómicos',
            'Obstétrico - morfológico',
            'Obstétrico - morfológico + 5D',
            'Obstétrico - 3er trimestre',
            'Ultrasonido mamario',
            'Doppler de mamas y elastosonografía',
            'Doppler de pene',
            'Doppler testicular',
            'Toma de biopsia dirigida con ultrasonido',
            'Punción dirigida con ultrasonido',
            'Eco endovaginal',
            'Eco ósteo-muscular (unilateral)',
            'Ecografía transrectal',
            'Doppler carotídeo',
            'Doppler obstétrico',
            'Doppler portal',
            'Doppler renal',
            'Doppler venoso 1 extremidad',
            'Doppler arterial 1 extremidad',
            'Doppler venoso 2 extremidades',
            'Doppler arterial 2 extremidades',
            'Doppler venoso y arterial 1 extremidad',
            'Doppler venoso y arterial 2 extremidades',
            'Biopsia trucut de mama con ecografía',
            'PAAF de mama',
            'PAAF de tiroides',
            'PAAF de ganglio',
            'PAAF evacuatoria de quiste mamario o de tiroides',
            'Eco partes blandas',
            'Eco pared abdominal',
            'Eco reumatológico (manos)',
            'Biopsia de hígado guiada**',
            'Biopsia de masas guiada**',
        ],
        'RESONANCIA MAGNÉTICA' => [
            'Angioresonancia del polígono de Willis',
            'Cerebro simple (técnica hospitalaria) + contraste',
            'Cerebro simple (técnica hospitalaria)',
            'Cerebro (incluye tallo cerebral) + contraste',
            'Cerebro (incluye tallo cerebral)',
            'Oídos simple',
            'Oídos sin contraste',
            'Órbitas simple',
            'Órbitas sin contraste',
            'Maxilar superior e inferior',
            'Senos paranasales o cara simple',
            'Senos paranasales o cara simple + contraste',
            'Silla turca-hipófisis',
            'Silla turca simple + contraste',
            'Columna cervical (técnica hospitalaria)',
            'Canal espinal y contenidos cervical',
            'Canal espinal y contenidos cervical + contraste',
            'Canal espinal y contenidos torácico',
            'Canal espinal y contenidos torácico + contraste',
            'Canal espinal y contenidos lumbar',
            'Columna dorsal (técnica hospitalaria)',
            'Columna lumbosacra (técnica hospitalaria)',
            'Columna mielográfica',
            'Columna total',
            'Cuello simple',
            'Cuello simple + contraste',
            'Laringe simple',
            'Laringe simple + contraste',
            'Mamas simple',
            'Mamas sin contraste',
            'Mamas simple con implante',
            'Tórax para linfadenopatía hiliar y mediastinal',
            'Tórax para linfadenopatía hiliar y mediastinal + contraste',
            'Tórax simple',
            'Tórax simple + contraste',
            'Abdomen completo y pelvis simple',
            'Abdomen completo y pelvis simple + contraste',
            'Abdomen simple',
            'Abdomen superior simple + contraste',
            'Pélvica simple',
            'Pélvica simple + contraste',
            'Próstata multiparamétrica',
            'Colangioresonancia + estudios totales (incluye estudios totales)',
            'Plexo braquial simple',
            'Plexo braquial simple + contraste',
            'Cualquier articulación de miembro superior',
            'Cualquier articulación de miembro superior + contraste',
            'Angioresonancia de miembros superiores simple y contrastada',
            'Cualquier articulación de miembro inferior',
            'Cualquier articulación de miembro inferior + contraste',
            'Angioresonancia aorta abdominal sin contraste',
        ],
    ];

    public function run(): void
    {
        foreach (['users', 'procedimiento', 'grupo_procedimiento'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('Falta la tabla '.$table.'. Ejecute primero las migraciones base.');
            }
        }

        $actor = (string) DB::table('users')->where('estado', 1)->orderBy('id')->value('id');
        if ($actor === '') {
            $actor = (string) DB::table('users')->orderBy('id')->value('id');
        }
        if ($actor === '') {
            throw new RuntimeException('Se requiere al menos un usuario para cargar Imagenologia.');
        }

        DB::disableQueryLog();
        DB::transaction(function () use ($actor) {
            $now = now();
            foreach (self::CATALOG as $groupName => $procedures) {
                $groupId = $this->groupId($groupName, $actor, $now);
                foreach ($procedures as $procedureName) {
                    $this->seedProcedure($procedureName, $groupName, $groupId, $actor, $now);
                }
            }
        });

        if ($this->command) {
            $count = array_sum(array_map('count', self::CATALOG));
            $this->command->info($count.' estudios de Imagenologia cargados en '.count(self::CATALOG).' agrupadores.');
        }
    }

    private function groupId(string $name, string $actor, $now): int
    {
        $existing = DB::table('grupo_procedimiento as grupo')
            ->whereRaw('UPPER(TRIM(grupo.nombre)) = ?', [mb_strtoupper($name, 'UTF-8')])
            ->where(function ($query) {
                $query->where('grupo.tipo_procedimiento', 2)->orWhere('grupo.id', 20);
            })
            ->select('grupo.*')
            ->orderBy('grupo.id')
            ->first();
        $values = $this->onlyColumns('grupo_procedimiento', [
            'nombre' => $name,
            'descripcion' => 'Estudios de '.$name.' del catálogo institucional de imágenes',
            'tipo_procedimiento' => 2,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP,
            'ip_modificacion' => self::USER_IP,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($existing) {
            unset($values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
            DB::table('grupo_procedimiento')->where('id', $existing->id)->update($values);

            return (int) $existing->id;
        }

        return (int) DB::table('grupo_procedimiento')->insertGetId($values);
    }

    private function seedProcedure(string $name, string $groupName, int $groupId, string $actor, $now): void
    {
        $normalizedName = mb_strtoupper(trim($name), 'UTF-8');
        $existing = DB::table('procedimiento as procedimiento')
            ->whereRaw('UPPER(TRIM(procedimiento.nombre)) = ?', [$normalizedName])
            ->where(function ($query) use ($groupId) {
                $query->where('procedimiento.id_grupo_procedimiento', $groupId)
                    ->orWhere('procedimiento.id_grupo_procedimiento', 20);
            })
            ->select('procedimiento.id')
            ->orderByRaw('CASE WHEN procedimiento.id_grupo_procedimiento = ? THEN 0 ELSE 1 END', [$groupId])
            ->orderBy('procedimiento.id')
            ->first();

        $values = $this->onlyColumns('procedimiento', [
            'nombre' => $normalizedName,
            'observacion' => $groupName,
            'cardiologia' => 0,
            'estado' => 1,
            'deleted_at' => null,
            'id_grupo_procedimiento' => $groupId,
            'laboratorio' => 0,
            'quirofano' => 0,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP,
            'ip_modificacion' => self::USER_IP,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($existing) {
            unset($values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
            DB::table('procedimiento')->where('id', $existing->id)->update($values);

            return;
        }

        DB::table('procedimiento')->insert($values);
    }

    private function onlyColumns(string $table, array $values): array
    {
        return array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
    }
}
