<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PorcentajeRenta extends Model
{
    protected $table = 'ct_porcentaje_renta';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    protected $keyType = 'string';

    public static function impuestoCausado($renta, $desde, $hasta, $utilidadBruta, $participacion, $ingresos, $nodeducible = 0)
    {
        $impuestoCalculado = 0;
        if (!is_object($renta) || !isset($renta->regimen_especial)) {
            return 0;
        }
        if ($renta->regimen_especial == 0) {
        } elseif ($renta->regimen_especial == 1) {
            $impuestoCalculado = ($nodeducible * $renta->porcentaje) / 100;
        } elseif ($renta->regimen_especial == 2) {
            if ($ingresos < 0) {
                $ingresos = $ingresos * -1;
            }
            if ($ingresos >= 0.00 && $ingresos <= 20000.00) {
                $porcentaje = 0;
                $impuesto = 60;
                $impuestoCalculado = $impuesto;
            } elseif ($ingresos >= 20000.01 && $ingresos <= 50000.00) {
                $porcentaje = 1;
                $impuesto = 60;
                $diferencia = $ingresos - 20000.01;
                $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
            } elseif ($ingresos >= 50000.01 && $ingresos <= 75000.00) {
                $porcentaje = 1.25;
                $impuesto = 360;
                $diferencia = $ingresos - 50000.01;
                $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
            } elseif ($ingresos >= 75000.01 && $ingresos <= 100000.00) {
                $porcentaje = 1.50;
                $impuesto = 672.50;
                $diferencia = $ingresos - 70000.01;
                $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
            } elseif ($ingresos >= 100000.01 && $ingresos <= 200000.00) {
                $porcentaje = 1.75;
                $impuesto = 1047.50;
                $diferencia = $ingresos - 100000.01;
                $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
            } elseif ($ingresos >= 200000.01 && $ingresos > 300000.00) {
                $porcentaje = 2;
                $impuesto = 2797.52;
                $diferencia = $ingresos - 200000.01;
                $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
            }
        }
        return $impuestoCalculado;
    }
    public static function getDatoRentaAnual($periodo)
    {
        return PorcentajeRenta::where('anio', $periodo)
            ->where('id_empresa', session('id_empresa'))
            ->first();
    }
    public static function infoTributarioEmpresa($id)
    {
        return DeEmpresas::leftjoin('ct_porcentaje_renta as p', 'de_empresa.id_empresa', 'p.id_empresa')
            ->where('de_empresa.id_empresa', $id)->get([
                DB::raw('IF(IFNULL(de_empresa.tipo_contribuyente,0)=0,"Persona natural","Persona juridica") as tipo_contribuyente'),
                DB::raw('IF(IFNULL(de_empresa.agente_retencion,0)=0,"No","Si") as agente_retencion'),
                'de_empresa.contribuyente_especial',
                DB::raw('IF(IFNULL(de_empresa.ambiente,0)=1,"Pruebas","Producción") as ambiente'),
                DB::raw('IF(IFNULL(de_empresa.contabilidad,0)=1,"Si","No") as contabilidad'),
                DB::raw('if(IFNULL(p.regimen_especial,0)=0,"Rimpe popular",IF(IFNULL(p.regimen_especial,0)=1,"Regimen general","Rimpe emprendedor")) as tipo_rimpe'),
                'p.anio'
            ]);
    }
}
