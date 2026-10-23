<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    private static $modelo = 'equipo';
    protected $table = 'equipo';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getEquipoCombo($term)
    {
        return Equipo::select('id', 'nombre as text')
            ->where('estado',1)
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get();
    }
    public static function getEquipos($id = '')
    {
        if ($id == '') {
            return Equipo::where('estado',1)->get();
        } else {
            return Equipo::find($id);
        }
    }
}
