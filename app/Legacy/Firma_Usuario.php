<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Firma_Usuario extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'firma_usuario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_usuario');
    }
}
