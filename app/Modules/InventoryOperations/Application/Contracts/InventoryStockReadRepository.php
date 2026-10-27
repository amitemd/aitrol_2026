<?php

namespace App\Modules\InventoryOperations\Application\Contracts;

use App\Modules\InventoryOperations\Application\Data\InventoryStockFilters;
use App\Modules\InventoryOperations\Application\Data\InventoryStockItem;
use Illuminate\Support\Collection;

interface InventoryStockReadRepository
{
    /** @return Collection<int, InventoryStockItem> */
    public function search(string $companyId, InventoryStockFilters $filters): Collection;
}
