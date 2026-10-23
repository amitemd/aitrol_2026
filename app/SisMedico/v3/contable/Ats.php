<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Ats extends Model
{
    private static $modelo = 'Ats';
    protected $table = 'ct_ats';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getAts($id)
    {
        return self::join('empresa as e', 'ct_ats.id_empresa', 'e.id')
            ->where('ct_ats.id', $id)
            ->first([
                'ct_ats.*',
                'e.razonsocial',
            ]);
    }
    public static function getAtsGenerados($r)
    {
        $datos = [];
        try {
            $datos = self::join('users as u', 'ct_ats.id_usuariocrea', 'u.id')
                ->where(function ($q) use ($r) {
                    if (isset($r->anio) && $r->anio != '' && isset($r->mes) && $r->mes != '') {
                        $q->where(DB::raw('SUBSTRING(ct_ats.periodo, 1, 4)'), $r->anio)
                            ->where(DB::raw('SUBSTRING(ct_ats.periodo, 6, 2)'), str_pad($r->mes, 2, 0, STR_PAD_LEFT))->get();
                    } elseif (isset($r->checkSemestral) && $r->checkSemestral != '') {
                        $tr = explode("-", $r->reservation);
                        $start_date = trim($tr[0]);
                        $end_date = trim($tr[1]);
                        $q->where(DB::raw('CONCAT(SUBSTRING(ct_ats.periodo, 1, 4), "-", SUBSTRING(ct_ats.periodo, 6, 2), "-01")'), '>=', DB::raw("STR_TO_DATE('$start_date', '%d/%m/%Y')"))
                            ->where(DB::raw('CONCAT(SUBSTRING(ct_ats.periodo, 1, 4), "-", SUBSTRING(ct_ats.periodo, 6, 2), "-01")'), '<=', DB::raw("STR_TO_DATE('$end_date', '%d/%m/%Y')"));
                    }
                })
                ->where('ct_ats.estado', 1)
                ->where('ct_ats.id_empresa', session('id_empresa'))
                ->get([
                    'ct_ats.*',
                    DB::raw('CONCAT(SUBSTRING(u.nombre1, 1, 1), ". ", u.apellido1) AS usuariocrea'),
                ]);
        } catch (Exception $r) {
            dd($r);
        }

        return $datos;
    }
}
