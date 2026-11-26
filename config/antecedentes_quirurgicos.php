<?php

return [
    'Abdominales y digestivos' => [
        ['nombre' => 'Apendicectomía'],
        ['nombre' => 'Colecistectomía'],
        ['nombre' => 'Hernioplastia'],
        ['nombre' => 'Cirugía gástrica o intestinal'],
        ['nombre' => 'Cirugía bariátrica'],
        ['nombre' => 'Cirugía de hemorroides'],
    ],
    'Ginecológicos y urológicos' => [
        ['nombre' => 'Cesárea'],
        ['nombre' => 'Histerectomía'],
        ['nombre' => 'Ooforectomía'],
        ['nombre' => 'Ligadura de trompas'],
        ['nombre' => 'Prostatectomía'],
        ['nombre' => 'Nefrectomía'],
        ['nombre' => 'Cirugía de cálculos renales'],
    ],
    'Traumatológicos y ortopédicos' => [
        ['nombre' => 'Osteosíntesis por fractura'],
        ['nombre' => 'Artroscopia'],
        ['nombre' => 'Reemplazo articular o prótesis'],
        ['nombre' => 'Cirugía de columna'],
        ['nombre' => 'Amputación'],
    ],
    'Cardiovasculares, tórax y cabeza' => [
        ['nombre' => 'Angioplastia o colocación de stent'],
        ['nombre' => 'Cirugía de bypass coronario'],
        ['nombre' => 'Implante de marcapasos'],
        ['nombre' => 'Cirugía pulmonar'],
        ['nombre' => 'Tiroidectomía'],
        ['nombre' => 'Amigdalectomía'],
        ['nombre' => 'Cirugía de catarata'],
    ],
    'Oncológicos y otros' => [
        ['nombre' => 'Mastectomía'],
        ['nombre' => 'Resección de tumor', 'requiere_detalle' => true, 'detalle' => 'Especifique el tumor o la región intervenida'],
        ['nombre' => 'Trasplante', 'requiere_detalle' => true, 'detalle' => 'Especifique el órgano trasplantado'],
        ['nombre' => 'Otra cirugía', 'requiere_detalle' => true, 'detalle' => 'Especifique la cirugía realizada'],
    ],
];
