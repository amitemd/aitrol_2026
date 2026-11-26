<?php

namespace Sis_medico\Services;

class TransanestheticCanvasImage
{
    public static function dataUri($storedPath, $publicRoot = null, $storageRoot = null)
    {
        $storedPath = trim((string) $storedPath);
        if ($storedPath === '') {
            return null;
        }

        if (strpos($storedPath, 'data:image/') === 0) {
            return $storedPath;
        }

        $resolved = self::resolve($storedPath, $publicRoot, $storageRoot);
        if ($resolved === null) {
            return null;
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($resolved) : false;
        if (! is_string($mime) || strpos($mime, 'image/') !== 0) {
            $mime = 'image/png';
        }

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($resolved));
    }

    public static function resolve($storedPath, $publicRoot = null, $storageRoot = null)
    {
        $storedPath = trim((string) $storedPath);
        if ($storedPath === '') {
            return null;
        }

        $publicRoot = $publicRoot ?: public_path();
        $storageRoot = $storageRoot ?: storage_path();
        $imageName = basename(str_replace('\\', '/', $storedPath));
        $candidates = [
            $publicRoot.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'transanestesico'.DIRECTORY_SEPARATOR.$imageName,
            $publicRoot.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'trasnanestesico'.DIRECTORY_SEPARATOR.$imageName,
            $storageRoot.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'hc_ima'.DIRECTORY_SEPARATOR.'trans'.DIRECTORY_SEPARATOR.$imageName,
            $publicRoot.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'hc_ima'.DIRECTORY_SEPARATOR.'trans'.DIRECTORY_SEPARATOR.$imageName,
            $storageRoot.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'hc_ima'.DIRECTORY_SEPARATOR.$imageName,
            $publicRoot.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'hc_ima'.DIRECTORY_SEPARATOR.$imageName,
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
