<?php

namespace App\Modules\Laboratory\Domain;

use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final class LaboratoryModule
{
    public function definition(): OperationalModule
    {
        return new OperationalModule(
            key: 'laboratory',
            eyebrow: 'Cadena diagnóstica',
            name: 'Laboratorio',
            summary: 'Conecta recepción, toma de muestras, procesamiento, validación y publicación de resultados.',
            mission: 'Hacer visible el estado real de cada muestra y conservar la trazabilidad del resultado.',
            accent: 'emerald',
            capabilities: ['Recepción', 'Control', 'Resultados', 'Exámenes', 'Protocolos', 'Agenda'],
            workflows: [
                new OperationalWorkflow('orden.index', 'Recepción de órdenes', 'Recibe y localiza órdenes de laboratorio.', 'Recepción', 'inbox'),
                new OperationalWorkflow('orden.index_control', 'Control de laboratorio', 'Supervisa muestras, estados y procesamiento.', 'Procesamiento', 'flask'),
                new OperationalWorkflow('nuevoResultado.realizar_new_lista', 'Ingreso de resultados', 'Registra y valida resultados analíticos.', 'Resultados', 'result'),
                new OperationalWorkflow('examen.index', 'Catálogo de exámenes', 'Administra pruebas, parámetros y referencias.', 'Configuración', 'catalog'),
                new OperationalWorkflow('protocolo.index', 'Protocolos', 'Agrupa exámenes y reglas diagnósticas.', 'Configuración', 'protocol'),
                new OperationalWorkflow('agendalabs.agenda', 'Agenda de laboratorio', 'Organiza citas y capacidad de atención.', 'Planificación', 'calendar'),
            ],
        );
    }
}
