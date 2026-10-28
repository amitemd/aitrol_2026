<?php

namespace App\Modules\MedicalScheduling\Domain;

use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final class MedicalSchedulingModule
{
    public function definition(): OperationalModule
    {
        return new OperationalModule(
            key: 'medical-scheduling',
            eyebrow: 'Atención conectada',
            name: 'Parte médica, agenda y preagenda',
            summary: 'Coordina la jornada clínica desde la reserva hasta el ingreso del paciente y la emisión de órdenes.',
            mission: 'Una línea de tiempo clínica única, sin romper las rutas ni los datos históricos.',
            accent: 'cyan',
            capabilities: ['Agenda médica', 'Preagenda', 'Turnero', 'Historia clínica', 'Órdenes 012'],
            workflows: [
                new OperationalWorkflow('agenda.index', 'Agenda médica', 'Consulta y administra las citas registradas.', 'Planificación', 'calendar'),
                new OperationalWorkflow('agenda.agenda2', 'Agenda por profesional', 'Organiza disponibilidad y horarios médicos.', 'Planificación', 'pulse'),
                new OperationalWorkflow('preagenda.index', 'Preagenda', 'Prepara procedimientos y asignaciones de sala.', 'Preparación', 'layers'),
                new OperationalWorkflow('historia_clinica.fullcontrol', 'Pacientes del día', 'Da continuidad al episodio clínico activo.', 'Atención', 'medical'),
                new OperationalWorkflow('turnero_sala_administracion', 'Administración de turnos', 'Gestiona módulos y avance de la sala de espera.', 'Atención', 'queue'),
                new OperationalWorkflow('orden_ingresada_formato012', 'Órdenes formato 012', 'Revisa solicitudes clínicas ingresadas.', 'Documentación', 'document'),
            ],
        );
    }
}
