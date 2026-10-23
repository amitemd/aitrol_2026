<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotadeDebitoClientes extends Model
{
    private static $modelo = 'NotadeDebitoClientes';
    protected $table = 'ct_debito_clientes';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNotasdeDebitoClientes($r)
    {
           
        DB::enableQueryLog();
        if ($r->id_cliente == '') {
            return NotadeDebitoClientes::selectRaw(
                'ct_debito_clientes.fecha,
                ct_debito_clientes.numero,
                cli.nombre cliente,
                ct_debito_clientes.concepto,
                ct_debito_clientes.valor_contable,
                u.nombre1 as ucnombre,
                u.apellido1 as ucapellido,
                ct_debito_clientes.estado,
                (select sum(valor_contable) from ct_debito_clientes where ct_debito_clientes.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as total'
            )
                ->join('ct_clientes as cli', 'ct_debito_clientes.id_cliente', 'cli.identificacion')
                ->join('users as u', 'u.id', 'ct_debito_clientes.id_usuariocrea')
                ->where('ct_debito_clientes.id_empresa', session('id_empresa'))
                ->whereBetween('ct_debito_clientes.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get();
        } else {
            $id_cliente = explode('|', $r->id_cliente);
            return NotadeDebitoClientes::selectRaw(
                'ct_debito_clientes.fecha,
                ct_debito_clientes.numero,
                cli.nombre cliente,
                ct_debito_clientes.concepto,
                ct_debito_clientes.valor_contable,
                u.nombre1 as ucnombre,
                u.apellido1 as ucapellido,
                ct_debito_clientes.estado,
                (select sum(valor_contable) from ct_debito_clientes where ct_debito_clientes.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as total'
            )
                ->join('ct_clientes as cli', 'ct_debito_clientes.id_cliente', 'cli.identificacion')
                ->join('users as u', 'u.id', 'ct_debito_clientes.id_usuariocrea')
                ->where('ct_debito_clientes.id_empresa', session('id_empresa'))
                ->whereBetween('ct_debito_clientes.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->where('ct_debito_clientes.id_cliente', $id_cliente[0])
                ->get();
        }
    }
}
