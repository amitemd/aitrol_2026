<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class InvTipoMovimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_tipo_movimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = []; 

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    
    
}