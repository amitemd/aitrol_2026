<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Configuracion\Application\MenuService;
use Aitrol\Configuracion\Domain\OpcionMenu;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class MenuController
{
    public function __construct(private readonly MenuService $menuService) {}

    public function index(Request $request): View
    {
        $opciones = $this->menuService->listar();
        $opcion = $request->query('editar') === null
            ? null
            : ($opciones[$request->integer('editar')] ?? abort(404));

        abort_if($opcion?->protegido, 403);

        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $busqueda = $datos['q'] ?? '';

        return view('configuracion::menu', [
            'opciones' => $opciones,
            'resultados' => $busqueda === '' ? $opciones : array_filter(
                $opciones,
                fn (OpcionMenu $item): bool => Str::contains($item->nombre, $busqueda, ignoreCase: true),
            ),
            'opcion' => $opcion,
            'destinos' => $this->destinos(),
            'ubicaciones' => $this->menuService->ubicaciones($opciones, $opcion),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->guardar($request);
    }

    public function update(Request $request, int $menu): RedirectResponse|JsonResponse
    {
        return $this->guardar($request, $this->menuService->listar()[$menu] ?? abort(404));
    }

    public function destroy(Request $request, int $menu): RedirectResponse|JsonResponse
    {
        $opcion = $this->menuService->listar()[$menu] ?? abort(404);

        try {
            $this->menuService->eliminar($opcion);
        } catch (DomainException $exception) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages(['menu' => $exception->getMessage()]);
            }

            return to_route('configuracion.menu.index')->withErrors(['menu' => $exception->getMessage()]);
        }

        return $this->resultado($request, 'Opción eliminada.');
    }

    private function guardar(Request $request, ?OpcionMenu $opcion = null): RedirectResponse|JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'ruta' => ['nullable', 'string', Rule::in(array_keys($this->destinos()))],
            'padre_id' => ['nullable', 'integer', 'min:1'],
            'icono' => ['required', 'string', 'max:50', 'regex:/^bi-[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'orden' => ['required', 'integer', 'min:0', 'max:65535'],
            'activo' => ['required', 'boolean'],
            'solo_administrador' => ['required', 'boolean'],
        ], [
            'ruta.in' => 'Selecciona un destino disponible del ERP.',
            'icono.regex' => 'Escribe una clase de Bootstrap Icons, por ejemplo bi-people.',
        ]);

        $datos['ruta'] ??= null;
        $datos['padre_id'] ??= null;

        try {
            $this->menuService->guardar($opcion, $datos);
        } catch (DomainException $exception) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages(['menu' => $exception->getMessage()]);
            }

            return back()->withInput()->withErrors(['menu' => $exception->getMessage()]);
        }

        return $this->resultado($request, 'Menú actualizado.');
    }

    private function resultado(Request $request, string $mensaje): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje, 'menuChanged' => true])
            : to_route('configuracion.menu.index')->with('estado', $mensaje);
    }

    private function destinos(): array
    {
        $destinos = config('menu.destinos') + config('clientes.secciones');

        return array_filter($destinos, fn (string $ruta): bool => Route::has($ruta), ARRAY_FILTER_USE_KEY);
    }
}
