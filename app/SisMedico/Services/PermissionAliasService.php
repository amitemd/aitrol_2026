<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sis_medico\Support\RequestQueryCache;
use Sis_medico\Support\RequestSchema as Schema;

/**
 * Relaciona una opción del sidebar histórico (system.*) con su equivalente
 * publicado en el árbol V3 (v3.menu.*). Ambos catálogos siguen coexistiendo,
 * pero un rol debe conceder la misma opción sin importar desde cuál se marcó.
 */
class PermissionAliasService
{
    private $aliases;

    public function equivalentKeys($key)
    {
        $key = trim((string) $key);
        if ($key === '') return array();

        $aliases = $this->aliases();
        return array_values(array_unique(array_merge(array($key), isset($aliases[$key]) ? $aliases[$key] : array())));
    }

    public function expand(array $keys)
    {
        $expanded = array();
        foreach ($keys as $key) {
            foreach ($this->equivalentKeys($key) as $equivalent) $expanded[$equivalent] = true;
        }
        return array_keys($expanded);
    }

    public function v3MenuIds(array $keys)
    {
        $ids = array();
        foreach ($this->expand($keys) as $key) {
            if (strpos($key, 'v3.menu.') !== 0) continue;
            $id = (int) substr($key, strlen('v3.menu.'));
            if ($id > 0) $ids[$id] = true;
        }
        return array_map('intval', array_keys($ids));
    }

    private function aliases()
    {
        if ($this->aliases !== null) return $this->aliases;

        $this->aliases = RequestQueryCache::remember(__METHOD__, function () {
            return $this->buildAliases();
        }, null, false, array('sidebar_items', 'new_db_menu'));
        return $this->aliases;
    }

    private function buildAliases()
    {
        $this->aliases = array();
        if (!Schema::hasTable('sidebar_items') || !Schema::hasTable('new_db_menu')) return $this->aliases;

        $sidebarItems = DB::table('sidebar_items')->where('estado', 1)->where('tipo', 'enlace')
            ->get(array('clave', 'etiqueta', 'nombre_ruta', 'url'));
        $v3Items = DB::table('new_db_menu')->where('estado_menu', 1)
            ->get(array('id_menu', 'nombre_menu', 'controlador_menu', 'funcion_menu'));

        $routes = array();
        $paths = array();
        $labels = array();
        foreach ($sidebarItems as $item) {
            if (trim((string) $item->nombre_ruta) !== '') $routes[trim((string) $item->nombre_ruta)][] = $item->clave;
            if (trim((string) $item->url) !== '') $paths[$this->path($item->url)][] = $item->clave;
            $label = $this->label($item->etiqueta);
            if ($label !== '') $labels[$label][] = $item->clave;
        }

        $v3LabelCounts = array();
        foreach ($v3Items as $item) {
            $label = $this->label($item->nombre_menu);
            if ($label !== '') $v3LabelCounts[$label] = isset($v3LabelCounts[$label]) ? $v3LabelCounts[$label] + 1 : 1;
        }

        foreach ($v3Items as $item) {
            $v3Key = 'v3.menu.'.(int) $item->id_menu;
            $matches = array();
            $controller = trim((string) $item->controlador_menu);
            $function = trim((string) $item->funcion_menu);
            if ($controller === 'legacy-route' && isset($routes[$function])) {
                $matches = array_merge($matches, $routes[$function]);
            }
            if ($controller === 'legacy-url' && isset($paths[$this->path($function)])) {
                $matches = array_merge($matches, $paths[$this->path($function)]);
            }

            // Algunas opciones dejaron de ser legacy-route al migrarse a un
            // controlador V3 nativo. En esos casos el nombre estable enlaza
            // ambos catálogos, siempre que no sea ambiguo.
            $label = $this->label($item->nombre_menu);
            if ($label !== '' && isset($labels[$label]) && count($labels[$label]) === 1
                && isset($v3LabelCounts[$label]) && $v3LabelCounts[$label] === 1) {
                $matches[] = $labels[$label][0];
            }

            foreach (array_unique($matches) as $legacyKey) $this->link($legacyKey, $v3Key);
        }
        return $this->aliases;
    }

    private function link($left, $right)
    {
        if (!isset($this->aliases[$left])) $this->aliases[$left] = array();
        if (!isset($this->aliases[$right])) $this->aliases[$right] = array();
        $this->aliases[$left][] = $right;
        $this->aliases[$right][] = $left;
    }

    private function path($value)
    {
        $path = parse_url((string) $value, PHP_URL_PATH);
        return strtolower(trim((string) $path, '/'));
    }

    private function label($value)
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(Str::ascii(trim((string) $value))));
    }
}
