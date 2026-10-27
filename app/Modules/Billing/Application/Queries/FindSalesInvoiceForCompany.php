<?php

namespace App\Modules\Billing\Application\Queries;

use App\Modules\Billing\Application\Contracts\SalesInvoiceReadRepository;
use App\Modules\Billing\Application\Data\SalesInvoiceView;

final readonly class FindSalesInvoiceForCompany
{
    public function __construct(private SalesInvoiceReadRepository $invoices)
    {
    }

    public function handle(int $invoiceId, string $companyId): ?SalesInvoiceView
    {
        return $this->invoices->findForCompany($invoiceId, $companyId);
    }
}
