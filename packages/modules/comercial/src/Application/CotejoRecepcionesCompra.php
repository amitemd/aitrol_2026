<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class CotejoRecepcionesCompra
{
    public function __construct(private EmpresaActiva $empresa) {}

    public function facturasVigentes(): Builder
    {
        return DB::table('com_facturas_recepciones as vinculo')->where('vinculo.id_empresa', $this->empresa->id())
            ->join('com_documentos as factura', fn ($join) => $join->on('factura.id', '=', 'vinculo.id_factura')
                ->on('factura.id_empresa', '=', 'vinculo.id_empresa'))->whereNull('factura.anulado_at');
    }

    public function saldos(Collection $lineas): Collection
    {
        if ($lineas->isEmpty()) {
            return $lineas;
        }
        $facturadasCompletas = $this->facturasCompletasVigentes()->whereIn('vinculo.id_recepcion', $lineas->pluck('id_documento')->unique()->values())
            ->pluck('vinculo.id_recepcion')->flip();
        $aplicado = $this->importesAplicados()->whereIn('aplicacion.id_linea_recepcion', $lineas->pluck('id'))
            ->get()->keyBy('id_linea_recepcion');

        return $lineas->map(function (stdClass $linea) use ($facturadasCompletas, $aplicado): stdClass {
            $descontar = $facturadasCompletas->has($linea->id_documento) ? $linea : $aplicado->get($linea->id);
            $linea->cantidad_facturable = (int) $linea->cantidad - (int) ($descontar->cantidad ?? 0);
            $linea->base_facturable = (int) $linea->subtotal - (int) ($descontar->subtotal ?? 0);
            $linea->impuesto_facturable = (int) $linea->impuesto - (int) ($descontar->impuesto ?? 0);

            return $linea;
        });
    }

    public function facturasCompletasVigentes(): Builder
    {
        return $this->facturasVigentes()
            ->whereNotExists(fn (Builder $q) => $q->from('com_aplicaciones_recepcion as partida')->selectRaw('1')
                ->whereColumn('partida.id_empresa', 'vinculo.id_empresa')->whereColumn('partida.id_factura', 'vinculo.id_factura'));
    }

    public function importesAplicados(): Builder
    {
        return DB::table('com_aplicaciones_recepcion as aplicacion')
            ->join('com_documentos as factura', fn ($join) => $join->on('factura.id', '=', 'aplicacion.id_factura')
                ->on('factura.id_empresa', '=', 'aplicacion.id_empresa'))
            ->where('aplicacion.id_empresa', $this->empresa->id())->whereNull('factura.anulado_at')
            ->groupBy('aplicacion.id_linea_recepcion')->select('aplicacion.id_linea_recepcion')
            ->selectRaw('SUM(aplicacion.cantidad) AS cantidad, SUM(aplicacion.subtotal) AS subtotal, SUM(aplicacion.impuesto) AS impuesto');
    }

    public function cantidadesFacturadas(int $orden): Collection
    {
        return DB::table('com_lineas as partida')
            ->join('com_documentos as factura', fn ($join) => $join->on('factura.id', '=', 'partida.id_documento')
                ->on('factura.id_empresa', '=', 'partida.id_empresa'))
            ->where(['factura.id_empresa' => $this->empresa->id(), 'factura.id_documento_origen' => $orden, 'factura.modulo' => 'compras', 'factura.tipo' => 'documentos'])
            ->whereNotNull('partida.id_linea_orden')->whereNull('factura.anulado_at')
            ->groupBy('partida.id_linea_orden')->select('partida.id_linea_orden')->selectRaw('SUM(partida.cantidad) AS cantidad')
            ->pluck('cantidad', 'partida.id_linea_orden');
    }

    public function detalleFactura(stdClass $factura): ?array
    {
        $cotejo = json_decode($factura->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR)['cotejo'] ?? null;
        if ($cotejo === null) {
            return null;
        }
        $cotejo['aplicaciones'] = DB::table('com_aplicaciones_recepcion as aplicacion')
            ->join('com_lineas as linea', fn ($join) => $join->on('linea.id', '=', 'aplicacion.id_linea_recepcion')
                ->on('linea.id_empresa', '=', 'aplicacion.id_empresa'))
            ->where(['aplicacion.id_empresa' => $this->empresa->id(), 'aplicacion.id_factura' => $factura->id])
            ->orderBy('linea.id_documento')->orderBy('linea.id')
            ->get(['linea.id_documento as id_recepcion', 'linea.id_linea_orden', 'linea.descripcion', 'aplicacion.cantidad', 'aplicacion.subtotal', 'aplicacion.impuesto'])
            ->map(fn (stdClass $fila): array => [
                'id_recepcion' => $fila->id_recepcion, 'id_linea_orden' => $fila->id_linea_orden, 'descripcion' => $fila->descripcion,
                'cantidad' => Cantidad::decimal($fila->cantidad, 3), 'subtotal' => Cantidad::decimal($fila->subtotal, 2),
                'impuesto' => Cantidad::decimal($fila->impuesto, 2),
            ])->all();

        return $cotejo;
    }

    /**
     * @return array{lineas: list<array>, aplicaciones: list<array>, cotejo: array}
     */
    public function preparar(int $idOrden, Collection $lineasOrden, Collection $saldosRecepciones, array $datos): array
    {
        $pendientes = $saldosRecepciones->where('cantidad_facturable', '>', 0)->groupBy('id_linea_orden');
        $cantidadesFacturadas = $this->cantidadesFacturadas($idOrden);
        $lineas = [];
        $aplicaciones = [];
        $subtotalServicios = 0;
        $diferenciaMercaderia = 0;
        $diferencias = [];

        foreach ($datos['lineas'] as $indice => $fila) {
            $campo = 'lineas.'.$indice;
            $orden = $lineasOrden->get((int) $fila['id_linea_orden']);
            if ($orden === null) {
                throw ValidationException::withMessages([$campo.'.id_linea_orden' => 'Selecciona una línea de esta orden.']);
            }
            $cantidad = Cantidad::unidades($fila['cantidad'], 3);
            $precio = Cantidad::unidades($fila['precio'], 4);
            $tasa = isset($fila['tasa']) ? Cantidad::unidades($fila['tasa'], 2) : (int) $orden->tasa;
            $subtotal = intdiv($cantidad * $precio + 50000, 100000) - Cantidad::unidades($fila['descuento'] ?? 0, 2);
            $impuesto = intdiv($subtotal * $tasa + 5000, 10000);
            if ($subtotal < 0) {
                throw ValidationException::withMessages([$campo.'.descuento' => 'El descuento no puede superar el importe de la línea.']);
            }

            $esMercaderia = $orden->tipo_producto === 'producto' || $orden->recibida > 0;
            if ($esMercaderia) {
                $asignadas = $this->distribuir($pendientes->get($orden->id, collect()), $cantidad, $campo);
                array_push($aplicaciones, ...$asignadas);
                $baseEsperada = array_sum(array_column($asignadas, 'subtotal'));
                $ivaEsperado = array_sum(array_column($asignadas, 'impuesto'));
                $diferenciaMercaderia += $subtotal - $baseEsperada;
            } else {
                $previa = (int) $cantidadesFacturadas->get($orden->id, 0);
                if ($previa + $cantidad > (int) $orden->cantidad) {
                    throw ValidationException::withMessages([$campo.'.cantidad' => 'La cantidad del servicio supera el saldo de la orden.']);
                }
                $baseEsperada = Cantidad::proporcionar((int) $orden->subtotal, $previa + $cantidad, (int) $orden->cantidad)
                    - Cantidad::proporcionar((int) $orden->subtotal, $previa, (int) $orden->cantidad);
                $ivaEsperado = Cantidad::proporcionar((int) $orden->impuesto, $previa + $cantidad, (int) $orden->cantidad)
                    - Cantidad::proporcionar((int) $orden->impuesto, $previa, (int) $orden->cantidad);
                $subtotalServicios += $subtotal;
            }

            if ($precio !== (int) $orden->precio || $tasa !== (int) $orden->tasa || $subtotal !== $baseEsperada || $impuesto !== $ivaEsperado) {
                $diferencias[] = ['id_linea_orden' => $orden->id, 'precio_esperado' => Cantidad::decimal($orden->precio, 4),
                    'precio_facturado' => Cantidad::decimal($precio, 4), 'tasa_esperada' => Cantidad::decimal($orden->tasa, 2),
                    'tasa_facturada' => Cantidad::decimal($tasa, 2), 'diferencia_base' => $subtotal - $baseEsperada,
                    'diferencia_impuesto' => $impuesto - $ivaEsperado];
            }
            $lineas[] = ['id_linea_orden' => $orden->id, 'id_producto' => $orden->id_producto, 'descripcion' => $orden->descripcion,
                'cantidad' => $cantidad, 'precio' => $precio, 'tasa' => $tasa, 'subtotal' => $subtotal,
                'impuesto' => $impuesto, 'total' => $subtotal + $impuesto];
        }

        if ($aplicaciones === [] || collect($aplicaciones)->pluck('id_recepcion')->unique()->count() !== count($datos['recepciones'])) {
            throw ValidationException::withMessages(['recepciones' => 'Selecciona solo las recepciones que aportan mercadería a esta factura.']);
        }
        foreach (['subtotal', 'impuesto'] as $campo) {
            if (Cantidad::unidades($datos[$campo], 2) !== array_sum(array_column($lineas, $campo))) {
                throw ValidationException::withMessages([$campo => 'El importe del comprobante debe coincidir con la suma de sus líneas.']);
            }
        }

        $cotejo = ['subtotal_servicios' => $subtotalServicios, 'diferencia_mercaderia' => $diferenciaMercaderia];
        if ($diferencias !== []) {
            Gate::authorize('permiso', ['contabilidad.asientos.index', 'escribir']);
            if (blank($datos['motivo_diferencia'] ?? null)) {
                throw ValidationException::withMessages(['motivo_diferencia' => 'Indica el motivo para aprobar las diferencias de precio, base o impuesto.']);
            }
            $cotejo['aprobacion'] = ['id_usuario' => auth()->id(), 'usuario' => auth()->user()?->name, 'fecha' => now()->toISOString(),
                'motivo' => $datos['motivo_diferencia'], 'lineas' => $diferencias];
        }

        return compact('lineas', 'aplicaciones', 'cotejo');
    }

    private function distribuir(Collection $pendientes, int $cantidad, string $campo): array
    {
        if ($cantidad > $pendientes->sum('cantidad_facturable')) {
            throw ValidationException::withMessages([$campo.'.cantidad' => 'La cantidad facturada supera la disponible en las recepciones seleccionadas.']);
        }
        $aplicaciones = [];
        foreach ($pendientes as $linea) {
            $aplicar = min($cantidad, $linea->cantidad_facturable);
            $aplicaciones[] = [
                'id_recepcion' => $linea->id_documento, 'id_linea_recepcion' => $linea->id, 'cantidad' => $aplicar,
                'subtotal' => Cantidad::proporcionar($linea->base_facturable, $aplicar, $linea->cantidad_facturable),
                'impuesto' => Cantidad::proporcionar($linea->impuesto_facturable, $aplicar, $linea->cantidad_facturable),
            ];
            $cantidad -= $aplicar;
            if ($cantidad === 0) {
                break;
            }
        }

        return $aplicaciones;
    }
}
