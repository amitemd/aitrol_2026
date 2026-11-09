<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Empresa;
use Sis_medico\v3\contable\V3ApiClient;

class AuthenticateV3ApiClient
{
    public function handle($request, Closure $next, $requiredScope = null)
    {
        if (!Schema::hasTable('v3_api_clients')) {
            return $this->error('API no configurada.', 503);
        }

        $plainToken = $request->bearerToken();
        if (!$plainToken) $plainToken = $request->header('X-V3-API-Key');
        if (!is_string($plainToken) || strlen($plainToken) < 32 || strlen($plainToken) > 160) {
            return $this->error('Credenciales requeridas.', 401);
        }

        $client = V3ApiClient::where('token_hash', hash('sha256', $plainToken))
            ->where('estado', 1)
            ->first();

        if (!$client || ($client->expires_at && strtotime($client->expires_at) <= time())) {
            return $this->error('Credenciales inválidas o vencidas.', 401);
        }
        if ($requiredScope && !$client->allows($requiredScope)) {
            return $this->error('La credencial no autoriza esta operación.', 403);
        }

        $company = Empresa::where('id', $client->id_empresa)->where('estado', 1)->first();
        if (!$company) return $this->error('Empresa no disponible.', 403);

        $request->attributes->set('v3_api_client', $client);
        $request->attributes->set('v3_api_company_id', (string) $company->id);

        V3ApiClient::where('id', $client->id)->update(array(
            'last_used_at' => date('Y-m-d H:i:s'),
            'last_used_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }

    private function error($message, $status)
    {
        return response()->json(array('error' => $message), $status)
            ->header('Cache-Control', 'no-store, private')
            ->header('Pragma', 'no-cache');
    }
}
