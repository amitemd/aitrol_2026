<?php

namespace Aitrol\Proveedores\Infrastructure\Http;

use Aitrol\Proveedores\Application\UseCases\ProveedoresService;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Proveedores\Infrastructure\Persistence\ArchivosProveedor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ProveedorWebController
{
    public function __construct(private ProveedoresService $proveedores, private ArchivosProveedor $archivos) {}

    public function index(ListarProveedoresRequest $request): View
    {
        return view('proveedores::index', [
            'proveedores' => $request->paginar($this->proveedores),
        ]);
    }

    public function create(): View
    {
        return view('proveedores::form', ['proveedor' => null, 'tipos' => TipoIdentificacion::cases()]);
    }

    public function edit(int $proveedor): View
    {
        return view('proveedores::form', [
            'proveedor' => $this->proveedores->consultar($proveedor),
            'tipos' => TipoIdentificacion::cases(),
        ] + $this->archivos->ficha($proveedor));
    }

    public function show(int $proveedor): View
    {
        $ficha = $this->proveedores->consultar($proveedor);

        return view('partials.detalle-ficha-simple', ['titulo' => 'Detalle de proveedor', 'volver' => route('proveedores.index'), 'campos' => [
            'Nombre' => $ficha->nombre, $ficha->identificacion->tipo->etiqueta() => $ficha->identificacion->numero,
            'Correo electrónico' => $ficha->email, 'Teléfono' => $ficha->telefono, 'Dirección' => $ficha->direccion,
            'Estado' => $ficha->activo ? 'Activo' : 'Inactivo',
        ]]);
    }

    public function store(GuardarProveedorRequest $request): JsonResponse|RedirectResponse
    {
        $datos = $request->datos();
        $this->archivos->guardar(fn () => $this->proveedores->registrar($datos), $request->file('foto'), $request->file('adjuntos', []));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Proveedor registrado.'], 201);
        }

        return to_route('proveedores.index')->with('status', 'Proveedor registrado.');
    }

    public function update(GuardarProveedorRequest $request, int $proveedor): RedirectResponse|JsonResponse
    {
        $datos = $request->datos();
        $this->archivos->guardar(fn () => $this->proveedores->actualizar($proveedor, $datos), $request->file('foto'), $request->file('adjuntos', []));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Proveedor actualizado.']);
        }

        return to_route('proveedores.index')->with('status', 'Proveedor actualizado.');
    }

    public function foto(int $proveedor): StreamedResponse
    {
        return $this->archivos->foto($proveedor);
    }

    public function destroy(Request $request, int $proveedor): JsonResponse|RedirectResponse
    {
        $this->proveedores->eliminar($proveedor);

        return $request->expectsJson()
            ? response()->json(['message' => 'Proveedor eliminado del listado.'])
            : to_route('proveedores.index')->with('status', 'Proveedor eliminado del listado.');
    }

    public function documento(int $proveedor, string $archivo): StreamedResponse
    {
        return $this->archivos->documento($proveedor, $archivo);
    }
}
