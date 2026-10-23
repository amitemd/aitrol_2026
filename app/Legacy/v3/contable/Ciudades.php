<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ciudades extends Model
{
    private static $modelo = 'Ciudades';
    protected $table = 'new_db_ciudades';
    protected $primaryKey  = 'id_ciudad';
    const CREATED_AT = 'created_at_ciudad';
    const UPDATED_AT = 'updated_at_ciudad';

    public static function getCiudadCombo($r)
    {
        $result = '<option>--' . trans('v3/new_varios.seleccionar') . '--</option>';
        $ciudad = Ciudades::where('id_provincia_ciudad', $r->id_provincia)->get();
        if (count($ciudad) > 0) {
            foreach ($ciudad as $c) {
                $select = '';
                if ($r->id_ciudad != 0) {
                    if ($r->id_ciudad == $c->id_ciudad) {
                        $select = 'selected';
                    }
                }
                $result .= '<option value="' . $c->id_ciudad . '" ' . $select . '>' .  ucwords(strtolower(Utilidades::sanear_string_tildes($c->nombre_ciudad))) . '</option>';
            }
        }
        return $result;
    }

    public static function getCiudad($id)
    {
        return Ciudades::find($id);
    }
}
