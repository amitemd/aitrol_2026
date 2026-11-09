<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $table = 'v3_cost_centers';
    protected $guarded = array();

    const DEPARTMENTS = array(
        'INVENTARIO' => 'Inventario',
        'BODEGA' => 'Bodega',
        'LABORATORIO' => 'Laboratorio',
        'CLINICA' => 'Clínica y atención médica',
        'ADMINISTRACION' => 'Administración',
        'FINANZAS' => 'Finanzas y contabilidad',
        'TALENTO_HUMANO' => 'Talento humano',
        'MANTENIMIENTO' => 'Mantenimiento',
        'OTRO' => 'Otro',
    );

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_centro_padre');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'id_centro_padre');
    }

    public static function departmentLabel($department)
    {
        return isset(self::DEPARTMENTS[$department]) ? self::DEPARTMENTS[$department] : (string) $department;
    }
}
