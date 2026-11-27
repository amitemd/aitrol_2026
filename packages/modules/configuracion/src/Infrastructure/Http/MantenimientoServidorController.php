<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Jobs\EjecutarMantenimientoServidor;
use App\Services\MantenimientoServidor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

final readonly class MantenimientoServidorController
{
    public function __construct(private MantenimientoServidor $mantenimiento) {}

    public function index(): View
    {
        return view('configuracion::mantenimiento', [
            'habilitado' => (bool) config('mantenimiento.habilitado'),
            'disponibles' => array_filter(array_keys(MantenimientoServidor::ACCIONES), $this->mantenimiento->disponible(...)),
            'acciones' => MantenimientoServidor::ACCIONES,
            ...$this->mantenimiento->estado(),
        ]);
    }

    public function estado(): JsonResponse
    {
        return response()->json($this->mantenimiento->estado());
    }

    public function ejecutar(Request $request, string $accion): JsonResponse|RedirectResponse
    {
        $id = $this->mantenimiento->registrar($accion, $request->user());
        Bus::dispatchAfterResponse(new EjecutarMantenimientoServidor($id));

        return $request->expectsJson()
            ? response()->json(['id' => $id, ...$this->mantenimiento->estado()], 202)
            : to_route('configuracion.mantenimiento.index');
    }
}
