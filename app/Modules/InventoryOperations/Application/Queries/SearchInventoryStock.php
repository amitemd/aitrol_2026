<?php

namespace App\Modules\InventoryOperations\Application\Queries;

use App\Modules\InventoryOperations\Application\Contracts\InventoryStockReadRepository;
use App\Modules\InventoryOperations\Application\Data\InventoryStockFilters;
use App\Modules\InventoryOperations\Application\Data\InventoryStockItem;
use Illuminate\Support\Collection;

final readonly class SearchInventoryStock
{
    public function __construct(private InventoryStockReadRepository $stock) {}

    /** @return Collection<int, InventoryStockItem> */
    public function handle(string $companyId, InventoryStockFilters $filters): Collection
    {
        return $this->stock->search($companyId, $filters);
    }
}
