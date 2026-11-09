<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class V3RoleMenuPermission extends Model
{
    protected $table = 'v3_role_menu_permissions';
    protected $fillable = array('id_empresa', 'id_tipo_usuario', 'id_menu', 'permitido', 'otorgado_por');
    protected $casts = array('permitido' => 'boolean');
}
