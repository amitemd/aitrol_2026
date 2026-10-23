<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Producto extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_producto';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function tipo()
    {
        return $this->belongsTo('Sis_medico\Hospital_Tipo','tipo_producto');
    }
    public function marcas()
    {
        return $this->belongsTo('Sis_medico\Hospital_Marca','id_marca');
    }
     public function evolucion_producto()
    {
        return $this->hasMany(App\evolucion_producto::class);
    }
}
 