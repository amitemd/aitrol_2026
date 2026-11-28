<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\UseCases\ClientesService;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Clientes\Infrastructure\Persistence\ArchivosCliente;
use Aitrol\Clientes\Infrastructure\Persistence\CarteraClientes;
use Aitrol\Clientes\Infrastructure\Persistence\ClienteModel;
use App\Services\CobroFacturaVenta;
use App\Services\ContextoOperativo;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

    public function show(int $cliente): View
    {
        $ficha = $this->clientes->consultar($cliente);

        return view('partials.detalle-ficha-simple', [
            'titulo' => 'Detalle de cliente',
            'volver' => route('clientes.index'),
            'accionUrl' => Gate::allows('permiso', ['clientes.reportes.cuentas-por-cobrar', 'leer']) ? route('clientes.estado-cuenta', $cliente) : null,
            'accionTexto' => 'Estado de cuenta',
            'accionWorkspace' => 'clientes.estado-cuenta.'.$cliente,
            'fotoUrl' => $this->archivos->ficha($cliente)['tieneFoto'] ? route('clientes.foto', $cliente) : null,
            'campos' => [
                'Nombre' => $ficha->nombre, $ficha->identificacion->tipo->etiqueta() => $ficha->identificacion->numero,
                'Correo electrónico' => $ficha->email, 'Teléfono' => $ficha->telefono, 'Dirección' => $ficha->direccion,
                'Estado' => $ficha->activo ? 'Activo' : 'Inactivo',
            ],
        ]);
    }

    public function cartera(Request $request, CarteraClientes $cartera, ContextoOperativo $contexto): View
    {
        return $this->reporteCartera($request, $cartera, $contexto);
    }

    public function comprobantesPago(Request $request, CobroFacturaVenta $cobros, ContextoOperativo $contexto): View
    {
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $filas = $cobros->comprobantes($filtros['q'] ?? null);
        $sucursal = $contexto->sucursal()->nombre;

        return view('clientes::comprobantes-pago', compact('filas', 'sucursal'));
    }

    public function estadoCuenta(Request $request, int $cliente, CarteraClientes $cartera, ContextoOperativo $contexto): View
    {
        $ficha = ClienteModel::withTrashed()->with('persona')->findOrFail($cliente);

        return $this->reporteCartera($request, $cartera, $contexto, $ficha);
    }

    private function reporteCartera(Request $request, CarteraClientes $cartera, ContextoOperativo $contexto, ?ClienteModel $cliente = null): View
    {
        $hoy = CarbonImmutable::today('America/Guayaquil');
        $finPeriodo = $hoy->setYear($contexto->periodo() ?? $hoy->year)->endOfYear()->startOfDay();
        $limite = $hoy->min($finPeriodo)->toDateString();
        $filtros = $request->validate(['corte' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:'.$limite], 'q' => ['nullable', 'string', 'max:120']]);
        $corte = CarbonImmutable::parse($filtros['corte'] ?? $limite, 'America/Guayaquil');
        $facturas = $cartera->facturas($corte, $cliente?->id, $filtros['q'] ?? null);
        $totales = $cartera->totales($facturas, $corte);
        $reporte = $cliente !== null ? 'estado-cuenta' : $request->route('reporte');
        $porCliente = in_array($reporte, ['saldos-de-cartera', 'edad-de-cartera'], true);

        $filas = match ($reporte) {
            'saldos-de-cartera', 'edad-de-cartera' => $cartera->porCliente($facturas, $corte, $reporte === 'edad-de-cartera'),
            'cuentas-por-cobrar' => $facturas->where('saldo', '>', 0)->orderBy('vencimiento')->orderBy('id'),
            'cartera-vencida' => $facturas->where('saldo', '>', 0)->where('vencimiento', '<', $corte->toDateString())->orderBy('vencimiento')->orderBy('id'),
            default => $facturas->orderByDesc('fecha')->orderByDesc('id'),
        };
        $filas = $filas->paginate(25)->appends($filtros);
        $movimientos = $cliente === null ? collect() : $cartera->movimientos($corte, $filas->getCollection()->pluck('id'));
        $titulo = $cliente === null ? config('clientes.secciones')[$request->route()->getName()] : 'Estado de cuenta';
        $sucursal = $contexto->sucursal()->nombre;
        $puedeCobrar = ! $porCliente && $corte->isSameDay($hoy)
            && Gate::allows('permiso', ['ventas.documentos.index', 'leer'])
            && Gate::allows('permiso', ['caja-bancos.movimientos.index', 'escribir']);

        return view('clientes::cartera', compact('titulo', 'sucursal', 'reporte', 'cliente', 'corte', 'limite', 'filtros', 'totales', 'filas', 'porCliente', 'movimientos', 'puedeCobrar'));
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

    public function destroy(Request $request, int $cliente): JsonResponse|RedirectResponse
    {
        $this->clientes->eliminar($cliente);

        return $request->expectsJson()
            ? response()->json(['message' => 'Cliente eliminado del listado.'])
            : to_route('clientes.index')->with('status', 'Cliente eliminado del listado.');
    }

    public function documento(int $cliente, string $archivo): StreamedResponse
    {
        return $this->archivos->documento($cliente, $archivo);
    }
}
