<?php

namespace App\Modules\Shared\Application\Contracts;

use App\Modules\Shared\Domain\Architecture\BoundedContextDefinition;

interface ArchitectureCatalog
{
    /** @return list<BoundedContextDefinition> */
    public function contexts(): array;
}
