<?php

namespace Aitrol\Tributario\Infrastructure\Persistence;

use Aitrol\Tributario\Application\Puertos\RepositorioTributario;
use Aitrol\Tributario\Domain\PeriodoFiscal;
use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class TributarioSql implements RepositorioTributario
{
    public function __construct(private EmpresaActiva $empresa) {}

    public function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    public function transaccion(Closure $trabajo): mixed
    {
        return $this->empresa->transaccion($trabajo);
    }

    public function buscarPeriodo(PeriodoFiscal $periodo): ?stdClass
    {
        return $this->tabla('tr_declaraciones')->where('formato', $periodo->formato)->where('desde', $periodo->desde)->where('hasta', $periodo->hasta)->first();
    }

    public function declaracion(int $id): stdClass
    {
        return $this->tabla('tr_declaraciones')->where('id', $id)->first() ?? abort(404, 'Papel de trabajo no encontrado.');
    }

    public function guardarDeclaracion(array $datos, ?int $id = null): int
    {
        return $this->guardar('tr_declaraciones', $datos, $id);
    }

    public function tieneConstancia(int $id): bool
    {
        return $this->tabla('tr_documentos')->where('id_declaracion', $id)->where('tipo', 'acuse')->exists();
    }

    public function existeEntregaSolapada(stdClass $declaracion): bool
    {
        return $this->tabla('tr_declaraciones')->where('formato', $declaracion->formato)->where('id', '<>', $declaracion->id)
            ->whereIn('estado', ['presentado', 'entregado'])->where('desde', '<=', $declaracion->hasta)
            ->where('hasta', '>=', $declaracion->desde)->exists();
    }

    public function evento(int $id, string $accion, ?string $detalle = null): void
    {
        $this->guardar('tr_eventos', ['id_declaracion' => $id, 'accion' => $accion, 'detalle' => $detalle]);
    }

    public function guardarNomina(array $datos): stdClass
    {
        $id = $this->tabla('tr_nomina')->where('ejercicio', $datos['ejercicio'])->where('identificacion', $datos['identificacion'])->value('id');
        $id = $this->guardar('tr_nomina', $datos, $id);

        return $this->tabla('tr_nomina')->where('id', $id)->first();
    }

    public function guardarDocumento(array $datos): stdClass
    {
        return $this->transaccion(function () use ($datos): stdClass {
            $declaracion = $this->declaracion($datos['id_declaracion']);
            $existente = $this->tabla('tr_documentos')->where('id_declaracion', $declaracion->id)->where('tipo', $datos['tipo'])->where('sha256', $datos['sha256'])->exists();
            abort_if($existente, 409, 'Este archivo ya está adjunto al papel de trabajo.');
            $id = $this->guardar('tr_documentos', $datos);
            $this->evento($declaracion->id, 'respaldo_adjunto', $datos['nombre']);

            return $this->tabla('tr_documentos')->where('id', $id)->first();
        });
    }

    public function preparar(PeriodoFiscal $periodo, array $entradas): array
    {
        return match ($periodo->formato) {
            'formulario-103' => $this->retenciones($periodo, $entradas),
            'formulario-104' => $this->iva($periodo),
            'formulario-107' => $this->nomina($periodo),
            'ats' => $this->ats($periodo),
        };
    }

    private function retenciones(PeriodoFiscal $periodo, array $entradas): array
    {
        $filas = $this->lineas($periodo)->whereNull('d.anulado_at')->where('d.modulo', 'compras')->where('d.tipo', 'retenciones')
            ->where('com_lineas.tipo_retencion', 'renta')->select('com_lineas.codigo_retencion as codigo', 'com_lineas.porcentaje_retencion as tasa')
            ->selectRaw('sum(com_lineas.base_retencion) as base, sum(com_lineas.total) as retenido')
            ->groupBy('com_lineas.codigo_retencion', 'com_lineas.porcentaje_retencion')->orderBy('codigo')->get();
        $nomina = $entradas['retencion_nomina'] ?? 0;
        $base = $entradas['base_nomina'] ?? 0;
        if ($base || $nomina) $filas->push((object) ['codigo' => 'Nómina informada', 'tasa' => null, 'base' => $base, 'retenido' => $nomina]);

        return [
            'resumen' => ['base_registrada' => $filas->sum('base'), 'renta_retenida' => $filas->sum('retenido')],
            'detalle' => $filas->all(), 'observaciones' => ['Revisa los códigos de retención y completa los importes de nómina u operaciones que estén fuera de Compras.'],
        ];
    }

    private function iva(PeriodoFiscal $periodo): array
    {
        $filas = $this->lineas($periodo)->whereNull('d.anulado_at')->whereIn('d.tipo', ['documentos', 'notas-credito', 'notas-debito'])
            ->select('d.modulo', 'com_lineas.tasa')
            ->selectRaw("sum(case when d.tipo = 'notas-credito' then -com_lineas.subtotal else com_lineas.subtotal end) as base")
            ->selectRaw("sum(case when d.tipo = 'notas-credito' then -com_lineas.impuesto else com_lineas.impuesto end) as impuesto")
            ->groupBy('d.modulo', 'com_lineas.tasa')->orderBy('d.modulo')->orderBy('com_lineas.tasa')->get();
        $retenciones = $this->lineas($periodo)->whereNull('d.anulado_at')->where('d.tipo', 'retenciones')
            ->where('com_lineas.tipo_retencion', 'iva')->select('d.modulo')->selectRaw('sum(com_lineas.total) as retenido')
            ->groupBy('d.modulo')->pluck('retenido', 'modulo');

        return [
            'resumen' => [
                'iva_ventas' => $filas->where('modulo', 'ventas')->sum('impuesto'), 'iva_compras' => $filas->where('modulo', 'compras')->sum('impuesto'),
                'iva_retenido_compras' => (int) ($retenciones['compras'] ?? 0), 'iva_retenido_ventas' => (int) ($retenciones['ventas'] ?? 0),
            ],
            'detalle' => $filas->all(),
            'observaciones' => ['Verifica el crédito tributario, las compensaciones y las operaciones que se registraron fuera de Ventas y Compras.'],
        ];
    }

    private function nomina(PeriodoFiscal $periodo): array
    {
        $filas = $this->tabla('tr_nomina')->where('ejercicio', (int) substr($periodo->desde, 0, 4))->orderBy('nombre')->orderBy('id')
            ->get(['identificacion', 'nombre', 'ingresos_gravados', 'ingresos_exentos', 'aporte_personal', 'retencion']);

        return [
            'resumen' => ['trabajadores' => $filas->count(), 'renta_retenida' => $filas->sum('retencion')],
            'detalle' => $filas->all(), 'observaciones' => ['Contrasta las cifras con la nómina anual y completa los casilleros aplicables antes de entregar el comprobante.'],
        ];
    }

    private function ats(PeriodoFiscal $periodo): array
    {
        $filas = $this->tabla('com_documentos')->whereBetween('com_documentos.fecha', [$periodo->desde, $periodo->hasta])
            ->whereIn('com_documentos.tipo', ['documentos', 'notas-credito', 'notas-debito', 'retenciones'])
            ->leftJoin('erp_clientes as c', 'c.id', '=', 'com_documentos.id_cliente')
            ->leftJoin('erp_proveedores as p', 'p.id', '=', 'com_documentos.id_proveedor')
            ->leftJoin('com_documentos as origen', 'origen.id', '=', 'com_documentos.id_documento_origen')
            ->select('com_documentos.fecha', 'com_documentos.numero', 'com_documentos.tipo', 'com_documentos.modulo', 'com_documentos.tercero',
                'com_documentos.subtotal as base', 'com_documentos.impuesto', 'com_documentos.total', 'com_documentos.anulado_at', 'origen.numero as documento_origen')
            ->selectRaw('coalesce(c.identificacion, p.identificacion) as identificacion')
            ->selectRaw('coalesce(c.tipo_identificacion, p.tipo_identificacion) as tipo_identificacion')
            ->orderBy('com_documentos.fecha')->orderBy('com_documentos.id')->get();
        $sinIdentificacion = $filas->whereNull('identificacion')->count();

        return [
            'resumen' => [
                'comprobantes' => $filas->count(), 'anulados' => $filas->whereNotNull('anulado_at')->count(),
                'sin_identificacion' => $sinIdentificacion,
            ],
            'detalle' => $filas->all(),
            'observaciones' => [
                'Antes de preparar el anexo oficial, completa autorizaciones, sustento tributario y formas de pago utilizando los comprobantes electrónicos.',
                ...($sinIdentificacion > 0 ? ['Hay comprobantes sin identificación del tercero.'] : []),
            ],
        ];
    }

    private function lineas(PeriodoFiscal $periodo): Builder
    {
        return $this->tabla('com_lineas')->join('com_documentos as d', 'd.id', '=', 'com_lineas.id_documento')
            ->whereBetween('d.fecha', [$periodo->desde, $periodo->hasta]);
    }

    private function guardar(string $tabla, array $datos, ?int $id = null): int
    {
        if ($id === null) {
            return (int) DB::table($tabla)->insertGetId($datos + [
                'id_empresa' => $this->empresa->id(), 'id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);

        return $id;
    }
}
