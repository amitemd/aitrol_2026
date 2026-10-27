<?php

namespace App\Modules\Billing\Application\Queries;

use App\Modules\Billing\Application\Contracts\SalesInvoiceDirectoryRepository;
use App\Modules\Billing\Application\Data\SalesInvoiceDirectoryFilters;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListSalesInvoices
{
    public function __construct(private SalesInvoiceDirectoryRepository $invoices) {}

    /** @return LengthAwarePaginator<int, object> */
    public function handle(SalesInvoiceDirectoryFilters $filters): LengthAwarePaginator
    {
        return $this->invoices->paginate($filters);
    }
}
