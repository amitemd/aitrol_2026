<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Inventario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_inventario';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }

    public function bodegas()
    {
        return $this->belongsTo('Sis_medico\Ct_Bodegas','id');
    }

}
