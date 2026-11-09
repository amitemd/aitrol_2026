<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Opcion_Usuario;
use Sis_medico\SidebarItem;

class SidebarMenuService
{
    private $available;
    private $grantedOptionIds;
    private $items;

    public function available()
    {
        if ($this->available === null) {
            $this->available = Schema::hasTable('sidebar_items');
        }
        return $this->available;
    }

    public function tree($rootKey = null)
    {
        if (!$this->available() || !Auth::check()) {
            return array();
        }

        $items = $this->items();
        $children = array();
        foreach ($items as $item) {
            $parent = $item->parent_id ? (int) $item->parent_id : 0;
            if (!isset($children[$parent])) {
                $children[$parent] = array();
            }
            $children[$parent][] = $item;
        }

        if ($rootKey !== null) {
            foreach ($items as $item) {
                if ($item->clave === $rootKey) {
                    $node = $this->buildNode($item, $children);
                    return $node ? array($node) : array();
                }
            }
            return array();
        }

        return $this->buildChildren(0, $children);
    }

    public function can($key)
    {
        if (!Auth::check()) {
            return false;
        }
        if (Auth::user()->isSystemUser()) {
            return true;
        }
        if (!$this->available()) {
            return false;
        }
        foreach ($this->items() as $item) {
            if ($item->clave === $key) {
                $companyDecision = app(CompanyAccessService::class)->decision(
                    Auth::user(),
                    session('id_empresa'),
                    $key
                );
                if ($companyDecision !== null) {
                    return $companyDecision;
                }
                return $this->canAccessItem($item);
            }
        }
        return false;
    }

    public function canWithLegacy($key, array $legacyRoles, array $legacyUsers = array())
    {
        if (!Auth::check()) {
            return false;
        }
        if (in_array((string) Auth::user()->id, $legacyUsers, true)) {
            return true;
        }
        if (!$this->available()) {
            return in_array((int) Auth::user()->id_tipo_usuario, $legacyRoles, true)
                || in_array((string) Auth::user()->id, $legacyUsers, true);
        }
        return $this->can($key);
    }

    private function items()
    {
        if ($this->items === null) {
            $key = 'sidebar_menu_items_v'.$this->cacheVersion();
            $minutes = max(1, (int) config('performance.sidebar_cache_minutes', 10));
            $this->items = Cache::remember($key, $minutes, function () {
                return SidebarItem::where('estado', 1)->orderBy('orden')->orderBy('id')->get();
            });
        }
        return $this->items;
    }

    private function buildChildren($parentId, array $children)
    {
        $result = array();
        if (!isset($children[$parentId])) {
            return $result;
        }
        foreach ($children[$parentId] as $item) {
            $node = $this->buildNode($item, $children);
            if ($node) {
                $result[] = $node;
            }
        }
        return $result;
    }

    private function buildNode($item, array $children)
    {
        // Esta opción se muestra explícitamente en la vista para que SISTEMAS y
        // VIDEO no dependan de permisos dinámicos antiguos ni de su caché.
        if ($item->clave === 'waiting_room.display') {
            return null;
        }

        $childNodes = $this->buildChildren((int) $item->id, $children);
        if ($item->tipo === 'grupo' && count($childNodes) === 0) {
            return null;
        }
        if ($item->tipo !== 'grupo' && !$this->canAccessItem($item)) {
            return null;
        }

        $label = $item->etiqueta;
        if ($item->traduccion) {
            $translated = trans($item->traduccion);
            if ($translated !== $item->traduccion) {
                $label = $translated;
            }
        }

        $url = '#';
        if ($item->nombre_ruta && Route::has($item->nombre_ruta)) {
            try {
                $url = route($item->nombre_ruta);
            } catch (\Exception $exception) {
                $url = $item->url ? url($item->url) : '#';
            }
        } elseif ($item->url) {
            $url = url($item->url);
        }

        $active = $this->isActive($item);
        foreach ($childNodes as $child) {
            if ($child['active']) {
                $active = true;
                break;
            }
        }

        return array(
            'id' => $item->id,
            'key' => $item->clave,
            'label' => $label,
            'type' => $item->tipo,
            'icon' => $item->icono ?: 'fa fa-circle-o',
            'url' => $url,
            'active' => $active,
            'pinnable' => (bool) $item->fijable,
            'children' => $childNodes,
        );
    }

    private function canAccessItem($item)
    {
        if ($item->clave === 'waiting_room.display') {
            return method_exists(Auth::user(), 'canManageWaitingRoomDisplay')
                && Auth::user()->canManageWaitingRoomDisplay();
        }

        if (Auth::user()->isSystemUser()) {
            return true;
        }
        $companyDecision = app(CompanyAccessService::class)->decision(
            Auth::user(),
            session('id_empresa'),
            $item->clave
        );
        if ($companyDecision !== null) {
            return $companyDecision;
        }
        if (!$item->id_opcion) {
            return true;
        }
        return in_array((int) $item->id_opcion, $this->grantedOptionIds(), true);
    }

    private function grantedOptionIds()
    {
        if ($this->grantedOptionIds === null) {
            $role = (int) Auth::user()->id_tipo_usuario;
            $key = 'sidebar_menu_grants_v'.$this->cacheVersion().'_role_'.$role;
            $minutes = max(1, (int) config('performance.sidebar_cache_minutes', 10));
            $this->grantedOptionIds = Cache::remember($key, $minutes, function () use ($role) {
                return Opcion_Usuario::where('id_tipo_usuario', $role)
                    ->where('estado', 1)
                    ->pluck('id_opcion')
                    ->map(function ($id) { return (int) $id; })
                    ->toArray();
            });
        }
        return $this->grantedOptionIds;
    }

    public static function flushCache()
    {
        $version = (int) Cache::get('sidebar_menu_cache_version', 1);
        Cache::forever('sidebar_menu_cache_version', $version + 1);
    }

    private function cacheVersion()
    {
        return (int) Cache::get('sidebar_menu_cache_version', 1);
    }

    private function isActive($item)
    {
        $current = trim(request()->path(), '/');
        $patterns = array_filter(array_map('trim', explode(',', (string) $item->patrones_activos)));
        foreach ($patterns as $pattern) {
            $pattern = trim($pattern, '/');
            if ($current === $pattern || strpos($current, $pattern . '/') === 0) {
                return true;
            }
        }
        return false;
    }
}
