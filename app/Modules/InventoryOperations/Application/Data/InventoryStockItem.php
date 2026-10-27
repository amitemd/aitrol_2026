<?php

namespace App\Modules\InventoryOperations\Application\Data;

final readonly class InventoryStockItem
{
    public function __construct(
        public int $id,
        public string $productCode,
        public string $productName,
        public string $brandName,
        public string $warehouseName,
        public int $quantity,
        public int $minimumQuantity,
        public float $averageCost,
    ) {}
}
