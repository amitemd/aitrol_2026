<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveExampleContactsFromSeededInsurances extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('seguros')) {
            return;
        }
        DB::table('seguros')->whereIn(DB::raw('UPPER(nombre)'), ['SALUDSA', 'VERIS'])
            ->where('ruc_cliente', '0999999999001')->update(['ruc_cliente' => null]);
        DB::table('seguros')->whereIn(DB::raw('UPPER(nombre)'), ['SALUDSA', 'VERIS'])
            ->whereRaw('LOWER(email) LIKE ?', ['%@example.org'])->update(['email' => null]);
    }

    public function down()
    {
        // Los datos ilustrativos no se restauran.
    }
}
