<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_transportista extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transportistas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getTransportista($id)
    {
        return Ct_transportista::where('ci_ruc', $id)
            ->first();
    }
}
