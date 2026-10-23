<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAmortizacionPrestamoLineas extends Migration
{
    public function up()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS amortizacion_prestamos_lineas (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            id_prestamo BIGINT UNSIGNED NOT NULL,
            identificador VARCHAR(80) NOT NULL,
            monto DECIMAL(18,2) NOT NULL,
            tasa_anual DECIMAL(10,6) NOT NULL,
            plazo_meses INT UNSIGNED NOT NULL,
            numero_cuotas INT UNSIGNED NOT NULL,
            fecha_primer_pago DATE NOT NULL,
            metodo ENUM('frances','aleman') NOT NULL,
            seguro_tipo ENUM('ninguno','porcentaje_saldo','fijo') NOT NULL DEFAULT 'ninguno',
            seguro_valor DECIMAL(18,6) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            PRIMARY KEY (id),
            UNIQUE KEY amortizacion_linea_identificador_unique (id_prestamo, identificador),
            KEY amortizacion_linea_prestamo_index (id_prestamo),
            CONSTRAINT amortizacion_linea_prestamo_fk FOREIGN KEY (id_prestamo) REFERENCES amortizacion_prestamos (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        DB::statement("ALTER TABLE amortizacion_prestamos_detalle
            ADD COLUMN id_linea BIGINT UNSIGNED NULL AFTER id_prestamo");
        DB::statement("ALTER TABLE amortizacion_prestamos_detalle
            DROP INDEX amortizacion_prestamos_detalle_id_prestamo_numero_cuota_unique,
            ADD KEY amortizacion_detalle_linea_index (id_linea),
            ADD CONSTRAINT amortizacion_detalle_linea_fk FOREIGN KEY (id_linea) REFERENCES amortizacion_prestamos_lineas (id) ON DELETE CASCADE,
            ADD UNIQUE KEY amortizacion_detalle_linea_numero_unique (id_prestamo, id_linea, numero_cuota)");
    }

    public function down()
    {
        DB::statement("ALTER TABLE amortizacion_prestamos_detalle
            DROP FOREIGN KEY amortizacion_detalle_linea_fk,
            DROP INDEX amortizacion_detalle_linea_index,
            DROP INDEX amortizacion_detalle_linea_numero_unique,
            ADD UNIQUE KEY amortizacion_prestamos_detalle_id_prestamo_numero_cuota_unique (id_prestamo, numero_cuota),
            DROP COLUMN id_linea");
        DB::statement('DROP TABLE IF EXISTS amortizacion_prestamos_lineas');
    }
}
