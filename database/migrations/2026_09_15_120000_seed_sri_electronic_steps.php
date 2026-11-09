<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedSriElectronicSteps extends Migration
{
    public function up()
    {
        $steps = [
            1 => 'Documento generado',
            2 => 'XML validado por XSD',
            3 => 'XML firmado',
            4 => 'Recibido por el SRI',
            5 => 'Autorizado por el SRI',
            6 => 'Error en la generación del documento',
            7 => 'Error en la generación del XML',
            8 => 'Error en la validación XSD',
            9 => 'Error en la recepción por el SRI',
            10 => 'Error en la autorización por el SRI',
            11 => 'Correo enviado',
            12 => 'En proceso en el SRI',
        ];

        foreach ($steps as $id => $name) {
            if (! DB::table('de_pasos')->where('id', $id)->exists()) {
                DB::table('de_pasos')->insert([
                    'id' => $id,
                    'nombre' => $name,
                    'estado' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Los pasos pueden ser utilizados por documentos heredados; no se eliminan.
    }
}
