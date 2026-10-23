<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Firma_Servicios_Generales extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'firma_servicios_generales';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function firma_usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuario');
    }
}