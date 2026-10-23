<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientesRetenciones extends Model
{
    private static $modelo = 'ClientesRetenciones';
    protected $table = 'ct_cliente_retencion';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function detalle()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleClienteRetencion', "id_cliente_retencion");
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function usuariomodif()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function retencion()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Ventas', 'id_factura');
    }
}
