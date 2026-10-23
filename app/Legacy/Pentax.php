<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Pentax extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pentax';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function procedimientos()
    {
        return $this->hasMany('Sis_medico\PentaxProc','id_pentax');
    }
}
