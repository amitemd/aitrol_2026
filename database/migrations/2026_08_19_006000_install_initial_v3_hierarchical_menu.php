<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallInitialV3HierarchicalMenu extends Migration
{
    const HISTORICAL_MAX_ID = 572;

    const FINANCE_ROOT_ID = 577;

    const ACCOUNTING_GROUP_ID = 582;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $catalog = $this->readCatalog();
        $this->validateHierarchy($catalog);
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            DB::transaction(function () use ($catalog) {
                $catalogIds = array_keys($catalog);

                // Los IDs históricos se conservan, pero las ramas duplicadas que no
                // pertenecen al catálogo inicial V3 dejan de mostrarse.
                DB::table('new_db_menu')
                    ->where('id_menu', '<=', self::HISTORICAL_MAX_ID)
                    ->whereNotIn('id_menu', $catalogIds)
                    ->update(['estado_menu' => 0]);

                foreach ($catalog as $id => $data) {
                    $exists = DB::table('new_db_menu')->where('id_menu', $id)->exists();
                    if ($exists) {
                        unset($data['id_menu'], $data['created_at_menu'], $data['id_usuario_creacion_menu'], $data['ip_creacion_menu']);
                        DB::table('new_db_menu')->where('id_menu', $id)->update($data);
                    } else {
                        DB::table('new_db_menu')->insert($data);
                    }
                }

                $this->normalizePermissions($catalog);
            });
        } finally {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $previous = [
            563 => ['name' => 'Contabilidad', 'parent' => null, 'order' => 0],
            36 => ['name' => 'Reportes', 'parent' => null, 'order' => 1],
            10 => ['name' => 'Configuraciones', 'parent' => null, 'order' => 2],
            530 => ['name' => 'Recursos humanos', 'parent' => null, 'order' => 3],
            2 => ['name' => 'Caja Banco', 'parent' => 563, 'order' => 3],
            6 => ['name' => 'Proveedores', 'parent' => 563, 'order' => 4],
            5 => ['name' => 'Clientes', 'parent' => 563, 'order' => 5],
            4 => ['name' => 'Compras', 'parent' => 563, 'order' => 6],
            3 => ['name' => 'Ventas', 'parent' => 563, 'order' => 7],
            61 => ['name' => 'Inventario', 'parent' => 563, 'order' => 8],
            51 => ['name' => 'Activos Fijos', 'parent' => 563, 'order' => 9],
            66 => ['name' => 'Tributario', 'parent' => 563, 'order' => 10],
            177 => ['name' => 'Importaciones', 'parent' => 563, 'order' => 11],
            204 => ['name' => 'Guía de remisión', 'parent' => 563, 'order' => 12],
        ];

        $catalog = $this->readCatalog();
        $createdIds = array_values(array_filter(array_keys($catalog), function ($id) {
            return $id > self::HISTORICAL_MAX_ID;
        }));
        $recoveredLegacyIds = $this->descendantIds([81, 87, 91, 105, 111, 140, 144, 146, 171, 212, 235], $catalog);

        DB::transaction(function () use ($previous, $createdIds, $recoveredLegacyIds) {
            foreach ($previous as $id => $item) {
                DB::table('new_db_menu')->where('id_menu', $id)->update([
                    'nombre_menu' => $item['name'],
                    'id_pertenece_menu' => $item['parent'],
                    'orden_menu' => $item['order'],
                ]);
            }

            if ($recoveredLegacyIds) {
                DB::table('new_db_menu')->whereIn('id_menu', $recoveredLegacyIds)->update(['estado_menu' => 0]);
            }
            if ($createdIds) {
                DB::table('new_db_menu')->whereIn('id_menu', $createdIds)->delete();
            }
            $this->removePermissions(array_values(array_unique(array_merge($createdIds, $recoveredLegacyIds))));
        });
    }

    private function readCatalog()
    {
        $path = database_path('data/v3_menu.csv');
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('No se encontró database/data/v3_menu.csv.');
        }

        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);
        if ($headers) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        }
        if (! $headers || ! in_array('id_menu', $headers, true)) {
            fclose($handle);
            throw new RuntimeException('El catálogo V3 no tiene una cabecera válida.');
        }

        $catalog = [];
        $now = date('Y-m-d H:i:s');
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($headers)) {
                fclose($handle);
                throw new RuntimeException('El catálogo V3 contiene una fila incompleta.');
            }

            $row = array_combine($headers, $values);
            $id = (int) $row['id_menu'];
            if ($id <= 0 || isset($catalog[$id])) {
                fclose($handle);
                throw new RuntimeException('El catálogo V3 contiene un ID inválido o duplicado: '.$id);
            }

            $catalog[$id] = [
                'id_menu' => $id,
                'nombre_menu' => $this->nullable($row['nombre_menu']),
                'icon_menu' => $this->nullable($row['icon_menu']),
                'controlador_menu' => $this->nullable($row['controlador_menu']),
                'funcion_menu' => $this->nullable($row['funcion_menu']),
                'id_usuario_creacion_menu' => $this->nullable($row['id_usuario_creacion_menu']) ?: env('BOOTSTRAP_SYSTEMS_ID', '0000000001'),
                'id_usuario_modificacion_menu' => env('BOOTSTRAP_SYSTEMS_ID', '0000000001'),
                'created_at_menu' => $this->timestamp($row['created_at_menu'], $now),
                'updated_at_menu' => $now,
                'ip_creacion_menu' => $this->nullable($row['ip_creacion_menu']) ?: '127.0.0.1',
                'ip_modificacion_menu' => '127.0.0.1',
                'orden_menu' => $this->integerOrNull($row['orden_menu']),
                'estado_menu' => (int) $row['estado_menu'] === 1 ? 1 : 0,
                'id_pertenece_menu' => $this->integerOrNull($row['id_pertenece_menu']),
                'color_menu' => $this->nullable($row['color_menu']),
            ];
        }
        fclose($handle);

        return $catalog;
    }

    private function validateHierarchy(array $catalog)
    {
        foreach ($catalog as $id => $row) {
            $parent = $row['id_pertenece_menu'];
            if ($parent !== null && ! isset($catalog[$parent])) {
                throw new RuntimeException('El menú '.$id.' referencia al padre inexistente '.$parent.'.');
            }

            $seen = [$id => true];
            while ($parent !== null) {
                if (isset($seen[$parent])) {
                    throw new RuntimeException('El catálogo V3 contiene un ciclo en el menú '.$id.'.');
                }
                $seen[$parent] = true;
                $parent = $catalog[$parent]['id_pertenece_menu'];
            }
        }
    }

    private function normalizePermissions(array $catalog)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        $catalogIds = array_keys($catalog);
        $catalogSet = array_fill_keys($catalogIds, true);
        $systemsId = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $rows = DB::table('new_db_permisos_usuario')->get();

        foreach ($rows as $permission) {
            $current = json_decode($permission->id_submenu_permiso, true);
            $current = is_array($current) ? array_values(array_unique(array_map('intval', $current))) : [];

            if ((string) $permission->id_usuario_permiso === (string) $systemsId) {
                $current = $catalogIds;
            } else {
                $current = array_values(array_filter($current, function ($id) use ($catalogSet) {
                    return $id > self::HISTORICAL_MAX_ID || isset($catalogSet[$id]);
                }));
                $current = $this->withAncestors($current, $catalog);
            }

            sort($current);
            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $permission->id_permiso)
                ->update(['id_submenu_permiso' => json_encode(array_values($current))]);
        }
    }

    private function withAncestors(array $ids, array $catalog)
    {
        $result = array_fill_keys($ids, true);
        foreach ($ids as $id) {
            $parent = isset($catalog[$id]) ? $catalog[$id]['id_pertenece_menu'] : null;
            while ($parent !== null && isset($catalog[$parent])) {
                $result[$parent] = true;
                $parent = $catalog[$parent]['id_pertenece_menu'];
            }
        }

        return array_map('intval', array_keys($result));
    }

    private function descendantIds(array $rootIds, array $catalog)
    {
        $result = array_fill_keys($rootIds, true);
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($catalog as $id => $row) {
                $parent = $row['id_pertenece_menu'];
                if ($parent !== null && isset($result[$parent]) && ! isset($result[$id])) {
                    $result[$id] = true;
                    $changed = true;
                }
            }
        }

        return array_map('intval', array_keys($result));
    }

    private function removePermissions(array $ids)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $current = json_decode($permission->id_submenu_permiso, true);
            $current = is_array($current) ? array_map('intval', $current) : [];
            $current = array_values(array_diff($current, $ids));
            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $permission->id_permiso)
                ->update(['id_submenu_permiso' => json_encode($current)]);
        }
    }

    private function nullable($value)
    {
        return $value === '' || strtoupper((string) $value) === 'NULL' ? null : $value;
    }

    private function integerOrNull($value)
    {
        $value = $this->nullable($value);

        return $value === null ? null : (int) $value;
    }

    private function timestamp($value, $fallback)
    {
        $value = $this->nullable($value);

        return $value && strtotime($value) !== false ? $value : $fallback;
    }
}
