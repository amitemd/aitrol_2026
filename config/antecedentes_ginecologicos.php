<?php

return [
    'Ciclo menstrual y hormonales' => [
        ['nombre' => 'Menarquia', 'requiere_detalle' => true, 'detalle' => 'Indique la edad de la menarquia'],
        ['nombre' => 'Menopausia', 'requiere_detalle' => true, 'detalle' => 'Indique la edad de la menopausia'],
        ['nombre' => 'Ciclos menstruales irregulares'],
        ['nombre' => 'Amenorrea'],
        ['nombre' => 'Dismenorrea'],
        ['nombre' => 'Sangrado uterino anormal'],
        ['nombre' => 'Síndrome de ovario poliquístico'],
    ],
    'Patologías ginecológicas' => [
        ['nombre' => 'Endometriosis'],
        ['nombre' => 'Miomatosis uterina'],
        ['nombre' => 'Quistes ováricos'],
        ['nombre' => 'Enfermedad inflamatoria pélvica'],
        ['nombre' => 'Prolapso genital'],
        ['nombre' => 'Incontinencia urinaria'],
        ['nombre' => 'Infertilidad'],
    ],
    'Infecciones y controles' => [
        ['nombre' => 'Virus del papiloma humano (VPH)'],
        ['nombre' => 'Infección de transmisión sexual', 'requiere_detalle' => true, 'detalle' => 'Especifique la infección'],
        ['nombre' => 'Papanicolaou anormal'],
        ['nombre' => 'Displasia cervical'],
    ],
    'Antecedentes obstétricos' => [
        ['nombre' => 'Gestaciones, partos, cesáreas y abortos', 'requiere_detalle' => true, 'detalle' => 'Ejemplo: G3 P1 C1 A1'],
        ['nombre' => 'Embarazo ectópico'],
        ['nombre' => 'Abortos recurrentes'],
        ['nombre' => 'Preeclampsia o eclampsia'],
        ['nombre' => 'Diabetes gestacional'],
        ['nombre' => 'Hemorragia obstétrica'],
    ],
    'Tratamientos y otros' => [
        ['nombre' => 'Método anticonceptivo', 'requiere_detalle' => true, 'detalle' => 'Especifique el método anticonceptivo'],
        ['nombre' => 'Terapia hormonal', 'requiere_detalle' => true, 'detalle' => 'Especifique la terapia hormonal'],
        ['nombre' => 'Cáncer ginecológico', 'requiere_detalle' => true, 'detalle' => 'Especifique el tipo de cáncer'],
        ['nombre' => 'Otro antecedente ginecológico', 'requiere_detalle' => true, 'detalle' => 'Especifique el antecedente'],
    ],
];
