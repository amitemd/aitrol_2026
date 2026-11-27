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
        $orientar = $abrir || preg_match('/^(?:donde|como|ruta (?:de|para)|en que (?:parte|modulo)|quiero|crear|registrar|agregar|nuevo|nueva)\b/', $texto);
        $resumen = preg_match('/\b(?:que es aitrol|para que sirve aitrol|de que se trata (?:aitrol|este proyecto)|cuantos modulos|que modulos|cuales son los modulos|modulos (?:de|que tiene) aitrol|arquitectura|estructura del proyecto)\b/', $texto);
        $nombreSuelto = ! $orientar && preg_match('/^[a-z]{5,32}$/D', $texto);
        if (! $orientar && ! $resumen && ! $nombreSuelto) {
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

        $texto = preg_replace('/^(?:(?:donde|como|en que (?:parte|modulo))(?: puedo| se)?(?: esta| estan| encuentro| veo| consulto)?|ruta (?:de|para)|abreme|abre|abrir|llevame(?: a)?|ir a|quiero)\b\s*/', '', $texto);
        $texto = preg_replace('/^(?:el )?formulario (?:para|de)\s+/', '', $texto);
        $verbos = ['creo' => 'crear', 'registro' => 'registrar', 'agrego' => 'agregar'];
        $texto = preg_replace_callback('/^(creo|registro(?! de\b)|agrego)\b/', fn (array $coincidencia): string => $verbos[$coincidencia[1]], $texto);
        $consulta = $this->frase($texto);

        [$candidatos, $puntaje] = $this->buscarDestinos($readme, $consulta);
        if ($nombreSuelto && $puntaje === 0) {
            return null;
        }
        if ($candidatos === []) {
            return $abrir ? ['respuesta' => 'No encontré esa pantalla en la guía de Aitrol. Indica el nombre del módulo o la tarea que quieres abrir.'] : null;
        }

        $hayVarias = count($candidatos) > 1;
        $opciones = [];
        foreach ($candidatos as [$titulo, $ruta, $modal, $indicacion]) {
            if (! $this->permitida($ruta) || ($modal !== '—' && ! $this->permitida($modal))) {
                continue;
            }

            $confirmar = $nombreSuelto || $puntaje > 1 || ($puntaje === 1 && $modal !== '—');
            $opciones[] = [
                'respuesta' => $confirmar ? "¿Te refieres a «{$titulo}»? Pulsa el enlace para abrir esa opción." : $indicacion,
                'destino' => [
                    'titulo' => $titulo,
                    'ruta' => $ruta,
                    'url' => route($ruta),
                    'modal' => $modal === '—' ? null : route($modal),
                    'abrir' => $abrir && ! $hayVarias && ! $confirmar,
                ],
            ];
        }

        if ($opciones === []) {
            return ['respuesta' => 'Tu usuario no tiene acceso a esa opción de Aitrol.'];
        }
        if ($hayVarias) {
            return [
                'respuesta' => '¿Qué pantalla quieres abrir? Elige el enlace que corresponda.',
                'sugerencias' => array_slice(array_column($opciones, 'destino'), 0, 3),
            ];
        }

        return $opciones[0];
    }

    /** @return array{list<array{string, string, string, string}>, int} */
    private function buscarDestinos(string $readme, string $consulta): array
    {
        $candidatos = [];
        $mejor = PHP_INT_MAX;
        foreach (explode("\n", $this->seccion($readme, 'rutas')) as $linea) {
            $partes = array_map(trim(...), explode('|', trim(trim($linea), '|')));
            if (count($partes) !== 5) {
                continue;
            }
            [$titulo, $ruta, $modal, $frases, $indicacion] = $partes;
            foreach (explode(';', $frases) as $frase) {
                $puntaje = $this->puntaje($consulta, $this->frase($frase));
                if ($puntaje === null || $puntaje > $mejor) {
                    continue;
                }
                if ($puntaje < $mejor) {
                    $candidatos = [];
                    $mejor = $puntaje;
                }
                $candidatos[$ruta.'|'.$modal] = [$titulo, $ruta, $modal, $indicacion];
            }
        }

        return [array_values($candidatos), $mejor];
    }

    private function puntaje(string $consulta, string $frase): ?int
    {
        if ($consulta === $frase) {
            return 0;
        }
        $entrada = explode(' ', $consulta);
        $alias = explode(' ', $frase);
        $distintas = array_diff_assoc($alias, $entrada);
        if (count($entrada) !== count($alias) || count($distintas) !== 1) {
            return null;
        }

        $posicion = array_key_first($distintas);
        $palabra = $entrada[$posicion];
        $esperada = $alias[$posicion];
        if (strlen($palabra) < 5 || strlen($esperada) < 5 || ! ctype_alpha($palabra) || ! ctype_alpha($esperada)) {
            return null;
        }
        $limite = strlen($esperada) >= 8 ? 2 : 1;
        if (abs(strlen($palabra) - strlen($esperada)) > $limite) {
            return null;
        }
        $distancia = levenshtein($palabra, $esperada);
        if ($distancia > $limite) {
            return null;
        }

        $repetidas = '/([a-z])\1+/';

        return preg_replace($repetidas, '$1', $palabra) === preg_replace($repetidas, '$1', $esperada) ? 1 : $distancia + 1;
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
