<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Devolucion_Productos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_devolucion_productos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\Ct_Cliente_Retencion', 'id', 'id_factura');
    }

    public function credito()
    {
        return $this->belongsTo('Sis_medico\Ct_Nota_Credito_Clientes', 'id_nota_credito', 'id');
    }

}
