<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_configuraciones_pdf extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_configuraciones_pdf';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
