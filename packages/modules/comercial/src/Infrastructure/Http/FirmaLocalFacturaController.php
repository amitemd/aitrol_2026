<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\FirmaLocalFactura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class FirmaLocalFacturaController
{
    public function __construct(private FirmaLocalFactura $firmas) {}

    public function show(int $registro): JsonResponse
    {
        return response()->json(['data' => $this->firmas->estado($registro)]);
    }

    public function store(Request $request, int $registro): JsonResponse|RedirectResponse
    {
        $estado = $this->firmas->firmar($registro);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $estado])
            : to_route('ventas.documentos.sri.log', $registro)->with('status', 'Factura firmada en AITROL. Pendiente de envío al SRI.');
    }

    public function download(int $registro): StreamedResponse
    {
        $xml = $this->firmas->descargar($registro);

        return response()->streamDownload(fn () => print ($xml), 'factura-pruebas-'.$registro.'-firmada.xml', [
            'Content-Type' => 'application/xml', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function enviar(Request $request, int $registro): JsonResponse|RedirectResponse
    {
        $estado = $this->firmas->enviar($registro);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $estado])
            : to_route('ventas.documentos.sri.log', $registro)->with('status', 'Resultado registrado en el historial de pruebas del SRI.');
    }
}
