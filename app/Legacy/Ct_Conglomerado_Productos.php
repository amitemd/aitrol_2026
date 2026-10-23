<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Conglomerado_Productos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_conglomerado_productos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proveedor(){
        
        return $this->belongsTo('Sis_medico\Ct_Nota_Inventario', 'id_inventario');
    }

}
