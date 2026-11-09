<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScopeProductPricesByCompany extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('precio_producto') || ! Schema::hasTable('ct_productos')) {
            return;
        }

        if (! Schema::hasColumn('precio_producto', 'id_empresa')) {
            Schema::table('precio_producto', function (Blueprint $table) {
                $table->string('id_empresa', 17)->nullable()->after('id');
            });
        }

        // Un código pudo existir en varias empresas cuando las tarifas eran
        // globales. Se conserva una copia para cada dueño antes de asignarlas.
        DB::statement("INSERT INTO precio_producto
            (id_empresa, codigo_producto, nivel, precio, estado, id_usuariocrea,
             id_usuariomod, ip_creacion, ip_modificacion, created_at, updated_at)
            SELECT companies.id_empresa, price.codigo_producto, price.nivel,
                   price.precio, price.estado, price.id_usuariocrea,
                   price.id_usuariomod, price.ip_creacion, price.ip_modificacion,
                   price.created_at, price.updated_at
            FROM precio_producto price
            INNER JOIN (
                SELECT codigo, id_empresa
                FROM ct_productos
                WHERE id_empresa IS NOT NULL AND id_empresa <> ''
                GROUP BY codigo, id_empresa
            ) companies ON companies.codigo = price.codigo_producto
            INNER JOIN (
                SELECT codigo, MIN(id_empresa) AS first_company
                FROM ct_productos
                WHERE id_empresa IS NOT NULL AND id_empresa <> ''
                GROUP BY codigo
            ) owner ON owner.codigo = price.codigo_producto
            WHERE price.id_empresa IS NULL
              AND companies.id_empresa <> owner.first_company
              AND NOT EXISTS (
                  SELECT 1
                  FROM precio_producto existing_price
                  WHERE existing_price.id_empresa = companies.id_empresa
                    AND existing_price.codigo_producto = price.codigo_producto
                    AND COALESCE(existing_price.nivel, '') = COALESCE(price.nivel, '')
              )");

        DB::statement("UPDATE precio_producto price
            INNER JOIN (
                SELECT codigo, MIN(id_empresa) AS id_empresa
                FROM ct_productos
                WHERE id_empresa IS NOT NULL AND id_empresa <> ''
                GROUP BY codigo
            ) product ON product.codigo = price.codigo_producto
            SET price.id_empresa = product.id_empresa
            WHERE price.id_empresa IS NULL");

        if (! $this->hasIndex('precio_producto', 'precio_producto_empresa_codigo_estado_idx')) {
            Schema::table('precio_producto', function (Blueprint $table) {
                $table->index(
                    ['id_empresa', 'codigo_producto', 'estado'],
                    'precio_producto_empresa_codigo_estado_idx'
                );
            });
        }
    }

    public function down()
    {
        if (! Schema::hasTable('precio_producto') || ! Schema::hasColumn('precio_producto', 'id_empresa')) {
            return;
        }

        if ($this->hasIndex('precio_producto', 'precio_producto_empresa_codigo_estado_idx')) {
            Schema::table('precio_producto', function (Blueprint $table) {
                $table->dropIndex('precio_producto_empresa_codigo_estado_idx');
            });
        }

        Schema::table('precio_producto', function (Blueprint $table) {
            $table->dropColumn('id_empresa');
        });
    }

    private function hasIndex($table, $name)
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $name)
            ->exists();
    }
}
