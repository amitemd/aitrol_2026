<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Bodegas_Nevera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bodegas_nevera';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
}
