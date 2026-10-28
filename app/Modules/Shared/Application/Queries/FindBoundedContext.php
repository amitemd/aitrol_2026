<?php

namespace App\Modules\Shared\Application\Queries;

use App\Modules\Shared\Application\Contracts\ArchitectureCatalog;
use App\Modules\Shared\Domain\Architecture\BoundedContextDefinition;

final readonly class FindBoundedContext
{
    public function __construct(private ArchitectureCatalog $catalog) {}

    public function handle(string $contextKey): ?BoundedContextDefinition
    {
        foreach ($this->catalog->contexts() as $context) {
            if ($context->key === $contextKey) {
                return $context;
            }
        }

        return null;
    }
}
