<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Route;

class ClinicalRouteSafetyAuditService
{
    public function inspect()
    {
        $items = array();
        foreach (Route::getRoutes() as $route) {
            if (!in_array('GET', $route->methods(), true)) continue;
            $uri = $route->uri();
            $action = $route->getActionName();
            if (!$this->clinical($uri, $action)) continue;

            $label = $uri.' '.$action;
            $destructive = preg_match('/(elimin|borrar|anular|delete|quitar)/i', $label) === 1;
            $possiblyMutable = preg_match('/(guardar|actualiza|crear|agregar|modificar|cambiar|salvar|liberar|planillar|registr|darbaja|dar_baja|baja)/i', $label) === 1;
            $readOnlyArtifact = preg_match('/(reporte|imprimir|excel|pdf|descargar|buscar|index|show|edit)/i', $uri) === 1;
            $writes = $this->writes($action);
            if (!$writes) continue;
            if (!$destructive && (!$possiblyMutable || $readOnlyArtifact)) continue;

            $items[] = array('uri' => $uri, 'action' => $action, 'name' => $route->getName());
        }
        return array('pending' => count($items), 'routes' => $items);
    }

    private function writes($action)
    {
        if (strpos($action, '@') === false) return false;
        list($class, $method) = explode('@', $action, 2);
        try {
            $reflection = new \ReflectionMethod($class, $method);
            $lines = file($reflection->getFileName());
            $source = implode('', array_slice($lines, $reflection->getStartLine() - 1,
                $reflection->getEndLine() - $reflection->getStartLine() + 1));
            return preg_match('/(::create\s*\(|::insert|firstOrCreate|updateOrCreate|insertGetId|->save\s*\(|->update\s*\(|->delete\s*\(|DB::(?:insert|update|delete|statement)|Storage::|->store\s*\(|->move\s*\(|unlink\s*\()/i', $source) === 1;
        } catch (\Exception $exception) {
            // Si no se puede inspeccionar una acción potencialmente mutable,
            // se conserva en el inventario para no crear un falso negativo.
            return true;
        }
    }

    private function clinical($uri, $action)
    {
        return preg_match('#^(hospital|historiaclinica|hc4|inicio|cie10|receta|orden|cardio|paciente)/#i', $uri)
            || preg_match('#Controllers\\\\(hospital|hc4|hc_admision)\\\\#i', $action);
    }
}
