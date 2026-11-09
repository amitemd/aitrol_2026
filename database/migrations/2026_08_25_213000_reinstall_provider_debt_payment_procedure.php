<?php

use Illuminate\Database\Migrations\Migration;

class ReinstallProviderDebtPaymentProcedure extends Migration
{
    /**
     * Reinstala la version corregida aun cuando la migracion inicial del SP
     * ya haya sido ejecutada en el servidor.
     *
     * @return void
     */
    public function up()
    {
        if (! class_exists('CreateProviderDebtPaymentProcedure')) {
            require_once __DIR__.'/2026_08_25_212000_create_provider_debt_payment_procedure.php';
        }

        $installer = new CreateProviderDebtPaymentProcedure;
        $installer->up();
    }

    /**
     * La rutina pertenece a la migracion inicial. No se elimina al revertir
     * solo esta reparacion para no romper el reporte en una base ya instalada.
     *
     * @return void
     */
    public function down()
    {
        // Sin cambios: la migracion inicial administra la eliminacion del SP.
    }
}
