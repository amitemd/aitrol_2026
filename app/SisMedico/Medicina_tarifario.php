<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Medicina_tarifario extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medicina_tarifario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function ap_procedimiento()
    {
        return $this->belongsTo('Sis_medico\Ap_Procedimiento', 'id_ap_procedimiento');
    }
}
 