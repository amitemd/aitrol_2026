<?php

namespace Aitrol\Configuracion\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class EmpresaModel extends Model
{
    protected $table = 'erp_empresas';

    protected $fillable = ['ruc', 'razon_social', 'nombre_comercial', 'email', 'telefono', 'direccion'];
}