<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_address';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
