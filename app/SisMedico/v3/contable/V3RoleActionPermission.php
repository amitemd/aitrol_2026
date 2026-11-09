<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class V3RoleActionPermission extends Model
{
    protected $table = 'v3_role_action_permissions';
    protected $fillable = array('id_empresa', 'id_tipo_usuario', 'modulo', 'accion', 'permitido', 'otorgado_por');
    protected $casts = array('permitido' => 'boolean');
}
