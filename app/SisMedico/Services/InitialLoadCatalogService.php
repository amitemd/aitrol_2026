<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class InitialLoadCatalogService
{
    /**
     * Catálogo mínimo que la plantilla de seguros puede referenciar.
     * Es idempotente y reactiva los nombres canónicos si ya existían.
     */
    public function ensureInsuranceTypes($actorId, $ip = '127.0.0.1')
    {
        if (!Schema::hasTable('seguro_tipos') || empty($actorId)) return 0;
        $now = date('Y-m-d H:i:s'); $changed = 0;
        $types = array(
            array('nombre' => 'PUBLICO', 'detalle' => 'COBERTURA DEL SECTOR PUBLICO', 'abreviatura' => 'PUB'),
            array('nombre' => 'PRIVADO', 'detalle' => 'COBERTURA DEL SECTOR PRIVADO', 'abreviatura' => 'PRI'),
        );
        foreach ($types as $type) {
            $existing = DB::table('seguro_tipos')->whereRaw('UPPER(nombre) = ?', array($type['nombre']))->first();
            $values = $type + array('estado' => 1, 'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip, 'updated_at' => $now);
            if ($existing) {
                DB::table('seguro_tipos')->where('id', $existing->id)->update($values);
            } else {
                $values += array('id_usuariocrea' => $actorId, 'ip_creacion' => $ip, 'created_at' => $now);
                DB::table('seguro_tipos')->insert($values);
            }
            $changed++;
        }
        return $changed;
    }
}
