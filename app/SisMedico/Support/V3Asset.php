<?php

namespace Sis_medico\Support;

class V3Asset
{
    /**
     * Use a compiled asset in production and fall back to the source file.
     * The fallback keeps deployments functional when assets have not yet
     * been compiled.
     */
    public static function url($path)
    {
        $path = ltrim($path, '/');
        $selected = $path;

        if (config('performance.minified_assets', false)) {
            $candidate = preg_replace('/\.(css|js)$/i', '.min.$1', $path);
            $sourceFile = public_path($path);
            $candidateFile = public_path($candidate);
            // Nunca servir un compilado anterior al fuente: además de estilos
            // obsoletos puede omitir validaciones y manejadores de errores.
            if ($candidate !== $path && is_file($candidateFile)
                && (!is_file($sourceFile) || filemtime($candidateFile) >= filemtime($sourceFile))) {
                $selected = $candidate;
            }
        }

        $absolute = public_path($selected);
        $version = is_file($absolute) ? (string) filemtime($absolute) : '1';

        return url($selected).'?v='.$version;
    }
}
