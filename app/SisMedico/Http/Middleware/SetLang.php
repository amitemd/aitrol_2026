<?php

namespace Sis_medico\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLang
{
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = (string) config('localization.default_locale', 'es');
        $supportedLocales = config('localization.supported_locales', ['es']);

        if ($request->session()->get('new_system.locale_explicitly_selected') !== true) {
            $request->session()->put('lang', $defaultLocale);
        }

        $locale = (string) $request->session()->get('lang', $defaultLocale);

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
            $request->session()->put('lang', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
