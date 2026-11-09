<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Sis_medico\Services\LaboratoryExamCatalogSeederService;

class LaboratoryExamCatalogSeeder extends Seeder
{
    public function run()
    {
        $actor = config('installation.systems.id', '0000000001');
        $result = app(LaboratoryExamCatalogSeederService::class)
            ->seed($actor, '127.0.0.1');

        if ($this->command && $result['seeded']) {
            $this->command->info(
                $result['groupers'].' agrupadores y '.$result['exams'].' exámenes cargados.'
            );
        }
    }
}
