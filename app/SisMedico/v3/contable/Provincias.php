<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\Utilidades;

class Provincias extends Model
{
    private static $modelo = 'Provincias';
    protected $table = 'new_db_provincias';
    protected $primaryKey  = 'id_provincia';
    const CREATED_AT = 'created_at_provincia';
    const UPDATED_AT = 'updated_at_provincia';

    public static function getProvinciaCombo($r)
    {
        $result = '<option>--'.trans('v3/new_varios.seleccionar').'--</option>';
        $provincia = Provincias::where('id_pais_provincia', $r->id_pais)->get();
        if (count($provincia) > 0) {
            foreach ($provincia as $p) {
                $select = '';
                if ($r->id_provincia != 0) {
                    if ($r->id_provincia == $p->id_provincia) {
                        $select = 'selected';
                    }
                }
                $result .= '<option value="' . $p->id_provincia . '" ' . $select . '>' . ucwords(strtolower(Utilidades::sanear_string_tildes($p->nombre_provincia))) . '</option>';
            }
        }
        return $result;
    }
}
