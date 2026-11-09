<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

require_once __DIR__.'/BootstrapUsersSeeder.php';
require_once __DIR__.'/WithholdingPercentagesSeeder.php';
require_once __DIR__.'/V3ClinicalDocumentsSeeder.php';
require_once __DIR__.'/Cie10CatalogSeeder.php';
require_once __DIR__.'/VademecumSeeder.php';
require_once __DIR__.'/LaboratoryExamCatalogSeeder.php';

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            BootstrapUsersSeeder::class,
            LaboratoryExamCatalogSeeder::class,
            WithholdingPercentagesSeeder::class,
            V3ClinicalDocumentsSeeder::class,
            Cie10CatalogSeeder::class,
            VademecumSeeder::class,
        ]);
    }
}
