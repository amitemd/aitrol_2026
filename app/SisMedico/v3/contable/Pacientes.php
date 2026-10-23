<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Pacientes extends Model
{
    protected $table = 'paciente';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getPacienteCombo($term)
    {
        return Pacientes::selectRaw('CONCAT(paciente.id,"|",IFNULL(paciente.telefono1,""),"|",IFNULL(paciente.telefono2,""),"|",IFNULL(paciente.direccion,""),"|",IFNULL(u.email,"")) as id, CONCAT(paciente.id," | ",paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) as text')
            ->join('users as u', 'paciente.id', 'u.id')
            ->where(function ($q) use ($term) {
                $q->orWhere('paciente.nombre1', 'like', '%' . $term . '%');
                $q->orWhere('paciente.nombre2', 'like', '%' . $term . '%');
                $q->orWhere('paciente.apellido1', 'like', '%' . $term . '%');
                $q->orWhere('paciente.apellido2', 'like', '%' . $term . '%');
                $q->orWhereRaw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido1," ",paciente.nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido1," ",paciente.nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido2," ",paciente.nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido2," ",paciente.nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.nombre1," ",paciente.apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.nombre1," ",paciente.apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.nombre2," ",paciente.apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.nombre1," ",paciente.nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.nombre2," ",paciente.apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido1," ",paciente.apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(paciente.apellido2," ",paciente.apellido1) like "%' . $term . '%"');
                $q->orWhere('paciente.id', 'like', '%' . $term . '%');
            })->get();
    }
}
