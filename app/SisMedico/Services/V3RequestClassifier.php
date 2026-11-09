<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;

class V3RequestClassifier
{
    const ACTIONS = array('view', 'create', 'update', 'delete', 'approve', 'export');
    const READ_ONLY_OPERATIONS = array('estadosdecuenta');

    public function module(Request $request)
    {
        $segments = $request->segments();
        return isset($segments[1]) ? strtolower(preg_replace('/[^a-z0-9_-]/i', '', $segments[1])) : 'admin';
    }

    public function submodule(Request $request)
    {
        $value = $request->route('submodulo');
        if (!is_null($value)) return (string) $value;
        $segments = $request->segments();
        return isset($segments[2]) ? (string) $segments[2] : '';
    }

    public function isMutationName($submodule)
    {
        $name = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $submodule));
        if ($name === '') {
            return false;
        }
        // Algunos reportes empiezan por "estado" sin representar una
        // modificación. Mantenerlos explícitos evita debilitar el bloqueo
        // general de cambios de estado ejecutados mediante GET.
        if (in_array($name, self::READ_ONLY_OPERATIONS, true)) {
            return false;
        }
        return preg_match('/^(save|store|destroy|resend|approve|guardar|eliminar|anular|estado|cambiar|clasificar|modificar|subir|reenviar|revisar|procesar|confirmar|aprobar|rechazar|cerrar|reabrir|certificar|reparar|diagnosticar|emitir|actualizar|registrar|aplicar|crearinfo|verificarconciliacion)/', $name) === 1;
    }

    public function operationName(Request $request)
    {
        $extra = $request->route('extra');
        if (!is_null($extra)) return (string) $extra;
        $submodule = $request->route('submodulo');
        if (!is_null($submodule)) return (string) $submodule;
        $action = $request->route('action');
        if (!is_null($action)) return (string) $action;

        $route = $request->route();
        if ($route) {
            $method = $route->getActionMethod();
            if (preg_match('/^(store|destroy|resend|approve|export|download|pdf|xml|excel)([A-Z_]|$)/', $method)) {
                return $method;
            }
            // En rutas anidadas la operación está al final, después del recurso.
            // Usar la plantilla del enrutador evita confundir un ID con la acción.
            $segments = array_values(array_filter(explode('/', $route->uri()), function ($segment) {
                return $segment !== '' && strpos($segment, '{') === false;
            }));
            if (count($segments) > 2) return (string) end($segments);
        }
        return $this->submodule($request);
    }

    public function action(Request $request)
    {
        $name = strtolower(preg_replace('/[^a-z0-9]/i', '', $this->operationName($request)));
        if ($request->isMethod('DELETE')) return 'delete';
        if (in_array($request->method(), array('GET', 'HEAD'), true) && !$this->isMutationName($name)) {
            return $this->isExportName($name) ? 'export' : 'view';
        }
        if (preg_match('/(eliminar|anular|borrar|delete|destroy)/', $name)) return 'delete';
        if (preg_match('/(aprobar|approve|conciliacion|cerrar|reabrir|certificar|estado|confirmar|reenviar|resend|revisar)/', $name)) return 'approve';
        if ($this->isExportName($name)) return 'export';
        if (preg_match('/^(save|store|guardar|subir|registrar|crearinfo)/', $name)) return 'create';
        return 'update';
    }

    private function isExportName($name)
    {
        return preg_match('/(export|excel|pdf|imprimir|descarga|download|xml)/', (string) $name) === 1;
    }
}
