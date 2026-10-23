<?php
namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\DB;

class GatewayAttachmentController extends Controller
{
    // El token del enlace autoriza la descarga desde Gateway sin una sesión en la sucursal.
    public function download($token)
    {
        abort_unless(preg_match('/^[a-f0-9]{64}$/', $token), 404);
        $attachment = DB::table('gateway_ticket_attachments')->where('token_hash', hash('sha256', $token))->first();
        abort_unless($attachment, 404);
        $path = rtrim(config('filesystems.disks.local.root'), '/\\') . DIRECTORY_SEPARATOR . $attachment->path;
        abort_unless(is_file($path), 404);
        $preview = request()->query('preview') === '1';
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $canPreview = in_array($mime, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true);
        abort_if($preview && !$canPreview, 415);

        $headers = [
            'Content-Type' => $preview ? $mime : 'application/octet-stream', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store', 'Referrer-Policy' => 'no-referrer',
            'Vary' => 'Origin',
        ];
        $gateway = parse_url((string) config('gateway.url'));
        if ($preview && isset($gateway['scheme'], $gateway['host']) && in_array($gateway['scheme'], ['http', 'https'], true)) {
            $origin = $gateway['scheme'] . '://' . $gateway['host'] . (isset($gateway['port']) ? ':' . $gateway['port'] : '');
            if (request()->header('Origin') === $origin) {
                $headers['Access-Control-Allow-Origin'] = $origin;
            }
        }
        return response()->download($path, $attachment->name, $headers, $preview ? 'inline' : 'attachment');
    }
}
