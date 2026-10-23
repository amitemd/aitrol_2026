<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Mantenimientos_Banos extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mantenimientos_banos';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo('Sis_medico\User', 'responsable_anest');
    }
    

}