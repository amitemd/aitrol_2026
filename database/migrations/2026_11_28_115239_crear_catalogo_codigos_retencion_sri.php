<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sri_codigos_retencion', function (Blueprint $table): void {
            $table->id();
            $table->string('tipo', 5);
            $table->string('codigo', 5);
            $table->string('descripcion');
            $table->decimal('porcentaje', 5, 2)->nullable();
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->unique(['tipo', 'codigo', 'vigente_desde']);
        });

        $renta = [
            ['303', 'Honorarios de profesionales con relación al título', 10, 10],
            ['303A', 'Servicios profesionales de sociedades residentes', 3, 5],
            ['304', 'Servicios intelectuales de personas naturales sin relación con el título', 10, 10],
            ['304A', 'Comisiones por servicios intelectuales de personas naturales', 10, 10],
            ['304B', 'Servicios de notarios y registradores', 10, 10],
            ['304C', 'Actividades de deportistas, árbitros y equipos técnicos', 8, 10],
            ['304D', 'Actividades de artistas', 8, 10],
            ['304E', 'Honorarios por docencia', 10, 10],
            ['307', 'Servicios de personas naturales con predominio de mano de obra', 2, 3],
            ['308', 'Uso de imagen o aprovechamiento del renombre', 10, 10],
            ['309', 'Servicios de medios y agencias de publicidad', 2.75, 3],
            ['310', 'Transporte privado de pasajeros y transporte de carga', 1, 1],
            ['311', 'Pagos con liquidación de compra por rusticidad o nivel cultural', 2, 3],
            ['312', 'Adquisición de bienes muebles corporales', 1.75, 2],
            ['312A', 'Adquisición de productos agropecuarios y afines al productor', 1, 1],
            ['312C', 'Adquisición de productos agropecuarios y afines al comercializador', 1.75, 1.75],
            ['314A', 'Regalías por franquicias pagadas a personas naturales', 10, 10],
            ['314B', 'Derechos de propiedad intelectual pagados a personas naturales', 10, 10],
            ['314C', 'Regalías por franquicias pagadas a sociedades', 10, 10],
            ['314D', 'Derechos de propiedad intelectual pagados a sociedades', 10, 10],
            ['319', 'Cuotas y opción de compra de arrendamiento mercantil', 2, 2],
            ['320', 'Arriendo de inmuebles', 10, 10],
            ['322', 'Primas de seguros y reaseguros sobre la base aplicable', 1, 2],
            ['323P', 'Intereses reconocidos por entidades públicas', 2, 3],
            ['323Q', 'Otros intereses y rendimientos financieros gravados', 2, 3],
            ['324A', 'Créditos entre entidades financieras o de economía popular', 1, 2],
            ['324B', 'Inversiones entre entidades financieras o de economía popular', 1, 2],
            ['324C', 'Intermediación del BCE entre entidades financieras', 1, 2],
            ['332', 'Adquisiciones no sujetas a retención', 0, 0],
            ['332B', 'Adquisición de bienes inmuebles', 0, 0],
            ['332C', 'Transporte público de pasajeros', 0, 0],
            ['332G', 'Pagos con tarjeta sujetos al régimen de retención del emisor', 0, 0],
            ['343', 'Adquisiciones a contribuyentes RIMPE emprendedores', 1, 1],
            ['343A', 'Adquisición de energía eléctrica', 1, 2],
            ['343B', 'Construcción, urbanización y lotización', 1.75, 2],
            ['344A', 'Pagos locales con tarjeta reportados por entidades financieras', 2, 2],
            ['344B', 'Adquisición nacional de sustancias minerales', 2, 2],
            ['346', 'Otros conceptos con porcentaje variable', null, null],
            ['3482', 'Comisiones a sociedades y establecimientos permanentes residentes', 3, 5],
        ];
        $filas = [];
        foreach ($renta as [$codigo, $descripcion, $anterior, $vigente]) {
            if ($anterior !== $vigente) {
                $filas[] = ['renta', $codigo, $descripcion, $anterior, '2024-03-01', '2026-02-28'];
            }
            $filas[] = ['renta', $codigo, $descripcion, $vigente, $anterior === $vigente ? '2024-03-01' : '2026-03-01', null];
        }
        foreach ([9 => 10, 10 => 20, 1 => 30, 11 => 50, 2 => 70, 3 => 100, 7 => 0, 8 => 0] as $codigo => $porcentaje) {
            $descripcion = match ($codigo) {
                7 => 'Retención de IVA en cero',
                8 => 'No procede retención de IVA',
                default => 'Retención del '.$porcentaje.' % del IVA causado',
            };
            $filas[] = ['iva', (string) $codigo, $descripcion, $porcentaje, '2026-01-01', null];
        }
        $campos = ['tipo', 'codigo', 'descripcion', 'porcentaje', 'vigente_desde', 'vigente_hasta'];
        DB::table('sri_codigos_retencion')->insert(array_map(fn (array $fila): array => array_combine($campos, $fila), $filas));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sri_codigos_retencion');
    }
};
