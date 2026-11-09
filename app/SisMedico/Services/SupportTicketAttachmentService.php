<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Storage;
use Sis_medico\SupportTicketDelivery;

class SupportTicketAttachmentService
{
    public function prepare(SupportTicketDelivery $delivery, array $files)
    {
        if (!$files) {
            return array();
        }
        $attachments = array();
        foreach ($files as $file) {
            $token = bin2hex(random_bytes(32));
            $path = 'support-ticket-evidence/' . bin2hex(random_bytes(32));
            $stream = fopen($file->getRealPath(), 'rb');
            try {
                $stored = Storage::disk('local')->put($path, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            if (!$stored) {
                throw new \RuntimeException('No se pudo guardar una evidencia.');
            }
            try {
                $attachment = $delivery->attachments()->create(array(
                    'name' => $this->safeName($file->getClientOriginalName()), 'path' => $path,
                    'token_hash' => hash('sha256', $token),
                    'download_url' => route('v3.support-tickets.evidence', array('token' => $token)),
                ));
            } catch (\Exception $exception) {
                Storage::disk('local')->delete($path);
                throw $exception;
            }
            $attachments[] = $attachment;
        }
        return $attachments;
    }

    public function safeName($name)
    {
        $name = str_replace(array('/', '\\', "\r", "\n", "\0"), '_', (string) $name);
        $name = preg_replace('/[\x00-\x1F\x7F]/', '_', $name);
        return mb_substr($name ?: 'evidencia', 0, 150, 'UTF-8');
    }
}
