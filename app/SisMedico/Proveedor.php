<?php

namespace Sis_medico;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proveedor';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function acreedor()
    {
        return $this->belongsTo('Sis_medico\Ct_Debito_bancario', 'id_acreedor');
    }
    public function compras()
    {
        return $this->hasMany('Sis_medico\Ct_compras', 'proveedor', 'id');
    }
    public static function getProveedor($id)
    {
        return Proveedor::find($id);
    }
    public static function buscarProveedor($term)
    {
        return Proveedor::selectRaw('id,razonsocial as text')
            ->where(function ($q) use ($term) {
                $q->where('razonsocial', 'LIKE', '%' . $term . '%')
                    ->orWhere('nombrecomercial', 'LIKE', '%' . $term . '%')
                    ->orWhere('id', 'LIKE', '%' . $term . '%');
            })
            ->where('estado', 1)
            ->get();
    }



    public static function getCuentasPagadas(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'id_empresa'  => 'required',
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msj' => $validator->errors()->first(),
            ], 422);
        }
        $empresa = $request->input('id_empresa');
        $proveedor = $request->input('proveedor');
        $filtrarProveedor = $proveedor !== null && $proveedor !== '';
        $excluirGrupoRobles = filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN);
        $rucsGrupoRobles = collect($request->input('rucs_grupo_robles', []))
            ->filter(function ($ruc) {
                return is_scalar($ruc) && trim((string) $ruc) !== '';
            })
            ->map(function ($ruc) {
                return trim((string) $ruc);
            })
            ->unique()
            ->values()
            ->all();
        $desde = $request->input('fecha_desde');
        $hasta = Carbon::parse($request->input('fecha_hasta'))
            ->addDay()->toDateString();
        $cuentaCajaGeneral = '1.01.01.01.01';

        $base = function ($tabla, $alias, $campoFecha) use (
            $empresa,
            $desde,
            $hasta
        ) {
            return DB::table($tabla . ' as ' . $alias)
                ->where($alias . '.id_empresa', $empresa)
                ->where($alias . '.estado', 1)
                ->where($alias . '.' . $campoFecha, '>=', $desde)
                ->where($alias . '.' . $campoFecha, '<', $hasta);
        };

        $egresos = $base('ct_comprobante_egreso', 'e', 'fecha_comprobante')
            ->leftJoin('proveedor as p', 'p.id', '=', 'e.id_proveedor')
            ->whereNotNull('e.id_proveedor')
            ->where('e.valor_pago', '>', 0)
            ->whereRaw("NOT EXISTS (
                SELECT 1
                FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = e.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->whereRaw("NOT EXISTS (
                SELECT 1
                FROM ct_debito_bancario b
                WHERE b.id = e.id_referencia
                  AND b.id_empresa = e.id_empresa
                  AND b.estado = 1
            )")
            ->select(
                'e.id',
                'e.fecha_comprobante as fecha',
                'e.secuencia as numero_documento',
                'e.id_proveedor',
                DB::raw('COALESCE(p.razonsocial, e.beneficiario) as proveedor'),
                DB::raw("'EGRESO' as documento"),
                DB::raw("
                CASE WHEN EXISTS (
                    SELECT 1 FROM ct_detalle_comprobante_egreso d
                    WHERE d.id_comprobante = e.id
                      AND d.estado = 1
                      AND d.id_compra IS NOT NULL
                )
                THEN 'CON FACTURA ASOCIADA'
                ELSE 'ANTICIPO SIN FACTURA'
                END as categoria
            "),
                DB::raw('e.valor_pago as dinero_pagado'),
                DB::raw("
                (SELECT COALESCE(SUM(d.abono), 0)
                 FROM ct_detalle_comprobante_egreso d
                 WHERE d.id_comprobante = e.id
                   AND d.estado = 1
                   AND d.id_compra IS NOT NULL) as aplicado_facturas
            ")
            );

        if ($filtrarProveedor) {
            $egresos->where('e.id_proveedor', $proveedor);
        }
        if ($excluirGrupoRobles && !empty($rucsGrupoRobles)) {
            $egresos->whereNotIn('e.id_proveedor', $rucsGrupoRobles);
        }

        $masivos = $base(
            'ct_comprobante_egreso_masivo',
            'm',
            'fecha_comprobante'
        )
            ->join('ct_detalle_comprobante_egreso_masivo as d', function ($join) {
                $join->on('d.id_comprobante', '=', 'm.id')
                    ->where('d.estado', '=', 1);
            })
            ->leftJoin('proveedor as p', 'p.id', '=', 'd.id_proveedor')
            ->whereNotNull('d.id_proveedor')
            ->where('m.valor_pago', '>', 0)
            ->whereRaw("NOT EXISTS (
                SELECT 1
                FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = m.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->select(
                'm.id',
                'm.fecha_comprobante as fecha',
                'm.secuencia as numero_documento',
                'd.id_proveedor',
                'p.razonsocial as proveedor',
                DB::raw("'EGRESO MASIVO' as documento"),
                DB::raw("
                CASE WHEN SUM(
                    CASE WHEN d.id_compra IS NOT NULL THEN 1 ELSE 0 END
                ) > 0
                THEN 'CON FACTURA ASOCIADA'
                ELSE 'ANTICIPO SIN FACTURA'
                END as categoria
            "),
                DB::raw('SUM(d.abono) as dinero_pagado'),
                DB::raw("
                SUM(CASE WHEN d.id_compra IS NOT NULL
                    THEN d.abono ELSE 0 END) as aplicado_facturas
            ")
            )
            ->groupBy(
                'm.id',
                'm.fecha_comprobante',
                'm.secuencia',
                'd.id_proveedor',
                'p.razonsocial'
            );

        if ($filtrarProveedor) {
            $masivos->where('d.id_proveedor', $proveedor);
        }
        if ($excluirGrupoRobles && !empty($rucsGrupoRobles)) {
            $masivos->whereNotIn('d.id_proveedor', $rucsGrupoRobles);
        }

        $debitos = $base('ct_debito_bancario', 'b', 'fecha')
            ->leftJoin('proveedor as p', 'p.id', '=', 'b.id_acreedor')
            ->whereNotNull('b.id_acreedor')
            ->where('b.valor', '>', 0)
            ->whereRaw("NOT EXISTS (
                SELECT 1
                FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = b.id_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->select(
                'b.id',
                'b.fecha',
                'b.secuencia as numero_documento',
                'b.id_acreedor as id_proveedor',
                'p.razonsocial as proveedor',
                DB::raw("'DEBITO BANCARIO' as documento"),
                DB::raw("
                CASE WHEN EXISTS (
                    SELECT 1 FROM ct_debito_bancario_detalle d
                    WHERE d.id_debito = b.id
                      AND d.estado = 1
                      AND d.id_compra IS NOT NULL
                )
                THEN 'CON FACTURA ASOCIADA'
                ELSE 'ANTICIPO SIN FACTURA'
                END as categoria
            "),
                DB::raw('b.valor as dinero_pagado'),
                DB::raw("
                (SELECT COALESCE(SUM(d.abono), 0)
                 FROM ct_debito_bancario_detalle d
                 WHERE d.id_debito = b.id
                   AND d.estado = 1
                   AND d.id_compra IS NOT NULL) as aplicado_facturas
            ")
            );

        if ($filtrarProveedor) {
            $debitos->where('b.id_acreedor', $proveedor);
        }
        if ($excluirGrupoRobles && !empty($rucsGrupoRobles)) {
            $debitos->whereNotIn('b.id_acreedor', $rucsGrupoRobles);
        }

        $varios = $base(
            'ct_comprobante_egreso_varios',
            'v',
            'fecha_comprobante'
        )
            ->leftJoin('proveedor as p', 'p.id', '=', 'v.id_proveedor')
            ->where('v.valor', '>', 0)
            ->whereRaw("NOT EXISTS (
                SELECT 1
                FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = v.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->select(
                'v.id',
                'v.fecha_comprobante as fecha',
                'v.secuencia as numero_documento',
                'v.id_proveedor',
                DB::raw("
                COALESCE(p.razonsocial,
                         NULLIF(TRIM(v.beneficiario), ''),
                         'SIN BENEFICIARIO') as proveedor
            "),
                DB::raw("'EGRESO VARIOS' as documento"),
                DB::raw("'EGRESO VARIOS' as categoria"),
                DB::raw('v.valor as dinero_pagado'),
                DB::raw('0 as aplicado_facturas')
            );

        if ($filtrarProveedor) {
            $varios->where('v.id_proveedor', $proveedor);
        }
        if ($excluirGrupoRobles && !empty($rucsGrupoRobles)) {
            $varios->where(function ($query) use ($rucsGrupoRobles) {
                $query->whereNull('v.id_proveedor')
                    ->orWhereNotIn('v.id_proveedor', $rucsGrupoRobles);
            });
        }

        $registros = $egresos->get()
            ->merge($masivos->get())
            ->merge($debitos->get())
            ->merge($varios->get())
            ->sortByDesc('fecha')
            ->values();

        return response()->json([
            'total_egresado' => round($registros->sum('dinero_pagado'), 2),
            'total_proveedores' => round(
                $registros->where('documento', '!=', 'EGRESO VARIOS')
                    ->sum('dinero_pagado'),
                2
            ),
            'total_egresos_varios' => round(
                $registros->where('documento', 'EGRESO VARIOS')
                    ->sum('dinero_pagado'),
                2
            ),
            'registros' => $registros,
        ]);
    }
}
