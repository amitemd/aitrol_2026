<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_Apps extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_apps';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
