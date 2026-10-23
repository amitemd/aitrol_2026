<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Session;
use App;
use Config;

class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //return $next($request);
        if (session()->has('lang')) {
            $lang = session()->get('lang');
            //dd("exito");
            //dd("entra");
        } else {
            $lang = config('app.locale');
            if (is_null($lang)) {
                $lang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

                if ($lang != 'es' && $lang != 'en') {
                    $lang = 'en';
                }
            }
        }
        //dd($lang);
        App::setLocale($lang);
        return $next($request);
    }
}
