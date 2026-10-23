<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class InvTrasladosBodegas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_traslados_bodegas';

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
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\InvCabMovimientos', 'id_inv_cab_mov_origen', 'id');
    }
    public function cabecera2()
    {
        return $this->belongsTo('Sis_medico\InvCabMovimientos', 'id_inv_cab_movientos', 'id');
    }
}