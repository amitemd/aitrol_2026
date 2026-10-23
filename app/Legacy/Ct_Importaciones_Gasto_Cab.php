<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Importaciones_Gasto_Cab extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_importaciones_gasto_cab';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function compra()
    {
        return $this->belongsTo('Sis_medico\Ct_Importaciones_Compras','id_import_compra');
    }

    public function detalle_compra()
    {
        return $this->hasMany('Sis_medico\Ct_Importaciones_Detalle_Compra','id_ct_compras','id_import_compra');
    }

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Importaciones_Cab','id_import_cabl');
    }
    public function ct_compra()
    {
        return $this->belongsTo('Sis_medico\Ct_compras','id_ct_compra');
    }
    

}
 
 