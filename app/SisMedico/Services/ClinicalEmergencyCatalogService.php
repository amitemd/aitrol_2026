<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalEmergencyCatalogService
{
    /**
     * Completa los catalogos clinicos minimos requeridos por el triaje.
     * Solo inserta valores activos que todavia no existen.
     */
    public function ensure($userId = null, $ip = '127.0.0.1')
    {
        $tables = array('ho_tipo_emergencia', 'ho_prioridad_emergencia', 'ho_glasgow');
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                return array('ready' => false, 'inserted' => 0);
            }
        }

        $actor = $this->resolveActor($userId);
        if (is_null($actor)) {
            return array('ready' => false, 'inserted' => 0);
        }

        $inserted = 0;

        foreach ($this->emergencyTypes() as $name) {
            $exists = DB::table('ho_tipo_emergencia')
                ->where('estado', 1)
                ->whereRaw('UPPER(nombre) = ?', array($this->upper($name)))
                ->exists();

            if (!$exists) {
                DB::table('ho_tipo_emergencia')->insert($this->audit(array(
                    'nombre' => $name,
                    'estado' => 1,
                ), $actor, $ip));
                $inserted++;
            }
        }

        foreach ($this->priorities() as $priority) {
            $exists = DB::table('ho_prioridad_emergencia')
                ->where('estado', 1)
                ->where('prioridad', $priority['prioridad'])
                ->exists();

            if (!$exists) {
                DB::table('ho_prioridad_emergencia')->insert($this->audit($priority + array(
                    'estado' => 1,
                ), $actor, $ip));
                $inserted++;
            }
        }

        foreach ($this->glasgowResponses() as $response) {
            $exists = DB::table('ho_glasgow')
                ->where('estado', 1)
                ->where('tipo', $response['tipo'])
                ->where('prioridad', $response['prioridad'])
                ->exists();

            if (!$exists) {
                DB::table('ho_glasgow')->insert($this->audit($response + array(
                    'estado' => 1,
                ), $actor, $ip));
                $inserted++;
            }
        }

        return array('ready' => true, 'inserted' => $inserted);
    }

    private function resolveActor($userId)
    {
        if (!empty($userId) && DB::table('users')->where('id', $userId)->exists()) {
            return $userId;
        }

        $systems = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)->orderBy('id')->first();
        if (!is_null($systems)) {
            return $systems->id;
        }

        $user = DB::table('users')->orderBy('id')->first();
        return is_null($user) ? null : $user->id;
    }

    private function audit(array $values, $actor, $ip)
    {
        $now = date('Y-m-d H:i:s');
        return $values + array(
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        );
    }

    private function emergencyTypes()
    {
        return array(
            'EMERGENCIA MEDICA',
            'EMERGENCIA TRAUMATICA',
            'EMERGENCIA QUIRURGICA',
            'EMERGENCIA OBSTETRICA',
            'EMERGENCIA PEDIATRICA',
        );
    }

    private function priorities()
    {
        return array(
            array('nombre' => 'ROJO - INMEDIATO', 'prioridad' => 1, 'color' => '#FF0800'),
            array('nombre' => 'NARANJA - MUY URGENTE', 'prioridad' => 2, 'color' => '#FF6C00'),
            array('nombre' => 'AMARILLO - URGENTE', 'prioridad' => 3, 'color' => '#FFE400'),
            array('nombre' => 'VERDE - ESTANDAR', 'prioridad' => 4, 'color' => '#0DD119'),
            array('nombre' => 'AZUL - NO URGENTE', 'prioridad' => 5, 'color' => '#1F57A9'),
        );
    }

    private function glasgowResponses()
    {
        return array(
            array('tipo' => 1, 'prioridad' => '4', 'nombre' => 'ESPONTANEA'),
            array('tipo' => 1, 'prioridad' => '3', 'nombre' => 'AL HABLA'),
            array('tipo' => 1, 'prioridad' => '2', 'nombre' => 'AL DOLOR'),
            array('tipo' => 1, 'prioridad' => '1', 'nombre' => 'NINGUNA'),
            array('tipo' => 2, 'prioridad' => '5', 'nombre' => 'ORIENTADA'),
            array('tipo' => 2, 'prioridad' => '4', 'nombre' => 'CONFUSA'),
            array('tipo' => 2, 'prioridad' => '3', 'nombre' => 'PALABRAS INAPROPIADAS'),
            array('tipo' => 2, 'prioridad' => '2', 'nombre' => 'SONIDOS INCOMPRENSIBLES'),
            array('tipo' => 2, 'prioridad' => '1', 'nombre' => 'NINGUNA'),
            array('tipo' => 3, 'prioridad' => '6', 'nombre' => 'OBEDECE ORDENES'),
            array('tipo' => 3, 'prioridad' => '5', 'nombre' => 'LOCALIZA EL DOLOR'),
            array('tipo' => 3, 'prioridad' => '4', 'nombre' => 'RETIRADA AL DOLOR'),
            array('tipo' => 3, 'prioridad' => '3', 'nombre' => 'FLEXION ANORMAL'),
            array('tipo' => 3, 'prioridad' => '2', 'nombre' => 'EXTENSION ANORMAL'),
            array('tipo' => 3, 'prioridad' => '1', 'nombre' => 'NINGUNA'),
        );
    }

    private function upper($value)
    {
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
    }
}
