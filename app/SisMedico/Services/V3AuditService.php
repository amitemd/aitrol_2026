<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Utilidades;

class V3AuditService
{
    private $classifier;
    private $securityMetadata;
    private $secretKeys = array('password', 'clave', 'token', '_token', 'authorization', 'firma', 'p12');

    public function __construct(V3RequestClassifier $classifier, SecurityAccessAuditService $securityMetadata)
    {
        $this->classifier = $classifier;
        $this->securityMetadata = $securityMetadata;
    }

    public function record(Request $request, array $context = array(), $queryLog = null)
    {
        $companyId = (string) session('id_empresa');
        $userId = Auth::check() ? Auth::user()->id : '0';
        $route = $request->route();
        $routeName = $route && method_exists($route, 'getName') ? $route->getName() : null;
        $actionName = $route && method_exists($route, 'getActionName') ? $route->getActionName() : '';
        $query = $this->queryMetadata($queryLog);
        $payload = $this->sanitize($request->except(array('_token')));
        $before = isset($context['before']) ? $this->sanitize($context['before']) : null;
        $after = isset($context['after']) ? $this->sanitize($context['after']) : null;
        $client = $this->securityMetadata->requestMetadata($request);
        $location = $this->requestLocation($request);
        $correlation = $request->headers->get('X-Correlation-ID');
        if (!$correlation || !preg_match('/^[A-Za-z0-9-]{8,64}$/', $correlation)) {
            $correlation = bin2hex(random_bytes(16));
        }

        $id = DB::transaction(function () use ($request, $context, $companyId, $userId, $routeName, $actionName, $query, $payload, $before, $after, $correlation, $client, $location) {
            $previous = DB::table('new_db_auditorias')->where('id_empresa_auditoria', $companyId)
                ->orderBy('id_auditoria', 'desc')->lockForUpdate()->first(array('event_hash'));
            $previousHash = $previous && isset($previous->event_hash) ? $previous->event_hash : str_repeat('0', 64);
            $createdAt = date('Y-m-d H:i:s');
            $canonical = json_encode(array($companyId, $userId, $actionName, $payload, $before, $after, $createdAt, $previousHash));
            $eventHash = hash('sha256', $canonical);
            $agent = (string) $request->server('HTTP_USER_AGENT', 'unknown');

            return DB::table('new_db_auditorias')->insertGetId(array(
                'controlador_auditoria' => $this->classifier->module($request),
                'submodulo_auditoria' => $this->classifier->submodule($request),
                'modelo_auditoria' => isset($context['model']) ? $context['model'] : '',
                'vista_auditoria' => $routeName ?: $request->path(),
                'query_auditoria' => $query['query'],
                'parametros_auditoria' => json_encode($query['bindings']),
                'tiempo_consulta_auditoria' => $query['time'],
                'id_usuario_auditoria' => $userId,
                'id_empresa_auditoria' => $companyId,
                'ip_auditoria' => (string) $request->ip(),
                'navegador_auditoria' => $client['browser'],
                'sistema_auditoria' => $client['os'].' · '.$client['type'],
                'version_navegador_auditoria' => '',
                'latitud_auditoria' => $location['latitude'],
                'longitud_auditoria' => $location['longitude'],
                'agente_auditoria' => substr($agent, 0, 500),
                'observacion_auditoria' => isset($context['reason']) ? $context['reason'] : '',
                'obj_auditoria' => json_encode($payload),
                'correlation_id' => $correlation,
                'action_auditoria' => $this->classifier->action($request),
                'before_auditoria' => is_null($before) ? null : json_encode($before),
                'after_auditoria' => is_null($after) ? null : json_encode($after),
                'previous_hash' => $previousHash,
                'event_hash' => $eventHash,
                'created_at_auditoria' => $createdAt,
                'updated_at_auditoria' => $createdAt,
            ), 'id_auditoria');
        }, 3);
        $request->attributes->set('v3.audit.recorded', true);
        return $id;
    }

    private function requestLocation(Request $request)
    {
        $empty = array('latitude' => '', 'longitude' => '');
        if (!$request->is('v3/utilidades/saveAuditoria')) return $empty;

        $parts = explode('|', (string) $request->input('d', ''), 2);
        if (count($parts) !== 2 || !is_numeric($parts[0]) || !is_numeric($parts[1])) return $empty;

        $latitude = (float) $parts[0];
        $longitude = (float) $parts[1];
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) return $empty;

        return array(
            'latitude' => number_format($latitude, 7, '.', ''),
            'longitude' => number_format($longitude, 7, '.', ''),
        );
    }

    private function queryMetadata($queryLog)
    {
        $last = is_array($queryLog) && count($queryLog) ? end($queryLog) : array();
        return array(
            'query' => isset($last['query']) ? substr($last['query'], 0, 5000) : '',
            // Los bindings pueden contener contraseñas, identificaciones,
            // tokens o datos clínicos sin una clave descriptiva. Nunca se
            // almacenan sus valores; la consulta conserva sus placeholders.
            'bindings' => isset($last['bindings']) ? $this->protectBindings($last['bindings']) : array(),
            'time' => isset($last['time']) ? $last['time'] : 0,
        );
    }

    private function protectBindings($bindings)
    {
        if (!is_array($bindings)) return '[PROTECTED]';
        $protected = array();
        foreach ($bindings as $key => $value) {
            $protected[$key] = is_array($value) ? $this->protectBindings($value) : '[PROTECTED]';
        }
        return $protected;
    }

    private function sanitize($value, $key = '')
    {
        foreach ($this->secretKeys as $secret) if ($key !== '' && stripos($key, $secret) !== false) return '[PROTECTED]';
        if (is_array($value)) {
            $clean = array();
            foreach ($value as $itemKey => $item) $clean[$itemKey] = $this->sanitize($item, (string) $itemKey);
            return $clean;
        }
        if (is_object($value)) return $this->sanitize((array) $value, $key);
        return is_string($value) && strlen($value) > 4000 ? substr($value, 0, 4000).'...' : $value;
    }
}
