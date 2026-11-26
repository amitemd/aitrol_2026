<?php

return [
    'Cardiovasculares y metabólicos' => [
        ['nombre' => 'Hipertensión arterial'],
        ['nombre' => 'Diabetes mellitus'],
        ['nombre' => 'Cardiopatía isquémica o enfermedad coronaria'],
        ['nombre' => 'Infarto de miocardio'],
        ['nombre' => 'Accidente cerebrovascular'],
        ['nombre' => 'Insuficiencia cardíaca'],
        ['nombre' => 'Dislipidemia'],
        ['nombre' => 'Obesidad'],
    ],
    'Oncológicos' => [
        ['nombre' => 'Cáncer de mama'],
        ['nombre' => 'Cáncer de colon o recto'],
        ['nombre' => 'Cáncer de próstata'],
        ['nombre' => 'Cáncer de estómago'],
        ['nombre' => 'Cáncer de útero o cuello uterino'],
        ['nombre' => 'Cáncer de pulmón'],
        ['nombre' => 'Otro tipo de cáncer', 'requiere_detalle' => true, 'detalle' => 'Especifique el tipo de cáncer'],
    ],
    'Renales, respiratorios y endocrinos' => [
        ['nombre' => 'Enfermedad renal crónica'],
        ['nombre' => 'Asma bronquial'],
        ['nombre' => 'Enfermedad tiroidea'],
        ['nombre' => 'Enfermedad autoinmune', 'requiere_detalle' => true, 'detalle' => 'Especifique la enfermedad autoinmune'],
    ],
    'Neurológicos, hematológicos y otros' => [
        ['nombre' => 'Epilepsia'],
        ['nombre' => 'Alzhéimer o demencia'],
        ['nombre' => 'Enfermedad psiquiátrica', 'requiere_detalle' => true, 'detalle' => 'Especifique la enfermedad psiquiátrica'],
        ['nombre' => 'Trombosis o trastorno de coagulación'],
        ['nombre' => 'Enfermedad hereditaria', 'requiere_detalle' => true, 'detalle' => 'Especifique la enfermedad hereditaria'],
    ],
];
