<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Rol extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_rol';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function ct_rol_pagos()
    {
        return $this->belongsTo('Sis_medico\Ct_Rol_Pagos','id_rol');
        
    }

}
