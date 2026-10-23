<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LimpiezaArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'limpieza_area';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    public function encargado()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function productos()
    {
        return $this->hasMany('Sis_medico\ProductosUtilizadosArea', 'id_limpieza_area');
    }
    public function insumos()
    {
        return $this->hasMany('Sis_medico\Insumos_area_nuevo', 'id_limpieza_area');
    }
}
