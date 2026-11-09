<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3InventoryAdjustmentDetails extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_inventory_adjustment_details')) {
            return;
        }

        Schema::create('v3_inventory_adjustment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('movement_id');
            $table->unsignedBigInteger('historical_kardex_id')->nullable();
            $table->unsignedBigInteger('modern_kardex_id')->nullable();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('warehouse_id');
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->string('lot', 255)->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('company_id', 13);
            $table->string('created_by', 13);
            $table->string('updated_by', 13);
            $table->string('creation_ip', 40);
            $table->string('modification_ip', 40);
            $table->timestamps();
            $table->index(['company_id', 'movement_id'], 'v3_inv_adj_company_movement_idx');
            $table->index(['company_id', 'product_id', 'warehouse_id'], 'v3_inv_adj_product_warehouse_idx');
            $table->unique('historical_kardex_id', 'v3_inv_adj_historical_kardex_uq');
            $table->unique('modern_kardex_id', 'v3_inv_adj_modern_kardex_uq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_inventory_adjustment_details');
    }
}
