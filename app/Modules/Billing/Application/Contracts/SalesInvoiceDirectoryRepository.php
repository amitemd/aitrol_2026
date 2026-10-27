<?php

namespace App\Modules\Billing\Application\Contracts;

use App\Modules\Billing\Application\Data\SalesInvoiceDirectoryFilters;
use Illuminate\Pagination\LengthAwarePaginator;

interface SalesInvoiceDirectoryRepository
{
    /** @return LengthAwarePaginator<int, object> */
    public function paginate(SalesInvoiceDirectoryFilters $filters): LengthAwarePaginator;
}
