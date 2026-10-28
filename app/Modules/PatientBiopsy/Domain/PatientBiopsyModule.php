<?php

namespace App\Modules\PatientBiopsy\Domain;

use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final class PatientBiopsyModule
{
    public function definition(): OperationalModule
    {
        return new OperationalModule(
            key: 'patient-biopsy',
            eyebrow: 'Identidad clínica',
            name: 'Pacientes y biopsias',
            summary: 'Centraliza la identidad del paciente, sus antecedentes diagnósticos y la trazabilidad de biopsias.',
            mission: 'Encontrar la historia correcta antes de crear una nueva, preservando cada evidencia clínica.',
            accent: 'violet',
            capabilities: ['Directorio de pacientes', 'Biopsias', 'Historial de laboratorio', 'Historial de exámenes'],
            workflows: [
                new OperationalWorkflow('paciente.index', 'Directorio de pacientes', 'Busca y administra la ficha maestra del paciente.', 'Identificación', 'people'),
                new OperationalWorkflow('biopsias_paciente.index', 'Ingreso de biopsias', 'Registra y controla resultados asociados al paciente.', 'Diagnóstico', 'biopsy'),
                new OperationalWorkflow('paciente.historial_orden_lab_paciente', 'Historial de laboratorio', 'Consulta órdenes y resultados longitudinales.', 'Trazabilidad', 'history'),
                new OperationalWorkflow('paciente.historial_examenes', 'Historial de exámenes', 'Revisa estudios y documentos clínicos previos.', 'Trazabilidad', 'archive'),
            ],
        );
    }
}
