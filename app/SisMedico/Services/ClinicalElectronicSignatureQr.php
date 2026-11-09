<?php

namespace Sis_medico\Services;

use Milon\Barcode\DNS2D;

/** Genera en PHP el QR visible usado por la firma electrónica clínica. */
class ClinicalElectronicSignatureQr
{
    public function dataUri($payload)
    {
        $payload = trim((string) $payload);
        if ($payload === '') return null;

        $svg = (new DNS2D)->getBarcodeSVG($payload, 'QRCODE', 3, 3);
        if (!$svg) return null;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
