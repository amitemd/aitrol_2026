<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Sucursales extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_sucursales';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cajas()
    {
        return $this->hasMany('Sis_medico\Ct_Caja', 'id_sucursal', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa', 'id');
    }

}
