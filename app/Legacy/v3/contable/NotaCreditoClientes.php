<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoClientes extends Model
{
    private static $modelo = 'NotaCreditoClientes';
    protected $table = 'ct_nota_credito_clientes';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNotasCredito($id = '', $r)
    {
        if ($id == '') {
            $columns = ['id'];
            $query = self::where('id_empresa', session('id_empresa'));
            if ($r->input('search.value')) {
                $search = $r->input('search.value');
                $query->where(function ($q) use ($search, $columns) {
                    foreach ($columns as $column) {
                        if (in_array($column, ['users.id', 'users.nombre1', 'users.nombre2', 'users.apellido1', 'users.apellido2', 'users.direccion'])) {
                            $q->orWhereRaw("CONCAT(users.nombre1, ' ', users.nombre2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.id) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ', users.nombre2,' ',users.apellido1,' ',users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ',users.apellido1,' ',users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre2, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2,' ',users.nombre1,' ',users.nombre2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2,' ',users.nombre2,' ',users.nombre1) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido2, ' ', users.apellido1,' ',users.nombre1,' ',users.nombre2) LIKE ?", ["%{$search}%"]);
                        } else {
                            $q->orWhere($column, 'like', "%{$search}%");
                        }
                    }
                });
            }
            $totalFiltered = $query->count();
            if ($r->input('order.0.column') !== null) {
                $orderColumn = $columns[$r->input('order.0.column')];
                $orderDirection = $r->input('order.0.dir');
                $query->orderBy($orderColumn, $orderDirection);
            }
            $start = $r->input('start');
            $length = $r->input('length');
            $data = $query->offset($start)->limit($length)->get();
            return response()->json([
                'draw' => $r->input('draw'),
                'recordsTotal' => self::count(),
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        } else {
            return self::find($id);
        }
    }
}
