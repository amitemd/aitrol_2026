<?php

namespace App\Modules\Shared\Application\Queries;

use App\Modules\Shared\Application\Contracts\ArchitectureCatalog;
use App\Modules\Shared\Domain\Architecture\BoundedContextDefinition;

final readonly class DescribeLegacyArchitecture
{
    public function __construct(private ArchitectureCatalog $catalog) {}

    /** @return list<BoundedContextDefinition> */
    public function handle(): array
    {
        return $this->catalog->contexts();
    }
}
