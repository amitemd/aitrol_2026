<?php

namespace App\Modules\AccountingFinance\Application\Queries;

use App\Modules\AccountingFinance\Application\Contracts\JournalEntryLineReadRepository;
use App\Modules\AccountingFinance\Application\Data\JournalEntryLine;
use Illuminate\Support\Collection;

final readonly class ListJournalEntryLinesForEditing
{
    public function __construct(private JournalEntryLineReadRepository $lines) {}

    /** @return Collection<int, JournalEntryLine> */
    public function handle(int $journalEntryId, string $companyId): Collection
    {
        return $this->lines->summarizedForCompany($journalEntryId, $companyId);
    }
}
