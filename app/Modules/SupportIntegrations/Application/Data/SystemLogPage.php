<?php

namespace App\Modules\SupportIntegrations\Application\Data;

use Illuminate\Pagination\LengthAwarePaginator;

final readonly class SystemLogPage
{
    /**
     * @param  LengthAwarePaginator<int, array<string, mixed>>  $entries
     * @param  list<array{name: string, size: int, modified_at: string}>  $files
     * @param  array<string, int>  $levelCounts
     */
    public function __construct(
        public LengthAwarePaginator $entries,
        public array $files,
        public array $levelCounts,
        public ?string $selectedFile,
        public int $fileSize,
        public int $scannedBytes,
        public bool $truncated,
    ) {}
}
