<?php

return [
    'secciones' => [
        'cuentas' => ['Cajas y cuentas bancarias', 'bi-bank', 'Nueva cuenta'],
        'movimientos' => ['Ingresos y egresos', 'bi-cash-stack', 'Nuevo movimiento'],
        'transferencias' => ['Transferencias internas', 'bi-arrow-left-right', 'Nueva transferencia'],
        'depositos' => ['Depósitos de caja', 'bi-box-arrow-in-down', 'Nuevo depósito'],
        'notas-debito' => ['Notas de débito bancarias', 'bi-file-earmark-minus', 'Nueva nota de débito'],
        'notas-credito' => ['Notas de crédito bancarias', 'bi-file-earmark-plus', 'Nueva nota de crédito'],
        'cierres' => ['Aperturas y cierres de caja', 'bi-safe', 'Abrir caja'],
        'extractos' => ['Extractos bancarios', 'bi-file-earmark-spreadsheet', 'Importar extracto'],
        'conciliacion' => ['Conciliación bancaria', 'bi-check2-square', null],
        'reportes' => ['Libro de caja y bancos', 'bi-journal-text', null],
    ],
    'operaciones' => [
        'movimientos' => ['ingreso', 'egreso', 'reversa'],
        'transferencias' => ['transferencia'],
        'depositos' => ['deposito'],
        'notas-debito' => ['debito'],
        'notas-credito' => ['credito'],
    ],
    'tipos' => [
        'ingreso' => 'Ingreso', 'egreso' => 'Egreso', 'transferencia' => 'Transferencia',
        'deposito' => 'Depósito de caja', 'debito' => 'Débito bancario', 'credito' => 'Crédito bancario', 'reversa' => 'Reversión',
    ],
    'monedas' => ['USD', 'EUR', 'GBP'],
];
