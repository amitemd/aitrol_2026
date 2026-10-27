<?php

namespace App\Modules\Billing\Application\Contracts;

use App\Modules\Billing\Application\Data\SalesInvoiceView;

interface SalesInvoiceReadRepository
{
    public function findForCompany(int $invoiceId, string $companyId): ?SalesInvoiceView;
}
