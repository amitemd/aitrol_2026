<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class RolesNomina extends Model
{
    private static $modelo = 'RolesNomina';
    protected $table = 'ct_rol_pagos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getRoles($r)
    {
        if ($r->id == '') {
            return RolesNomina::join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
                ->leftjoin('empresa as e', 'ct_rol_pagos.id_empresa', 'e.id')
                //->where('ct_rol_pagos.id_empresa', session('id_empresa'))

                ->where('anio', $r->periodo)
                ->where('mes', $r->mes)
                ->get([
                    'ct_rol_pagos.id',
                    'e.razonsocial'
                ]);
        } else {
            return RolesNomina::find($r->id);
        }
    }
}
