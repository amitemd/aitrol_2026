<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Prepara el menu V3 cuando una opcion gobernada abre una pantalla heredada.
 * No cambia el controlador, el metodo HTTP ni la persistencia de la pantalla.
 */
class PresentLegacyInV3
{
    public function handle($request, Closure $next)
    {
        if ($request->query('_v3') === '1' || $this->isNativeV3ClinicalSurface($request)) {
            $menuId = (int) $request->query('_v3_menu', $this->nativeV3MenuId($request));
            $request->session()->put('v3_legacy_shell', true);
            $request->session()->put('v3_legacy_menu_id', $menuId);
        }

        if (!$this->shouldPrepare($request)) {
            return $next($request);
        }

        $menuId = (int) $request->query(
            '_v3_menu',
            $request->session()->get('v3_legacy_menu_id', 0)
        );

        return app(General::class)->handle($request, function ($preparedRequest) use ($next, $menuId) {
            // Algunos controladores V2 reemplazan por completo config('data').
            // Esta copia conserva el contexto visual sin tocar datos de negocio.
            config(array('v3_legacy_context' => config('data') ?: array()));
            config(array(
                'data.v3_legacy_shell' => true,
                'data.menu_id' => $menuId,
            ));

            return $next($preparedRequest);
        });
    }

    private function isNativeV3ClinicalSurface($request)
    {
        return $request->is('rc/preanestesico*')
            || $request->is('rc/transanestesico*')
            || $request->is('rc/postanestesico*')
            || $request->is('epicrisis/final*')
            || $request->is('hospital/ordenes-clinicas*')
            || $request->is('hospital/interconsultas-clinicas*')
            || $request->is('hospital/accesos-clinicos*')
            || $request->is('hospital/seguridad-clinica*');
    }

    private function nativeV3MenuId($request)
    {
        if ($request->is('rc/preanestesico*')
            || $request->is('rc/transanestesico*')
            || $request->is('rc/postanestesico*')
            || $request->is('epicrisis/final*')) {
            // La documentación anestésica y de egreso forma parte de Atención al paciente.
            return 574;
        }

        return 0;
    }

    private function shouldPrepare($request)
    {
        if (!Auth::check()
            || !$request->session()->get('v3_legacy_shell', false)
            || !$request->isMethod('GET')
            || $request->wantsJson()
            || $request->is('v3/*')
            || $request->is('logout')) {
            return false;
        }

        return true;
    }
}
