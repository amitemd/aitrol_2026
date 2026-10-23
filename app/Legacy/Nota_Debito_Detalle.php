<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_Debito_Detalle extends Model
{
    //
    protected $table = 'ct_nota_debito_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Nota_Debito', 'id_nota_debito');
    }

}
