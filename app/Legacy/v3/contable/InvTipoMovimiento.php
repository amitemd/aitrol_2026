<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class InvTipoMovimiento extends Model
{
        protected $table = 'inv_tipo_movimiento';

        const CREATED_AT = 'created_at';
        const UPDATED_AT = 'updated_at';
        protected $keyType = 'string';

}
