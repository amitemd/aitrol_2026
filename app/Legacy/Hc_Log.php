<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_Log extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_log';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   
}
