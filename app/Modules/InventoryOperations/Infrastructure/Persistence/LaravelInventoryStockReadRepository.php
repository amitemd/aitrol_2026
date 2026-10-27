<?php

namespace App\Modules\InventoryOperations\Infrastructure\Persistence;

use App\Modules\InventoryOperations\Application\Contracts\InventoryStockReadRepository;
use App\Modules\InventoryOperations\Application\Data\InventoryStockFilters;
use App\Modules\InventoryOperations\Application\Data\InventoryStockItem;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelInventoryStockReadRepository implements InventoryStockReadRepository
{
    public function search(string $companyId, InventoryStockFilters $filters): Collection
    {
        $query = DB::table('inv_inventario as inventory')
            ->leftJoin('producto as product', 'product.id', '=', 'inventory.id_producto')
            ->leftJoin('marca as brand', 'brand.id', '=', 'product.id_marca')
            ->leftJoin('bodega as warehouse', 'warehouse.id', '=', 'inventory.id_bodega')
            ->where('inventory.id_empresa', $companyId)
            ->where('inventory.estado', '<>', 0)
            ->where('inventory.existencia', '>', 0)
            ->select([
                'inventory.id',
                'inventory.existencia',
                'inventory.existencia_min',
                'inventory.costo_promedio',
                'product.codigo as product_code',
                'product.nombre as product_name',
                'brand.nombre as brand_name',
                'warehouse.nombre as warehouse_name',
            ]);

        $this->applyFilters($query, $filters);

        return $query
            ->orderBy('inventory.id')
            ->get()
            ->map(fn (object $record): InventoryStockItem => new InventoryStockItem(
                id: (int) $record->id,
                productCode: (string) ($record->product_code ?? ''),
                productName: (string) ($record->product_name ?? ''),
                brandName: (string) ($record->brand_name ?? ''),
                warehouseName: (string) ($record->warehouse_name ?? ''),
                quantity: (int) $record->existencia,
                minimumQuantity: (int) $record->existencia_min,
                averageCost: (float) $record->costo_promedio,
            ));
    }

    private function applyFilters(Builder $query, InventoryStockFilters $filters): void
    {
        if ($filters->productId !== null) {
            $query->where('inventory.id_producto', $filters->productId);
        }

        if ($filters->warehouseId !== null) {
            $query->where('inventory.id_bodega', $filters->warehouseId);
        }

        if ($filters->productTypeId !== null) {
            $query->where('product.tipo_producto', $filters->productTypeId);
        }

        if ($filters->productName !== null) {
            $query->where('product.nombre', 'like', '%'.$this->escapeLike($filters->productName).'%');
        }
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
