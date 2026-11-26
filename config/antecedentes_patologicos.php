<?php

return [
    'Cardiovasculares y metabólicos' => [
        ['nombre' => 'Hipertensión arterial'],
        ['nombre' => 'Diabetes mellitus tipo 1'],
        ['nombre' => 'Diabetes mellitus tipo 2'],
        ['nombre' => 'Dislipidemia'],
        ['nombre' => 'Obesidad'],
        ['nombre' => 'Cardiopatía isquémica o enfermedad coronaria'],
        ['nombre' => 'Infarto de miocardio previo'],
        ['nombre' => 'Insuficiencia cardíaca'],
        ['nombre' => 'Arritmia cardíaca'],
        ['nombre' => 'Accidente cerebrovascular previo'],
    ],
    'Respiratorios' => [
        ['nombre' => 'Asma bronquial'],
        ['nombre' => 'Enfermedad pulmonar obstructiva crónica (EPOC)'],
        ['nombre' => 'Apnea obstructiva del sueño'],
    ],
    'Renales, hepáticos y digestivos' => [
        ['nombre' => 'Enfermedad renal crónica'],
        ['nombre' => 'Litiasis renal'],
        ['nombre' => 'Hepatopatía crónica o cirrosis'],
        ['nombre' => 'Hepatitis B'],
        ['nombre' => 'Hepatitis C'],
        ['nombre' => 'Colelitiasis o cálculos biliares'],
        ['nombre' => 'Gastritis o enfermedad ulcerosa péptica'],
    ],
    'Endocrinos, neurológicos y hematológicos' => [
        ['nombre' => 'Hipotiroidismo'],
        ['nombre' => 'Hipertiroidismo'],
        ['nombre' => 'Epilepsia'],
        ['nombre' => 'Anemia'],
    ],
    'Infecciosos y otros relevantes' => [
        ['nombre' => 'Tuberculosis'],
        ['nombre' => 'VIH'],
        ['nombre' => 'Cáncer', 'requiere_detalle' => true, 'detalle' => 'Especifique el tipo de cáncer'],
        ['nombre' => 'Enfermedad autoinmune', 'requiere_detalle' => true, 'detalle' => 'Especifique la enfermedad autoinmune'],
    ],
];
