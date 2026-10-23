<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_doc_externos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_doc_externos';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}