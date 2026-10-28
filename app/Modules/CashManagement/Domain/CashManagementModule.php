<?php

namespace App\Modules\CashManagement\Domain;

use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final class CashManagementModule
{
    public function definition(): OperationalModule
    {
        return new OperationalModule(
            key: 'cash-management',
            eyebrow: 'Control financiero diario',
            name: 'Caja, bancos y cierres',
            summary: 'Integra puntos de emisión, fondos, bancos, cierres operativos y cierre contable mensual.',
            mission: 'Cerrar cada jornada con trazabilidad desde el movimiento hasta el asiento contable.',
            accent: 'rose',
            capabilities: ['Puntos de emisión', 'Caja y bancos', 'Caja chica', 'Cierre general', 'Cierre de laboratorio', 'Cierre mensual'],
            workflows: [
                new OperationalWorkflow('caja.index', 'Puntos de emisión', 'Configura cajas y puntos autorizados por empresa.', 'Configuración', 'terminal'),
                new OperationalWorkflow('caja_banco.index', 'Caja y bancos', 'Administra cuentas y movimientos financieros.', 'Tesorería', 'bank'),
                new OperationalWorkflow('v3.cash-management.module', 'Caja chica', 'Registra reposiciones y control de fondos menores.', 'Tesorería', 'wallet', ['submodulo' => 'cajachica']),
                new OperationalWorkflow('reporte.index_cierre', 'Cierre de caja general', 'Consolida recaudación y comprobantes de la jornada.', 'Cierre', 'close'),
                new OperationalWorkflow('c_caja.index', 'Cierre de laboratorio', 'Controla caja y recaudación del laboratorio.', 'Cierre', 'flask'),
                new OperationalWorkflow('cierre.mensual.index', 'Cierre contable mensual', 'Bloquea y controla períodos financieros.', 'Contabilidad', 'lock'),
            ],
        );
    }
}
