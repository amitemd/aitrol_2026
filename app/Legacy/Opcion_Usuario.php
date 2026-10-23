<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Opcion_Usuario extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'opcion_usuario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function opcion()
    {
        return $this->belongsTo('Sis_medico\Opcion','id_opcion');
    }



}
 