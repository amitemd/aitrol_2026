<?php

namespace App\Modules\Administration\Domain;

use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final class AdministrationModule
{
    public function definition(): OperationalModule
    {
        return new OperationalModule(
            key: 'administration',
            eyebrow: 'Gobierno operativo',
            name: 'Administración, empresas y usuarios',
            summary: 'Gobierna identidad, acceso, empresa activa y parámetros que comparten todos los contextos.',
            mission: 'Una sola identidad institucional para el sistema moderno y los procesos heredados.',
            accent: 'amber',
            capabilities: ['Usuarios', 'Roles', 'Empresas', 'Empresa activa', 'Permisos'],
            workflows: [
                new OperationalWorkflow('new-system.users.index', 'Usuarios del nuevo sistema', 'Administra cuentas y empresas desde la interfaz actualizada.', 'Acceso', 'users'),
                new OperationalWorkflow('user-management.index', 'Usuarios heredados', 'Conserva el mantenimiento completo del proyecto base.', 'Compatibilidad', 'legacy'),
                new OperationalWorkflow('empresa.catalogo.index', 'Catálogo de empresas', 'Administra entidades y configuración institucional.', 'Institucional', 'building'),
                new OperationalWorkflow('plan_cuentas.seleccion_empresa', 'Selección contable de empresa', 'Define el contexto empresarial para la operación financiera.', 'Contexto', 'switch'),
            ],
        );
    }
}
