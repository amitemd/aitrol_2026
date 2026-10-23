<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Modulos extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modulos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function modulo_bodega()
    {
        $id_empresa = Session::get('id_empresa');
        return $this->hasMany('Sis_medico\ModuloBodega', 'id_modulo')->where('id_empresa', $id_empresa);
    }

}
