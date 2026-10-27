<?php

namespace App\Modules\Billing\Infrastructure\Persistence;

use App\Modules\Billing\Application\Contracts\SalesInvoiceDirectoryRepository;
use App\Modules\Billing\Application\Data\SalesInvoiceDirectoryFilters;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class LaravelSalesInvoiceDirectoryRepository implements SalesInvoiceDirectoryRepository
{
    public function paginate(SalesInvoiceDirectoryFilters $filters): LengthAwarePaginator
    {
        $query = $this->baseQuery($filters->companyId);
        $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('ct_ventas.id')
            ->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    private function baseQuery(string $companyId): Builder
    {
        return DB::table('ct_ventas')
            ->leftJoin('seguros as se', 'ct_ventas.seguro_paciente', '=', 'se.id')
            ->join('users as uc', 'ct_ventas.id_usuariocrea', '=', 'uc.id')
            ->join('users as um', 'ct_ventas.id_usuariomod', '=', 'um.id')
            ->where('ct_ventas.estado', '<', 2)
            ->where('ct_ventas.tipo', 'VEN-FA')
            ->where('ct_ventas.id_empresa', $companyId)
            ->select([
                'ct_ventas.rutapdf',
                'ct_ventas.electronica',
                'ct_ventas.id',
                'ct_ventas.orden_venta',
                'ct_ventas.ip_creacion',
                'ct_ventas.nro_comprobante',
                'ct_ventas.id_asiento',
                'ct_ventas.created_at',
                'ct_ventas.fecha',
                'ct_ventas.estado',
                'ct_ventas.nombre_cliente',
                'ct_ventas.nombres_paciente',
                'ct_ventas.seguro_paciente',
                'ct_ventas.procedimientos',
                'ct_ventas.fecha_procedimiento',
                'ct_ventas.estado_electronica',
                'ct_ventas.doc_electronico',
                'ct_ventas.nro_autorizacion',
                'ct_ventas.id_empresa',
                'uc.nombre1',
                'uc.nombre2',
                'uc.apellido1',
                'uc.apellido2',
                'um.nombre1 as nombremod1',
                'um.nombre2 as nombremod2',
                'um.apellido1 as apellidomod1',
                'um.apellido2 as apellidomod2',
                'se.nombre',
                'ct_ventas.emisor',
            ])
            ->selectRaw('ROUND((COALESCE(ct_ventas.subtotal_0, 0) + COALESCE(ct_ventas.subtotal_12, 0)) - COALESCE(ct_ventas.descuento, 0), 2) AS valor_sin_iva')
            ->selectRaw('EXISTS (SELECT 1 FROM venta_kardex WHERE venta_kardex.id_venta = ct_ventas.id) AS tiene_venta_kardex');
    }

    private function applyFilters(Builder $query, SalesInvoiceDirectoryFilters $filters): void
    {
        $exactFilters = [
            'ct_ventas.id' => $filters->invoiceId,
            'ct_ventas.id_asiento' => $filters->journalEntryId,
        ];
        $partialFilters = [
            'ct_ventas.nro_comprobante' => $filters->number,
            'ct_ventas.fecha' => $filters->date,
            'ct_ventas.nombre_cliente' => $filters->clientName,
            'ct_ventas.nombres_paciente' => $filters->patientNames,
        ];

        foreach ($exactFilters as $column => $value) {
            if ($value !== null) {
                $query->where($column, $value);
            }
        }

        foreach ($partialFilters as $column => $value) {
            if ($value !== null) {
                $query->where($column, 'like', $this->pattern($value));
            }
        }

        if ($filters->globalSearch === null) {
            return;
        }

        $searchTerm = $filters->globalSearch;
        $pattern = $this->pattern($searchTerm);
        $query->where(function (Builder $searchQuery) use ($pattern, $searchTerm): void {
            $searchQuery
                ->where('ct_ventas.nro_comprobante', 'like', $pattern)
                ->orWhere('ct_ventas.fecha', 'like', $pattern)
                ->orWhere('ct_ventas.nombre_cliente', 'like', $pattern)
                ->orWhere('ct_ventas.nombres_paciente', 'like', $pattern)
                ->orWhere('ct_ventas.procedimientos', 'like', $pattern)
                ->orWhere('se.nombre', 'like', $pattern)
                ->orWhereRaw("CONCAT_WS(' ', uc.nombre1, uc.apellido1) LIKE ?", [$pattern])
                ->orWhereRaw("CONCAT_WS(' ', um.nombre1, um.apellido1) LIKE ?", [$pattern]);

            if (ctype_digit($searchTerm)) {
                $searchQuery
                    ->orWhere('ct_ventas.id', (int) $searchTerm)
                    ->orWhere('ct_ventas.id_asiento', (int) $searchTerm);
            }
        });
    }

    private function pattern(string $value): string
    {
        return '%'.addcslashes($value, '\\%_').'%';
    }
}
