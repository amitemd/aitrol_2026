<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class V3ActionPermission extends Model
{
    protected $table = 'v3_action_permissions';
    protected $guarded = array('id');
}
