<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

use function Illuminate\Support\defer;

final class VickyCore
{
    private const INFORMES = [
        'ventas' => ['workflow' => 'sales', 'operation' => 'summarize'],
        'compras' => ['workflow' => 'purchasing', 'operation' => 'summarize'],
        'banco' => ['workflow' => 'finance', 'operation' => 'read'],
        'cobrar' => ['workflow' => 'payments', 'operation' => 'summarize'],
        'pagar' => ['workflow' => 'payments', 'operation' => 'summarize'],
    ];

    private const FUENTES = [
        'clientes' => 'sales',
        'proveedores' => 'purchasing',
        'pacientes' => 'medical',
        'facturas_venta' => 'sales',
        'cotizaciones' => 'sales',
        'lineas_venta' => 'sales',
        'facturas_compra' => 'purchasing',
        'ordenes_compra' => 'purchasing',
        'lineas_compra' => 'purchasing',
        'productos' => 'inventory',
        'bodegas' => 'inventory',
        'categorias' => 'inventory',
        'inventario_movimientos' => 'inventory',
        'kardex' => 'inventory',
        'existencias' => 'inventory',
        'tesoreria_movimientos' => 'finance',
        'tesoreria_cierres' => 'finance',
        'tesoreria_extractos' => 'finance',
        'asientos_contables' => 'accounting',
        'lineas_contables' => 'accounting',
        'cuentas_contables' => 'accounting',
        'periodos_contables' => 'accounting',
        'activos_fijos' => 'accounting',
        'movimientos_activos' => 'accounting',
        'depreciaciones' => 'accounting',
        'mantenimiento' => 'accounting',
        'declaraciones' => 'tax',
        'documentos_tributarios' => 'tax',
    ];

    public function __construct(private readonly VickyCoreOutbox $pendientes) {}

    /** @return array{respuesta: string, consulta_id?: string, agente?: string}|null */
    public function consultarGuia(int $empresa, string $workflow, string $objetivo = 'overview', ?string $tema = null, ?string $temaRelacionado = null): ?array
    {
        $conexion = $this->conexion();
        if ($conexion === null) {
            return null;
        }
        try {
            $id = (string) Str::uuid();
            $datos = [
                'requestId' => $id,
                'tenantKey' => hash_hmac('sha256', (string) $empresa, $conexion['token']),
                'workflow' => $workflow,
            ];
            if ($objetivo !== 'overview') {
                $datos['goal'] = $objetivo;
            }
            if ($tema !== null) {
                $datos['topic'] = $tema;
            }
        if ($temaRelacionado !== null) {
            $datos['relatedTopic'] = $temaRelacionado;
        }
            $respuesta = Http::acceptJson()->asJson()->withToken($conexion['token'])->withoutRedirecting()
                ->connectTimeout(1)->timeout(32)
                ->post($conexion['url'].'/v1/business/consult/aitrol_2026', $datos);
            $texto = $respuesta->successful() ? $respuesta->json('answer') : null;
            if (! is_string($texto) || trim($texto) === '') {
                return null;
            }
            $resultado = ['respuesta' => Str::limit(trim($texto), 3000)];
            $agente = $respuesta->json('agent');
            if ($respuesta->json('requestId') === $id && is_string($agente) && preg_match('/\A[a-z][a-z0-9_-]{0,63}\z/', $agente)) {
                $resultado += ['consulta_id' => $id, 'agente' => $agente];
            }

            return $resultado;
        } catch (ConnectionException) {
            return null;
        }
    }

    /** @param array{step: string, relation: string, relatedStep?: string}|null $correccion */
    public function valorarGuia(int $empresa, string $id, string $valoracion, ?array $correccion): int
    {
        $conexion = $this->conexion();
        if ($conexion === null) {
            return 503;
        }
        $datos = [
            'requestId' => $id,
            'tenantKey' => hash_hmac('sha256', (string) $empresa, $conexion['token']),
            'rating' => $valoracion,
        ];
        if ($correccion !== null) {
            $datos['correction'] = $correccion;
        }
        try {
            return Http::acceptJson()->asJson()->withToken($conexion['token'])->withoutRedirecting()
                ->connectTimeout(1)->timeout(8)
                ->post($conexion['url'].'/v1/business/feedback/aitrol_2026', $datos)->status();
        } catch (ConnectionException) {
            return 503;
        }
    }

    /** @return array{workflow: string, operation: string}|null */
    public function ambito(string $tipo, mixed $fuente = null, mixed $operacion = null): ?array
    {
        if ($tipo !== 'operativa') {
            return self::INFORMES[$tipo] ?? null;
        }
        if (! is_string($fuente) || ! isset(self::FUENTES[$fuente])) {
            return null;
        }
        $operacion = match ($operacion) {
            'listar' => 'list',
            'contar', 'sumar', 'promediar', 'minimo', 'maximo' => 'summarize',
            default => null,
        };

        return $operacion === null ? null : ['workflow' => self::FUENTES[$fuente], 'operation' => $operacion];
    }

    /** @param array{workflow: string, operation: string}|null $ambito */
    public function iniciar(string $id, int $empresa, ?array $ambito): void
    {
        try {
            $conexion = $this->conexion();
            if ($conexion !== null) {
                $evento = $this->evento($id, $empresa, $conexion['token'], 'in_progress', 0, $ambito);
                $this->enviar($conexion['url'], $conexion['token'], $evento, 0.5);
            }
        } catch (Throwable) {
            // La consulta de Aitrol continúa aunque falle la publicación de presencia.
        }
    }

    /** @param array{workflow: string, operation: string}|null $ambito */
    public function registrar(string $id, int $empresa, string $estado, int $duracion, ?array $ambito): void
    {
        $conexion = $this->conexion();
        if ($conexion === null) {
            return;
        }
        ['url' => $url, 'token' => $token] = $conexion;

        $evento = $this->evento($id, $empresa, $token, $estado, $duracion, $ambito);

        try {
            $this->pendientes->guardar($evento);
        } catch (Throwable) {
            Log::warning('No se pudo guardar el registro pendiente de Vicky Core.', ['request_id' => $evento['requestId']]);
            defer(fn (): bool => $this->enviar($url, $token, $evento), always: true);

            return;
        }
        defer(fn () => $this->pendientes->despachar(fn (array $registro): bool => $this->enviar($url, $token, $registro)), always: true);
    }

    /** @param array{workflow: string, operation: string}|null $ambito */
    private function evento(string $id, int $empresa, string $token, string $estado, int $duracion, ?array $ambito): array
    {
        $evento = [
            'requestId' => $id,
            'tenantKey' => hash_hmac('sha256', (string) $empresa, $token),
            'status' => $estado,
            'durationMs' => min(120_000, max(0, $duracion)),
        ];

        return ! in_array($estado, ['rejected', 'unclassified'], true) && $ambito !== null
            ? $evento + $ambito
            : $evento;
    }

    private function enviar(string $url, string $token, array $evento, float $timeout = 2): bool
    {
        try {
            $respuesta = Http::acceptJson()->asJson()->withToken($token)->withoutRedirecting()
                ->connectTimeout(min(1, $timeout))->timeout($timeout)
                ->post($url.'/v1/business/ingress/aitrol_2026', $evento);
            if ($respuesta->successful()) {
                return true;
            }
        } catch (ConnectionException) {
            // Si Core no responde, el archivo queda pendiente para la próxima consulta.
        }
        if ($evento['status'] !== 'in_progress') {
            Log::warning('No se entregó el registro de Vicky Core.', ['request_id' => $evento['requestId']]);
        }

        return false;
    }

    /** @return array{url: string, token: string}|null */
    private function conexion(): ?array
    {
        $url = config('services.vicky.core_url');
        $token = config('services.vicky.core_erp_token');
        $faltaUrl = $url === null || $url === '';
        $faltaToken = $token === null || $token === '';
        if ($faltaUrl || $faltaToken) {
            $ruta = config('services.vicky.core_connection_file')
                ?: VickyCoreOutbox::directorioPrivado().DIRECTORY_SEPARATOR.'aitrol_2026.json';
            if (! is_string($ruta)) {
                return null;
            }
            if (! is_file($ruta) || is_link($ruta) || ! is_readable($ruta) || ! $this->fueraDelDirectorioWeb($ruta)) {
                return null;
            }
            $documento = @file_get_contents($ruta, false, null, 0, 2049);
            if (! is_string($documento) || strlen($documento) > 2048) {
                return null;
            }
            $datos = json_decode($documento, true, depth: 3);
            if (! is_array($datos) || count($datos) !== 2 || ! is_string($datos['url'] ?? null) || ! is_string($datos['token'] ?? null)) {
                return null;
            }
            if ((! $faltaUrl && (! is_string($url) || rtrim($url, '/') !== rtrim($datos['url'], '/')))
                || (! $faltaToken && (! is_string($token) || ! hash_equals($datos['token'], $token)))) {
                return null;
            }
            $url = $faltaUrl ? $datos['url'] : $url;
            $token = $faltaToken ? $datos['token'] : $token;
        }
        if (! is_string($url) || ! is_string($token)) {
            return null;
        }
        $url = rtrim($url, '/');
        if (! $this->urlPermitida($url) || ! preg_match('/\A[A-Za-z0-9_-]{40,128}\z/', $token)) {
            return null;
        }

        return compact('url', 'token');
    }

    private function fueraDelDirectorioWeb(string $ruta): bool
    {
        $archivo = realpath($ruta);
        $padre = dirname(base_path());
        $publico = realpath(PHP_OS_FAMILY === 'Windows' && strcasecmp(basename($padre), 'htdocs') === 0 ? $padre : public_path());
        if ($archivo === false || $publico === false) {
            return false;
        }
        $prefijo = rtrim($publico, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return PHP_OS_FAMILY === 'Windows'
            ? strncasecmp($archivo, $prefijo, strlen($prefijo)) !== 0
            : ! str_starts_with($archivo, $prefijo);
    }

    private function urlPermitida(string $url): bool
    {
        $partes = parse_url($url);
        if (! is_array($partes) || ! isset($partes['scheme'], $partes['host'])
            || array_diff(array_keys($partes), ['scheme', 'host', 'port']) !== []
            || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return $partes['scheme'] === 'https'
            || ($partes['scheme'] === 'http' && (
                in_array($partes['host'], ['localhost', '127.0.0.1', '[::1]'], true)
                || (filter_var($partes['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    && preg_match('/\A100\.(?:6[4-9]|[7-9]\d|1[01]\d|12[0-7])\./', $partes['host']))
            ));
    }
}
