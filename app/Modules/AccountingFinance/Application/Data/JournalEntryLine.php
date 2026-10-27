<?php

namespace App\Modules\AccountingFinance\Application\Data;

final readonly class JournalEntryLine
{
    public function __construct(
        public int $sourceLineId,
        public int $journalEntryId,
        public string $accountPlanId,
        public string $accountName,
        public string $description,
        public float $debit,
        public float $credit,
    ) {}
}
