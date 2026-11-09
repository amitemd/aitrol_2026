<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportV3ConsentFormats extends Migration
{
    const SOURCE = 'V3-IMPORT-EXP-20260901';

    public function up()
    {
        if (! Schema::hasTable('formato_consentimiento')) {
            return;
        }
        $columns = array_flip(Schema::getColumnListing('formato_consentimiento'));
        $path = database_path('data/formato_consentimiento.json');
        $items = json_decode(file_get_contents($path), true);
        if (! is_array($items)) {
            throw new RuntimeException('El catálogo de consentimientos no es válido.');
        }
        $now = date('Y-m-d H:i:s');
        foreach ($items as $item) {
            $name = trim((string) $item['nombre']);
            $query = DB::table('formato_consentimiento')->where('nombre', $name);
            if (isset($columns['id_empresa'])) {
                $query->whereNull('id_empresa');
            }
            $existing = $query->first();
            $payload = ['id_procedimiento' => null, 'nombre' => $name, 'estado' => (int) $item['estado'], 'auditoria' => self::SOURCE,
                'pdf' => $item['pdf'], 'imagen_url' => $item['imagen_url'], 'consiste' => $item['consiste'], 'realiza' => $item['realiza'],
                'duracion_estimada' => $item['duracion_estimada'], 'beneficios' => $item['beneficios'], 'riesgos_frecuentes' => $item['riesgos_frecuentes'],
                'riesgos_infrecuentes' => $item['riesgos_infrecuentes'], 'riesgos_adicionales' => $item['riesgos_adicionales'],
                'alternativas' => $item['alternativas'], 'manejo' => $item['manejo'], 'consecuencias' => $item['consecuencias'],
                'check_labs' => (int) $item['check_labs'], 'updated_at' => $now];
            $payload = array_intersect_key($payload, $columns);
            if ($existing) {
                $changes = [];
                foreach ($payload as $field => $value) {
                    if (($existing->$field === null || $existing->$field === '') && $value !== null) {
                        $changes[$field] = $value;
                    }
                }
                if ($changes) {
                    DB::table('formato_consentimiento')->where('id', $existing->id)->update($changes);
                }
            } else {
                $payload += ['id_empresa' => null, 'archivo' => null, 'ip_creacion' => null, 'ip_modificacion' => null,
                    'id_usuariocrea' => null, 'id_usuariomod' => null, 'created_at' => $now];
                $payload = array_intersect_key($payload, $columns);
                DB::table('formato_consentimiento')->insert($payload);
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('formato_consentimiento')) {
            DB::table('formato_consentimiento')->where('auditoria', self::SOURCE)->delete();
        }
    }
}
