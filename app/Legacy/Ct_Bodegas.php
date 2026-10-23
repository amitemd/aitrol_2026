<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Bodegas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_bodegas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

}
