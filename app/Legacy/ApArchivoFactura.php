<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ApArchivoFactura extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_archivo_factura';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    
    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

}