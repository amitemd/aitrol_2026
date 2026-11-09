<?php

namespace Sis_medico\Services;

use Illuminate\Http\UploadedFile;
use Sis_medico\v3\contable\Compras;

class V3FileSecurityService
{
    public function validateUpload($request, $field, array $extensions = array('pdf', 'jpg', 'jpeg', 'png'), $maxBytes = 10485760)
    {
        $file = $request->file($field);
        if (!$file instanceof UploadedFile || !$file->isValid()) abort(422, 'El archivo no se recibio correctamente.');
        if ($file->getSize() <= 0 || $file->getSize() > $maxBytes) abort(422, 'El archivo supera el tamano permitido.');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $extensions, true)) abort(422, 'La extension del archivo no esta permitida.');
        $allowedMime = array(
            'pdf' => array('application/pdf'),
            'jpg' => array('image/jpeg'), 'jpeg' => array('image/jpeg'),
            'png' => array('image/png'),
            'xml' => array('application/xml', 'text/xml', 'text/plain'),
            'csv' => array('text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'),
            'ofx' => array('application/x-ofx', 'text/plain', 'application/octet-stream', 'application/xml', 'text/xml'),
            'p12' => array('application/x-pkcs12', 'application/pkcs12', 'application/octet-stream'),
            'pfx' => array('application/x-pkcs12', 'application/pkcs12', 'application/x-pkcs12-certificates', 'application/octet-stream'),
        );
        $mime = strtolower((string) $file->getMimeType());
        if (!isset($allowedMime[$extension]) || !in_array($mime, $allowedMime[$extension], true)) {
            abort(422, 'El contenido del archivo no coincide con su extension.');
        }
        return $file;
    }

    public function randomName(UploadedFile $file, $prefix = '')
    {
        $safePrefix = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $prefix);
        return ($safePrefix === '' ? '' : $safePrefix.'_').bin2hex(random_bytes(20)).'.'.strtolower($file->getClientOriginalExtension());
    }

    public function purchaseDownloadPath($filename)
    {
        if (!is_string($filename) || $filename !== basename($filename) || preg_match('/[\x00-\x1F\\\/]/', $filename)) abort(404);
        $owned = Compras::where('id_empresa', session('id_empresa'))->where('rutapdf', $filename)->exists();
        if (!$owned) abort(404);
        return $this->resolveInside(storage_path('app/v3/compras'), $filename);
    }

    public function resolveInside($baseDirectory, $relativeName)
    {
        $base = realpath($baseDirectory);
        $path = realpath($baseDirectory.DIRECTORY_SEPARATOR.basename($relativeName));
        if ($base === false || $path === false || strpos($path, $base.DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) abort(404);
        return $path;
    }
}
