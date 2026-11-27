<?php

namespace App\Services;

use App\Http\Middleware\VerificarPermiso;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

final class VickyGuiaAitrol
{
    public function responder(string $pregunta): ?array
    {
        $texto = $this->normalizar($pregunta);
        $abrir = preg_match('/^(?:abre|abreme|abrir|llevame|ir a)\b/', $texto) === 1;
        $orientar = $abrir || preg_match('/^(?:donde|como|ruta (?:de|para)|en que parte|crear|registrar|agregar|nuevo|nueva)\b/', $texto);
        $resumen = preg_match('/\b(?:que es aitrol|para que sirve aitrol|de que se trata (?:aitrol|este proyecto)|cuantos modulos|que modulos|cuales son los modulos|arquitectura|estructura del proyecto)\b/', $texto);
        if (! $orientar && ! $resumen) {
            return null;
        }

        $readme = @file_get_contents(base_path('README.md'));
        if ($readme === false) {
            return null;
        }
        if ($resumen) {
            $descripcion = $this->seccion($readme, 'resumen');

            return $descripcion !== '' ? ['respuesta' => $descripcion] : null;
        }

        $texto = preg_replace('/^(?:(?:donde|como|en que parte)(?: puedo| se)?(?: esta| estan| encuentro| veo| consulto)?|ruta (?:de|para)|abreme|abre|abrir|llevame(?: a)?|ir a)\b\s*/', '', $texto);
        $texto = preg_replace('/^(?:creo|registro|agrego)\b/', ['crear', 'registrar', 'agregar'][array_search(Str::before($texto, ' '), ['creo', 'registro', 'agrego'], true) ?: 0], $texto, 0);
        $texto = strtr($texto, ['creo ' => 'crear ', 'registro ' => 'registrar ', 'agrego ' => 'agregar ']);
        $consulta = $this->frase($texto);

        foreach (explode("\n", $this->seccion($readme, 'rutas')) as $linea) {
            $partes = array_map(trim(...), explode('|', trim(trim($linea), '|')));
            if (count($partes) !== 5 || ! in_array($consulta, array_map($this->frase(...), explode(';', $partes[3])), true)) {
                continue;
            }
            [$titulo, $ruta, $modal, , $indicacion] = $partes;
            if (! $this->permitida($ruta) || ($modal !== '—' && ! $this->permitida($modal))) {
                return ['respuesta' => 'Tu usuario no tiene acceso a esa opción de Aitrol.'];
            }

            return [
                'respuesta' => $indicacion,
                'destino' => [
                    'titulo' => $titulo,
                    'ruta' => $ruta,
                    'url' => route($ruta),
                    'modal' => $modal === '—' ? null : route($modal),
                    'abrir' => $abrir,
                ],
            ];
        }

        return $abrir ? ['respuesta' => 'No encontré esa pantalla en la guía de Aitrol. Indica el nombre del módulo o la tarea que quieres abrir.'] : null;
    }

    private function normalizar(string $texto): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', Str::lower(Str::ascii($texto))));
    }

    private function frase(string $texto): string
    {
        $texto = preg_replace('/\b(?:el|la|los|las|un|una|al)\b/', ' ', $this->normalizar($texto));
        $texto = preg_replace('/\b(?:en|de) aitrol$/', '', $texto);

        return trim(preg_replace('/\s+/', ' ', $texto));
    }

    private function seccion(string $readme, string $nombre): string
    {
        $patron = '/<!-- vicky:'.$nombre.' -->\s*(.*?)\s*<!-- \/vicky:'.$nombre.' -->/s';

        return preg_match($patron, $readme, $partes) ? trim($partes[1]) : '';
    }

    private function permitida(string $nombre): bool
    {
        $ruta = Route::getRoutes()->getByName($nombre);
        if ($ruta === null || ! in_array('GET', $ruta->methods(), true) || $ruta->parameterNames() !== []) {
            return false;
        }

        $verificada = false;
        foreach (Route::gatherRouteMiddleware($ruta) as $middleware) {
            [$clase, $argumentos] = array_pad(explode(':', $middleware, 2), 2, '');
            if ($clase === VerificarPermiso::class) {
                $verificada = true;
                if (Gate::denies('permiso', explode(',', $argumentos))) {
                    return false;
                }
            } elseif ($clase === Authorize::class) {
                $verificada = true;
                if (str_contains($argumentos, ',') || Gate::denies($argumentos)) {
                    return false;
                }
            }
        }

        return $verificada;
    }
}
