<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $lista = DB::table('menu')->where('ruta', 'clientes.index')->orderBy('id')->first();
            $ubicacion = $lista?->padre_id;
            $menuActual = $ubicacion === null ? null : DB::table('menu')->find($ubicacion);
            $clientes = $menuActual?->nombre === 'Clientes' && $menuActual->ruta === null
                ? $menuActual->id
                : $this->agregarOpcion($ubicacion, 'Clientes', null, 'bi-people', $lista?->orden ?? 20, $lista);

            if ($lista === null) {
                $this->agregarOpcion($clientes, 'Lista de clientes', 'clientes.index', 'bi-list-ul', 10);
            } else {
                DB::table('menu')->where('id', $lista->id)->update([
                    'nombre' => 'Lista de clientes',
                    'padre_id' => $clientes,
                    'orden' => 10,
                    'updated_at' => now(),
                ]);
            }

            $documentos = $this->agregarOpcion($clientes, 'Documentos', null, 'bi-folder2-open', 20, $lista);
            foreach ([
                ['Facturas', 'facturas', 'bi-receipt'],
                ['Notas de venta', 'notas-de-venta', 'bi-file-text'],
                ['Retenciones', 'retenciones', 'bi-file-earmark-check'],
                ['Notas de crédito', 'notas-de-credito', 'bi-file-minus'],
                ['Notas de débito', 'notas-de-debito', 'bi-file-plus'],
                ['Comprobantes de pago', 'comprobantes-de-pago', 'bi-credit-card'],
            ] as $posicion => [$nombre, $ruta, $icono]) {
                $this->agregarOpcion($documentos, $nombre, 'clientes.documentos.'.$ruta, $icono, 10 * ($posicion + 1));
            }

            $reportes = $this->agregarOpcion($clientes, 'Reportes', null, 'bi-bar-chart', 30, $lista);
            foreach ([
                ['Cuentas por cobrar', 'cuentas-por-cobrar', 'bi-wallet2'],
                ['Saldos de cartera', 'saldos-de-cartera', 'bi-journal-text'],
                ['Cartera vencida', 'cartera-vencida', 'bi-exclamation-circle'],
                ['Edad de cartera', 'edad-de-cartera', 'bi-hourglass-split'],
            ] as $posicion => [$nombre, $ruta, $icono]) {
                $this->agregarOpcion($reportes, $nombre, 'clientes.reportes.'.$ruta, $icono, 10 * ($posicion + 1));
            }
        });
    }

    public function down(): void
    {
        // Las opciones quedan disponibles para conservar cualquier ajuste posterior del administrador.
    }

    private function agregarOpcion(?int $padre, string $nombre, ?string $ruta, string $icono, int $orden, ?object $origen = null): int
    {
        $opcion = DB::table('menu')->where('padre_id', $padre)->where('ruta', $ruta);
        if ($ruta === null) {
            $opcion->where('nombre', $nombre);
        }

        return $opcion->value('id') ?? DB::table('menu')->insertGetId([
            'padre_id' => $padre,
            'nombre' => $nombre,
            'ruta' => $ruta,
            'icono' => $icono,
            'orden' => $orden,
            'activo' => $origen?->activo ?? true,
            'solo_administrador' => $origen?->solo_administrador ?? false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
