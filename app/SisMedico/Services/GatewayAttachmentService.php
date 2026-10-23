<?php
namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GatewayAttachmentService
{
    public function prepare($externalId, $userId, array $files)
    {
        $existing = DB::table('gateway_ticket_attachments')->where('external_id', $externalId)->where('user_id', $userId)->orderBy('id')->get();
        if (count($existing)) {
            if (count($files)) {
                throw new \RuntimeException('La evidencia ya está guardada. Reintente sin seleccionar archivos para conservar los adjuntos originales.');
            }
            return $existing;
        }
        if (count($files) && DB::table('gateway_tickets')->where('external_id', $externalId)->exists()) {
            throw new \RuntimeException('Este ticket ya fue enviado o está pendiente. Cree un nuevo ticket para adjuntar nueva evidencia.');
        }
        $paths = [];
        try {
            DB::transaction(function () use ($externalId, $userId, $files, &$paths) {
                foreach ($files as $file) {
                    $token = bin2hex(random_bytes(32));
                    $path = 'gateway-evidence/' . bin2hex(random_bytes(32));
                    $stream = fopen($file->getRealPath(), 'rb');
                    try {
                        $stored = Storage::disk('local')->put($path, $stream);
                    } finally {
                        if (is_resource($stream)) { fclose($stream); }
                    }
                    if (!$stored) { throw new \RuntimeException('No se pudo guardar la evidencia. Intente nuevamente.'); }
                    $paths[] = $path;
                    DB::table('gateway_ticket_attachments')->insert([
                        'external_id' => $externalId, 'user_id' => $userId,
                        'name' => self::safeName($file->getClientOriginalName()), 'path' => $path,
                        'token_hash' => hash('sha256', $token),
                        'download_url' => route('gateway_tickets.attachment', ['token' => $token]),
                        'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            });
        } catch (\Exception $exception) {
            foreach ($paths as $path) { Storage::disk('local')->delete($path); }
            throw $exception;
        }
        return DB::table('gateway_ticket_attachments')->where('external_id', $externalId)->where('user_id', $userId)->orderBy('id')->get();
    }

    public static function safeName($name)
    {
        $name = str_replace(['/', '\\', "\r", "\n", "\0"], '_', $name);
        $name = preg_replace('/[\x00-\x1F\x7F]/', '_', $name);
        return mb_substr($name ?: 'evidencia', 0, 150, 'UTF-8');
    }
}
