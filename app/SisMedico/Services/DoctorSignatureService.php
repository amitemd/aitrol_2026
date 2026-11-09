<?php

namespace Sis_medico\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Sis_medico\Firma_Usuario;

/** Stores clinical signatures separately from user avatars and normalizes them for print. */
class DoctorSignatureService
{
    const DIRECTORY = 'firmas_sellos';
    const WIDTH = 2835; // 24 cm at 300 dpi
    const HEIGHT = 1890; // 16 cm at 300 dpi

    public function store($doctorId, UploadedFile $upload, $kind, $actorId, $ip)
    {
        $column = $kind === 'rubrica' ? 'firma' : 'nombre';
        $prefix = $kind === 'rubrica' ? 'f_' : 'firma_';
        $filename = $prefix . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $doctorId)
            . '_' . bin2hex(random_bytes(8)) . '.png';
        $path = $this->directory() . DIRECTORY_SEPARATOR . $filename;
        $this->normalize($upload->getRealPath(), $path);

        try {
            DB::transaction(function () use ($doctorId, $column, $filename, $actorId, $ip) {
                DB::table('users')->where('id', $doctorId)->lockForUpdate()->first();
                $record = Firma_Usuario::where('id_usuario', $doctorId)->orderBy('id')->lockForUpdate()->first();
                if (!$record) {
                    $record = new Firma_Usuario;
                    $record->id_usuario = (string) $doctorId;
                    // nombre es NOT NULL en instalaciones históricas; la rúbrica puede cargarse primero.
                    $record->nombre = '';
                    $record->id_usuariocrea = (string) $actorId;
                    $record->ip_creacion = (string) $ip;
                }
                $record->{$column} = $filename;
                $record->id_usuariomod = (string) $actorId;
                $record->ip_modificacion = (string) $ip;
                $record->save();
            });
        } catch (Exception $exception) {
            if (is_file($path)) @unlink($path);
            throw $exception;
        }
        return $filename;
    }

    public function pathForFilename($filename)
    {
        if (!$filename) return null;
        $name = basename((string) $filename);
        $newPath = $this->directory() . DIRECTORY_SEPARATOR . $name;
        if (is_file($newPath)) return $newPath;
        $legacyPath = storage_path('app/avatars/' . $name);
        return is_file($legacyPath) ? $legacyPath : null;
    }

    public function dataUriForFilename($filename)
    {
        $path = $this->pathForFilename($filename);
        if (!$path) return null;
        $info = @getimagesize($path);
        if (!$info || empty($info['mime']) || !in_array($info['mime'], array('image/jpeg', 'image/png'), true)) {
            return null;
        }
        $contents = @file_get_contents($path);
        if ($contents === false) return null;
        return 'data:'.$info['mime'].';base64,'.base64_encode($contents);
    }

    private function directory()
    {
        $directory = storage_path('app/' . self::DIRECTORY);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new Exception('No se pudo preparar el directorio privado de firmas.');
        }
        return $directory;
    }

    private function normalize($sourcePath, $destinationPath)
    {
        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime']) || $info[0] < 1 || $info[1] < 1 || $info[0] > 10000 || $info[1] > 10000) {
            throw new Exception('La imagen de firma no es válida.');
        }
        if ($info['mime'] === 'image/jpeg') $source = @imagecreatefromjpeg($sourcePath);
        elseif ($info['mime'] === 'image/png') $source = @imagecreatefrompng($sourcePath);
        else throw new Exception('La firma debe ser una imagen JPG o PNG.');
        if (!$source) throw new Exception('No se pudo procesar la imagen de firma.');

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, self::WIDTH, self::HEIGHT, $transparent);
        $scale = min(self::WIDTH / $info[0], self::HEIGHT / $info[1]);
        $width = max(1, (int) round($info[0] * $scale));
        $height = max(1, (int) round($info[1] * $scale));
        imagecopyresampled($canvas, $source, (int) ((self::WIDTH - $width) / 2), (int) ((self::HEIGHT - $height) / 2), 0, 0, $width, $height, $info[0], $info[1]);
        if (function_exists('imageresolution')) @imageresolution($canvas, 300, 300);
        $written = imagepng($canvas, $destinationPath, 6);
        imagedestroy($source);
        imagedestroy($canvas);
        if (!$written) throw new Exception('No se pudo guardar la imagen de firma.');
    }
}
