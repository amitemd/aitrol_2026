<?php

return [
    'migration_order' => [
        'institutional',
        'clinical-care',
        'diagnostics',
        'hospitalization',
        'inventory-operations',
        'billing',
        'accounting-finance',
        'human-resources',
        'support-integrations',
    ],

    'contexts' => [
        'institutional' => [
            'name' => 'Identidad e institucional',
            'status' => 'in_progress',
            'depends_on' => [],
            'legacy_modules' => [
                'Núcleo institucional',
                'Administración hospitalaria',
                'Plataforma V3',
            ],
        ],
        'clinical-care' => [
            'name' => 'Pacientes y atención clínica',
            'status' => 'in_progress',
            'depends_on' => ['institutional'],
            'legacy_modules' => [
                'Historia clínica y admisión',
                'Historia clínica HC4',
                'Turnero',
                'Preanestésico',
                'Consulta médica',
                'Enfermería',
                'Técnicas quirúrgicas',
                'Preparaciones',
            ],
        ],
        'diagnostics' => [
            'name' => 'Diagnóstico',
            'status' => 'in_progress',
            'depends_on' => ['institutional', 'clinical-care'],
            'legacy_modules' => [
                'Laboratorio',
                'Pentax y endoscopía',
                'CIE-10',
            ],
        ],
        'hospitalization' => [
            'name' => 'Hospitalización',
            'status' => 'in_progress',
            'depends_on' => ['institutional', 'clinical-care', 'diagnostics'],
            'legacy_modules' => [
                'Hospitalización',
                'Hospital IESS',
            ],
        ],
        'inventory-operations' => [
            'name' => 'Inventario y operaciones',
            'status' => 'in_progress',
            'depends_on' => ['institutional'],
            'legacy_modules' => [
                'Insumos e inventario',
                'Servicios generales',
                'Control de temperatura',
                'Limpieza y desinfección',
                'Disponibilidad',
            ],
        ],
        'billing' => [
            'name' => 'Facturación electrónica',
            'status' => 'in_progress',
            'depends_on' => ['institutional', 'clinical-care', 'inventory-operations'],
            'legacy_modules' => [
                'Archivo plano y aseguradoras',
                'SRI electrónico',
                'Guías de remisión',
            ],
        ],
        'accounting-finance' => [
            'name' => 'Contabilidad y finanzas',
            'status' => 'in_progress',
            'depends_on' => ['institutional', 'billing'],
            'legacy_modules' => [
                'Contabilidad',
                'Activos fijos',
                'Planeación financiera',
                'Amortizaciones',
            ],
        ],
        'human-resources' => [
            'name' => 'Talento humano',
            'status' => 'in_progress',
            'depends_on' => ['institutional', 'accounting-finance'],
            'legacy_modules' => [
                'Recursos humanos',
                'Mantenimiento de nómina',
            ],
        ],
        'support-integrations' => [
            'name' => 'Soporte e integraciones',
            'status' => 'in_progress',
            'depends_on' => ['institutional'],
            'legacy_modules' => [
                'Permisos y tickets',
                'Back office',
                'Soporte técnico',
                'Sensores',
            ],
        ],
    ],
];
