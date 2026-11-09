<?php

namespace Sis_medico\Services;

use Sis_medico\v3\contable\Menu;

class V3PermissionMenuService
{
    public function options($roots)
    {
        if (!$roots || count($roots) === 0) return '';
        // Antes se consultaban los mismos hijos al comprobar y al descender.
        // Conservar también los hijos inactivos que mostraba este editor.
        $children = Menu::query()->get()->groupBy('id_pertenece_menu');
        return $this->render($roots, $children, []);
    }

    private function render($menus, $children, array $ancestors)
    {
        $html = '';
        foreach ($menus as $menu) {
            $id = (int) $menu->id_menu;
            if (isset($ancestors[$id])) continue;
            $branch = $ancestors;
            $branch[$id] = true;
            $descendants = $children->get($id, collect());
            if ($descendants->isNotEmpty()) {
                $html .= '<optgroup label="'.e($menu->nombre_menu).'">'
                    .$this->render($descendants, $children, $branch).'</optgroup>';
            } else {
                $html .= '<option value="'.$id.'">'.e($menu->nombre_menu).'</option>';
            }
        }
        return $html;
    }
}
