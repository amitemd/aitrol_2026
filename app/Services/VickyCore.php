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

    private const GUIAS = [
        'ventas' => ['workflow' => 'sales', 'permiso' => 'ventas.reportes.index'],
        'compras' => ['workflow' => 'purchasing', 'permiso' => 'compras.reportes.index'],
        'inventario' => ['workflow' => 'inventory', 'permiso' => 'inventarios.productos.index'],
        'inventarios' => ['workflow' => 'inventory', 'permiso' => 'inventarios.productos.index'],
        'contabilidad' => ['workflow' => 'accounting', 'permiso' => 'contabilidad.asientos.index'],
        'caja y bancos' => ['workflow' => 'finance', 'permiso' => 'caja-bancos.cuentas.index'],
        'cuentas por cobrar' => ['workflow' => 'payments', 'permiso' => 'clientes.reportes.cuentas-por-cobrar'],
        'cuentas por pagar' => ['workflow' => 'payments', 'permiso' => 'proveedores.reportes.cuentas-por-pagar'],
        'tributario' => ['workflow' => 'tax', 'permiso' => 'tributario.declaraciones.index'],
    ];

    public function __construct(private readonly VickyCoreOutbox $pendientes) {}

    /** @return array{workflow: string, permiso: string}|null */
    public function guiaSolicitada(string $texto): ?array
    {
        if (! preg_match('/\A\s*[¿?]?\s*como funciona(?:n)?\s+(?:(?:el|la|las|los|modulo de)\s+){0,2}([a-z ]+?)(?:\s+(?:en aitrol|del erp))?\s*[?!.]*\z/u', $texto, $coincidencias)) {
            return null;
        }

        return self::GUIAS[$coincidencias[1]] ?? null;
    }

    public function consultarGuia(int $empresa, string $workflow): ?string
    {
        $conexion = $this->conexion();
        if ($conexion === null) {
            return null;
        }
        try {
            $respuesta = Http::acceptJson()->asJson()->withToken($conexion['token'])->withoutRedirecting()
                ->connectTimeout(1)->timeout(32)
                ->post($conexion['url'].'/v1/business/consult/aitrol_2026', [
                    'requestId' => (string) Str::uuid(),
                    'tenantKey' => hash_hmac('sha256', (string) $empresa, $conexion['token']),
                    'workflow' => $workflow,
                ]);
            $texto = $respuesta->successful() ? $respuesta->json('answer') : null;

            return is_string($texto) && trim($texto) !== '' ? Str::limit(trim($texto), 3000) : null;
        } catch (ConnectionException) {
            return null;
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
    public function registrar(int $empresa, string $estado, int $duracion, ?array $ambito): void
    {
        $conexion = $this->conexion();
        if ($conexion === null) {
            return;
        }
        ['url' => $url, 'token' => $token] = $conexion;

        $evento = [
            'requestId' => (string) Str::uuid(),
            'tenantKey' => hash_hmac('sha256', (string) $empresa, $token),
            'status' => $estado,
            'durationMs' => min(120_000, max(0, $duracion)),
        ];
        if ($estado !== 'rejected' && $ambito !== null) {
            $evento['workflow'] = $ambito['workflow'];
            $evento['operation'] = $ambito['operation'];
        }

        try {
            $this->pendientes->guardar($evento);
        } catch (Throwable) {
            Log::warning('No se pudo guardar el registro pendiente de Vicky Core.', ['request_id' => $evento['requestId']]);
            defer(fn (): bool => $this->enviar($url, $token, $evento), always: true);

            return;
        }
        defer(fn () => $this->pendientes->despachar(fn (array $registro): bool => $this->enviar($url, $token, $registro)), always: true);
    }

    private function enviar(string $url, string $token, array $evento): bool
    {
        try {
            $respuesta = Http::acceptJson()->asJson()->withToken($token)->withoutRedirecting()
                ->connectTimeout(1)->timeout(2)
                ->post($url.'/v1/business/ingress/aitrol_2026', $evento);
            if ($respuesta->successful()) {
                return true;
            }
        } catch (ConnectionException) {
            // Si Core no responde, el archivo queda pendiente para la próxima consulta.
        }
        Log::warning('No se entregó el registro de Vicky Core.', ['request_id' => $evento['requestId']]);

        return false;
    }

    /** @return array{url: string, token: string}|null */
    private function conexion(): ?array
    {
        $url = config('services.vicky.core_url');
        $token = config('services.vicky.core_erp_token');
        if (($url === null || $url === '') && ($token === null || $token === '')) {
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
            if (! is_array($datos) || count($datos) !== 2) {
                return null;
            }
            $url = $datos['url'] ?? null;
            $token = $datos['token'] ?? null;
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
