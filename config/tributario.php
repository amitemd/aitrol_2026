<?php

return [
    'secciones' => [
        'formulario-103' => ['Formulario 103', 'bi-file-earmark-text', 'Preparar Formulario 103'],
        'formulario-104' => ['Formulario 104', 'bi-file-earmark-ruled', 'Preparar Formulario 104'],
        'formulario-107' => ['Formulario 107', 'bi-person-vcard', 'Preparar Formulario 107'],
        'ats' => ['ATS', 'bi-file-earmark-spreadsheet', 'Preparar ATS'],
        'declaraciones' => ['Declaraciones', 'bi-journal-check', null],
        'documentos' => ['Gestión documental', 'bi-folder2-open', 'Añadir respaldo'],
        'procesos' => ['Procesos electrónicos', 'bi-arrow-repeat', null],
    ],
    'formatos' => ['formulario-103', 'formulario-104', 'formulario-107', 'ats'],
    'estados' => ['borrador' => 'Borrador', 'revisado' => 'Revisado', 'presentado' => 'Presentación registrada', 'entregado' => 'Entrega registrada'],
    'archivos' => ['respaldo' => 'Documento de respaldo', 'acuse' => 'Acuse o constancia', 'xml' => 'XML recibido'],
];
