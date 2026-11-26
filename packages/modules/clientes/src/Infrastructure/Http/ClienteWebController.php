<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\UseCases\ClientesService;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Clientes\Infrastructure\Persistence\ArchivosCliente;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ClienteWebController
{
    public function __construct(private ClientesService $clientes, private ArchivosCliente $archivos) {}

    public function index(ListarClientesRequest $request): View
    {
        return view('clientes::index', [
            'clientes' => $request->paginar($this->clientes),
        ]);
    }

    public function create(): View
    {
        return view('clientes::form', ['cliente' => null, 'tipos' => TipoIdentificacion::cases()]);
    }

    public function edit(int $cliente): View
    {
        return view('clientes::form', [
            'cliente' => $this->clientes->consultar($cliente),
            'tipos' => TipoIdentificacion::cases(),
        ] + $this->archivos->ficha($cliente));
    }

    public function store(GuardarClienteRequest $request): JsonResponse|RedirectResponse
    {
        $datos = $request->datos();
        $this->archivos->guardar(fn () => $this->clientes->registrar($datos), $request->file('foto'), $request->file('adjuntos', []));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cliente registrado.'], 201);
        }

        return to_route('clientes.index')->with('status', 'Cliente registrado.');
    }

    public function update(GuardarClienteRequest $request, int $cliente): RedirectResponse|JsonResponse
    {
        $datos = $request->datos();
        $this->archivos->guardar(fn () => $this->clientes->actualizar($cliente, $datos), $request->file('foto'), $request->file('adjuntos', []));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cliente actualizado.']);
        }

        return to_route('clientes.index')->with('status', 'Cliente actualizado.');
    }

    public function foto(int $cliente): StreamedResponse
    {
        return $this->archivos->foto($cliente);
    }

    public function documento(int $cliente, string $archivo): StreamedResponse
    {
        return $this->archivos->documento($cliente, $archivo);
    }
}
