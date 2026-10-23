<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Saldos_Producto extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_saldos_producto';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto','id');
    }


}
