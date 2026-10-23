<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Apps_Agenda extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps_agenda';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
