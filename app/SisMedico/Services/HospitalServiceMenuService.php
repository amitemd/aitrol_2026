<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Support\RequestSchema as Schema;

class HospitalServiceMenuService
{
    const TABLE = 'hospital_company_services';

    /**
     * Catálogo único de accesos publicados en la portada hospitalaria.
     */
    public function catalog()
    {
        return array(
            'emergency' => array(
                'label' => 'Emergencia', 'description' => 'Atención inmediata',
                'icon' => 'fa-ambulance', 'color' => 'module-red', 'tour' => 'hospital-emergency',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '2'), 'order' => 10,
            ),
            'surgery' => array(
                'label' => 'Cirugía', 'description' => 'Procesos quirúrgicos',
                'icon' => 'fa-bed', 'color' => 'module-blue', 'tour' => 'hospital-surgery',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '5'), 'order' => 20,
            ),
            'laboratory' => array(
                'label' => 'Laboratorio', 'description' => 'Análisis clínicos',
                'icon' => 'fa-flask', 'color' => 'module-purple', 'tour' => 'hospital-laboratory',
                'route' => 'hospital_laboratorio.index', 'parameters' => array(), 'order' => 30,
            ),
            'pharmacy' => array(
                'label' => 'Farmacia', 'description' => 'Gestión de medicamentos',
                'icon' => 'fa-medkit', 'color' => 'module-green', 'tour' => 'hospital-pharmacy',
                'route' => 'hospital.master_farmacia', 'parameters' => array(), 'order' => 40,
            ),
            'imaging' => array(
                'label' => 'Imágenes', 'description' => 'Diagnóstico por imagen',
                'icon' => 'fa-file-image-o', 'color' => 'module-cyan', 'tour' => 'hospital-images',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '4'), 'order' => 50,
            ),
            'neonatal_icu' => array(
                'label' => 'UCIN', 'description' => 'Cuidado intensivo neonatal',
                'icon' => 'fa-heartbeat', 'color' => 'module-pink', 'tour' => 'hospital-ucin',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '7'), 'order' => 60,
            ),
            'icu' => array(
                'label' => 'UCI', 'description' => 'Cuidados intensivos',
                'icon' => 'fa-heartbeat', 'color' => 'module-orange', 'tour' => 'hospital-uci',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '6'), 'order' => 70,
            ),
            'hospitalization' => array(
                'label' => 'Hospitalización', 'description' => 'Seguimiento del paciente',
                'icon' => 'fa-hospital-o', 'color' => 'module-teal', 'tour' => 'hospital-inpatient',
                'route' => 'hospital.index_modulos', 'parameters' => array('id_paso' => '3'), 'order' => 80,
            ),
            'clinical_orders' => array(
                'label' => 'Órdenes y resultados', 'description' => 'Seguimiento clínico transversal',
                'icon' => 'fa-clipboard', 'color' => 'module-blue', 'tour' => 'hospital-clinical-orders',
                'route' => 'clinical_orders.index', 'parameters' => array(), 'order' => 90,
            ),
            'clinical_consults' => array(
                'label' => 'Interconsultas', 'description' => 'SLA, aceptación y respuesta',
                'icon' => 'fa-user-md', 'color' => 'module-purple', 'tour' => 'hospital-clinical-consults',
                'route' => 'clinical_consults.index', 'parameters' => array(), 'order' => 100,
            ),
            'blood_bank' => array(
                'label' => 'Banco de sangre', 'description' => 'Stock y solicitudes transfusionales',
                'icon' => 'fa-tint', 'color' => 'module-red', 'tour' => 'hospital-blood-bank',
                'route' => 'blood_bank.index', 'parameters' => array(), 'order' => 110,
            ),
            'oncology_consults' => array(
                'label' => 'Interconsultas oncológicas', 'description' => 'Triaje, revisión y plan terapéutico',
                'icon' => 'fa-medkit', 'color' => 'module-teal', 'tour' => 'hospital-oncology-consults',
                'route' => 'oncology_consults.index', 'parameters' => array(), 'order' => 120,
            ),
        );
    }

    public function companyId($required = false)
    {
        $companyId = trim((string) session('id_empresa'));
        if ($required && $companyId === '') {
            throw new RuntimeException('Seleccione una empresa antes de configurar los servicios hospitalarios.');
        }
        return $companyId;
    }

    public function isInstalled()
    {
        return Schema::hasTable(self::TABLE);
    }

    public function forCompany($companyId)
    {
        $companyId = trim((string) $companyId);
        $catalog = $this->catalog();
        $settings = collect();

        if ($companyId !== '' && $this->isInstalled()) {
            $settings = DB::table(self::TABLE)->where('id_empresa', $companyId)
                ->get()->keyBy('service_key');
        }

        $services = array();
        foreach ($catalog as $key => $service) {
            $setting = $settings->get($key);
            $service['key'] = $key;
            $service['enabled'] = is_null($setting) ? true : (bool) $setting->enabled;
            $service['order'] = is_null($setting) ? $service['order'] : (int) $setting->sort_order;
            $services[] = $service;
        }

        usort($services, function ($left, $right) {
            if ($left['order'] === $right['order']) return strcmp($left['label'], $right['label']);
            return $left['order'] <=> $right['order'];
        });

        return collect($services);
    }

    public function visibleForCompany($companyId)
    {
        return $this->forCompany($companyId)->filter(function ($service) {
            return $service['enabled'];
        })->values();
    }

    public function saveForCompany($companyId, array $enabledKeys, $actorId, $ip)
    {
        $companyId = trim((string) $companyId);
        if ($companyId === '') {
            throw new RuntimeException('Seleccione una empresa antes de guardar la configuración.');
        }
        if (!$this->isInstalled()) {
            throw new RuntimeException('La configuración por empresa todavía no está instalada. Ejecute las migraciones.');
        }

        $catalog = $this->catalog();
        $unknown = array_diff($enabledKeys, array_keys($catalog));
        if (!empty($unknown)) {
            throw new RuntimeException('La selección contiene servicios hospitalarios no reconocidos.');
        }

        $enabled = array_fill_keys($enabledKeys, true);
        $now = date('Y-m-d H:i:s');

        DB::transaction(function () use ($catalog, $companyId, $enabled, $actorId, $ip, $now) {
            foreach ($catalog as $key => $service) {
                $scope = array('id_empresa' => $companyId, 'service_key' => $key);
                $values = array(
                    'enabled' => isset($enabled[$key]) ? 1 : 0,
                    'sort_order' => $service['order'],
                    'id_usuariomod' => $actorId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                );

                if (DB::table(self::TABLE)->where($scope)->exists()) {
                    DB::table(self::TABLE)->where($scope)->update($values);
                } else {
                    DB::table(self::TABLE)->insert(array_merge($scope, $values, array(
                        'id_usuariocrea' => $actorId,
                        'ip_creacion' => $ip,
                        'created_at' => $now,
                    )));
                }
            }
        });
    }
}
