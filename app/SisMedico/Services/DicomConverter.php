<?php

namespace Sis_medico\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class DicomConverter
{
    /**
     * Convierte un archivo DICOM almacenado en storage/app/hc_ima a JPEG.
     *
     * Los nombres se validan y el comando se ejecuta como una lista de
     * argumentos, sin pasar por un shell.
     */
    public function convert($sourceName, $destinationName)
    {
        $this->assertSafeFileName($sourceName, 'dcm');
        $this->assertSafeFileName($destinationName, 'jpg');

        $storageDirectory = realpath(storage_path('app/hc_ima'));
        if ($storageDirectory === false) {
            throw new RuntimeException('No existe el directorio de imágenes DICOM.');
        }

        $sourcePath = realpath($storageDirectory . DIRECTORY_SEPARATOR . $sourceName);
        if ($sourcePath === false || dirname($sourcePath) !== $storageDirectory) {
            throw new RuntimeException('No se encontró el archivo DICOM de origen.');
        }

        $destinationPath = $storageDirectory . DIRECTORY_SEPARATOR . $destinationName;
        $process = new Process([
            'dcmj2pnm',
            $sourcePath,
            $destinationPath,
            '+oj',
        ]);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful() || !is_file($destinationPath)) {
            throw new RuntimeException('No fue posible convertir el archivo DICOM.');
        }

        return $destinationPath;
    }

    private function assertSafeFileName($fileName, $expectedExtension)
    {
        if (!is_string($fileName)
            || basename($fileName) !== $fileName
            || !preg_match('/\A[a-zA-Z0-9_.-]+\z/', $fileName)
            || strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== $expectedExtension
        ) {
            throw new RuntimeException('Nombre de archivo DICOM no válido.');
        }
    }
}
