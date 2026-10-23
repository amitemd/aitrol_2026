<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UserMembresia extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_membresia';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function membresia()
    {
        return $this->belongsTo('Sis_medico\Membresia', 'membresia_id');
    }
}
