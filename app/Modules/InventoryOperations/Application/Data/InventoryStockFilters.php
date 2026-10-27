<?php

namespace App\Modules\InventoryOperations\Application\Data;

final readonly class InventoryStockFilters
{
    public function __construct(
        public ?int $productId = null,
        public ?int $warehouseId = null,
        public ?int $productTypeId = null,
        public ?string $productName = null,
    ) {}
}
