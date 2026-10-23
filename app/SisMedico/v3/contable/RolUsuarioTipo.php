<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class RolUsuarioTipo extends Model
{
    private static $modelo = 'RolUsuarioTipo';
    protected $table = 'roles_usuario';
    protected $primaryKey  = 'id_rol';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function buscarRecaudador($term)
    {
        return self::selectRaw('u.id as id, CONCAT(u.id," ",nombre1," ",nombre2," ",apellido1," ",apellido2) as text')
            ->join('users as u', 'roles_usuario.id_usuario', 'u.id')
            ->where(function ($q) use ($term) {
                $q->orWhere('u.nombre1', 'like', '%' . $term . '%');
                $q->orWhere('u.nombre2', 'like', '%' . $term . '%');
                $q->orWhere('u.apellido1', 'like', '%' . $term . '%');
                $q->orWhere('u.apellido2', 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre1, ' ', u.nombre2)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre1, ' ', u.nombre2,' ',u.apellido1)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre1, ' ', u.nombre2,' ',u.apellido2)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre2, ' ', u.nombre1,' ',u.apellido1)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre2, ' ', u.nombre1,' ',u.apellido2)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre1, ' ', u.nombre2,' ',u.apellido1,' ',u.apellido2)", 'like', '%' . $term . '%');
                $q->orWhereRaw("CONCAT(u.nombre1, ' ', u.nombre2,' ',u.apellido2,' ',u.apellido1)", 'like', '%' . $term . '%');
                $q->orWhere('u.id', 'like', '%' . $term . '%');
            })->get();
    }
}
