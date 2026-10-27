<?php

namespace App\Modules\Billing\Infrastructure\Persistence;

use App\Modules\Billing\Application\Contracts\SalesInvoiceReadRepository;
use App\Modules\Billing\Application\Data\SalesInvoiceView;
use Illuminate\Support\Facades\DB;

final class LaravelSalesInvoiceReadRepository implements SalesInvoiceReadRepository
{
    public function findForCompany(int $invoiceId, string $companyId): ?SalesInvoiceView
    {
        $record = DB::table('ct_ventas')
            ->where('id', $invoiceId)
            ->where('id_empresa', $companyId)
            ->first();

        return $record === null ? null : SalesInvoiceView::fromLegacyRecord($record);
    }
}
