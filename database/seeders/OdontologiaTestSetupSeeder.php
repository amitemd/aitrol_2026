<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OdontologiaTestSetupSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'pruebas'])) {
            throw new RuntimeException('OdontologiaTestSetupSeeder solo puede ejecutarse en local, testing o pruebas.');
        }

        DB::transaction(function () {
            $this->call([
                OdontologiaCatalogSeeder::class,
                OdontologiaTestDataSeeder::class,
            ]);
        });
    }
}
