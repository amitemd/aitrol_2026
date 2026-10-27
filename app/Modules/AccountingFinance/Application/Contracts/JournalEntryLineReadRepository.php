<?php

namespace App\Modules\AccountingFinance\Application\Contracts;

use App\Modules\AccountingFinance\Application\Data\JournalEntryLine;
use Illuminate\Support\Collection;

interface JournalEntryLineReadRepository
{
    /** @return Collection<int, JournalEntryLine> */
    public function summarizedForCompany(int $journalEntryId, string $companyId): Collection;
}
