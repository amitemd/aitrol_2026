<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class ComprasPendientes
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto, private CotejoRecepcionesCompra $cotejo) {}

    public function reporte(?string $busqueda, string $estado): array
    {
        $recepciones = $this->partidas('recepciones')
            ->leftJoinSub($this->cotejo->facturasCompletasVigentes()->select('vinculo.id_recepcion')->distinct(), 'completa', 'completa.id_recepcion', '=', 'documento.id')
            ->leftJoinSub($this->cotejo->importesAplicados(), 'aplicada', 'aplicada.id_linea_recepcion', '=', 'partida.id')
            ->whereNotNull('partida.id_linea_orden')->groupBy('partida.id_linea_orden')
            ->select('partida.id_linea_orden')->selectRaw('SUM(partida.cantidad) AS recibida')
            ->selectRaw('SUM(CASE WHEN completa.id_recepcion IS NULL THEN partida.cantidad - COALESCE(aplicada.cantidad, 0) ELSE 0 END) AS por_facturar')
            ->selectRaw('SUM(CASE WHEN completa.id_recepcion IS NULL THEN partida.subtotal - COALESCE(aplicada.subtotal, 0) ELSE 0 END) AS base_por_facturar');

        $ordenes = $this->partidas('ordenes-compra')
            ->where(fn (Builder $q) => $q->whereNull('documento.estado_aprobacion')->orWhere('documento.estado_aprobacion', 'aprobada'))
            ->leftJoin('inv_productos as producto', fn ($join) => $join->on('producto.id', '=', 'partida.id_producto')->on('producto.id_empresa', '=', 'partida.id_empresa'))
            ->leftJoinSub($recepciones, 'recepcion', 'recepcion.id_linea_orden', '=', 'partida.id')
            ->where(fn (Builder $q) => $q->where('producto.tipo_producto', 'producto')->orWhereNotNull('recepcion.id_linea_orden'))
            ->when(filled($busqueda), fn (Builder $q) => $q->where(fn (Builder $filtro) => $filtro->whereLike('documento.numero', '%'.$busqueda.'%')
                ->orWhereLike('documento.tercero', '%'.$busqueda.'%')->orWhereLike('partida.descripcion', '%'.$busqueda.'%')))
            ->select('documento.id as id_orden', 'documento.numero', 'documento.fecha', 'documento.tercero as proveedor', 'partida.id as id_linea_orden', 'partida.descripcion')
            ->selectRaw('partida.cantidad AS ordenada, COALESCE(recepcion.recibida, 0) AS recibida, partida.cantidad - COALESCE(recepcion.recibida, 0) AS por_recibir')
            ->selectRaw('COALESCE(recepcion.por_facturar, 0) AS por_facturar, COALESCE(recepcion.base_por_facturar, 0) AS base_por_facturar');

        $consulta = DB::query()->fromSub($ordenes, 'pendiente');
        match ($estado) {
            'por-recibir' => $consulta->where('por_recibir', '>', 0),
            'por-facturar' => $consulta->where('por_facturar', '>', 0),
            default => $consulta->where(fn (Builder $q) => $q->where('por_recibir', '>', 0)->orWhere('por_facturar', '>', 0)),
        };
        $totales = [
            'ordenes' => (clone $consulta)->distinct()->count('id_orden'),
            'base_por_facturar' => Cantidad::decimal((int) (clone $consulta)->sum('base_por_facturar'), 2),
        ];
        $filas = $consulta->orderBy('fecha')->orderBy('id_orden')->orderBy('id_linea_orden')->paginate(25)->withQueryString()
            ->through(function (stdClass $fila): stdClass {
                foreach (['ordenada' => 3, 'recibida' => 3, 'por_recibir' => 3, 'por_facturar' => 3, 'base_por_facturar' => 2] as $campo => $decimales) {
                    $fila->$campo = Cantidad::decimal((int) $fila->$campo, $decimales);
                }

                return $fila;
            });

        return compact('totales', 'filas');
    }

    private function partidas(string $tipo): Builder
    {
        return DB::table('com_documentos as documento')->where([
            'documento.id_empresa' => $this->empresa->id(), 'documento.id_sucursal' => $this->contexto->sucursal()->id,
            'documento.modulo' => 'compras', 'documento.tipo' => $tipo,
        ])->whereNull('documento.anulado_at')
            ->join('com_lineas as partida', fn ($join) => $join->on('partida.id_documento', '=', 'documento.id')
                ->on('partida.id_empresa', '=', 'documento.id_empresa'));
    }
}
