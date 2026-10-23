<?php

namespace Sis_medico;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ct_Balances extends Model
{
    public static function GetBalance($idEmpresa, $desde, $hasta)
    {
        $sql = "
        select balance.plan,balance.nombre,IF(balance.nombre='(-) Capital Suscrito No Pagado',balance.debe,balance.debe) as debe,balance.haber,balance.saldo_deudor,balance.saldo_acreedor,balance.naturaleza,
        balance.naturaleza_2,balance.total,balance.tipo,balance.id_clase,balance.clase,balance.id_grupo,balance.grupo,balance.id_cuenta,
        balance.cuenta,balance.id_subcuenta,balance.subcuenta from (
        (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
        1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
        from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,1) and plan BETWEEN 1 AND 3)
        UNION ALL
        (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
        1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
        from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,4) and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
        UNION ALL
        (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
        1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
        from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
        UNION ALL
        (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
        1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
        from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,9) and plan!=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
        UNION ALL
        (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
        1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
        from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,10) and plan!=SUBSTRING(plan,1,9) and plan!=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
        UNION ALL
        (SELECT pe.plan,pe.nombre,SUM(debe) AS debe,SUM(haber) AS haber,
        IF(pe.naturaleza_2=1,SUM(IFNULL(haber,0)-IFNULL(debe,0)),0) AS saldo_acreedor,
        IF(pe.naturaleza_2=0,SUM(IFNULL(debe,0)-IFNULL(haber,0)),0) AS saldo_deudor,
        pe.naturaleza,pe.naturaleza_2,
        (IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) BETWEEN 1 AND 12 THEN (IF(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0)) AS total,
        IF(SUBSTRING(pe.plan,1,1)='1','ACTIVOS',IF(SUBSTRING(pe.plan,1,1)='2','PASIVOS','PATRIMONIO')) AS tipo,
        COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,1) AND id BETWEEN 1 AND 3 LIMIT 1),'') AS id_clase,
        COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,1) AND id BETWEEN 1 AND 3 LIMIT 1),'') AS clase,
        COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,4) LIMIT 1),'') AS id_grupo,
        COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,4) LIMIT 1),'') AS grupo,
        COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,7) LIMIT 1),'') AS id_cuenta,
        COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,7) LIMIT 1),'') AS cuenta,
        COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,10) LIMIT 1),(SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,9) LIMIT 1),'') AS id_subcuenta,
        COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,10) LIMIT 1),(SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,9) LIMIT 1),'') AS subcuenta
        FROM ct_asientos_detalle ad
        JOIN ct_asientos_cabecera ac ON ad.id_asiento_cabecera=ac.id
        LEFT JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa=ac.id_empresa
        LEFT JOIN plan_cuentas pc ON ad.id_plan_cuenta=pc.id
        WHERE ac.id_empresa=$idEmpresa
        AND ac.fecha_asiento<'$hasta'
        AND (pe.plan LIKE '1.%' OR pe.plan LIKE '2.%' OR pe.plan LIKE '3.%')
        AND pe.plan!='3.07.01'
        GROUP BY pe.plan)
        UNION ALL
        
        
        
        select imp_.plan,imp_.nombre,imp_.debe,imp_.haber,imp_.saldo_acreedor,(imp_.haber*-1),imp_.naturaleza,imp_.naturaleza_2,imp_.total,
        imp_.tipo,imp_.id_clase,imp_.clase,imp_.id_grupo,imp_.grupo,imp_.id_cuenta,imp_.cuenta,imp_.id_subcuenta,imp_.subcuenta
        from( 
        SELECT '2.01.07.01.11' as plan,'IMPUESTO A LA RENTA POR PAGAR' as nombre,0 as debe,ROUND(IFNULL(
        
        IF(regimen_especial=0,0,
        
        IF(regimen_especial=1,
        
        (
        select ROUND((sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1,2)
        
        -(select IF(Participacion.saldo>0,ROUND((Participacion.saldo)*0.15,2),0) as participacion from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta) as Participacion )
        
        +(select ROUND((sum(gastosnoDeclarados.debe)-sum(gastosnoDeclarados.haber))*-1,2) as utilidad from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '5.%')
        and ac.aparece_sri=0
        group by pe.plan
        
        ) as gastosnoDeclarados 
        )*-1 as utilidadGravable from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta )*porcentaje/100
        
        ,
        
        IF(regimen_especial=2,
        
        (Select ROUND(IFNULL(
        
        IF(regimen_especial=0,0,
        
        IF(regimen_especial=1,
        
        (
        select ROUND((sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1,2)
        
        -(select IF(Participacion.saldo>0,ROUND((Participacion.saldo)*0.15,2),0) as participacion from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta) as Participacion )
        
        +(select ROUND((sum(gastosnoDeclarados.debe)-sum(gastosnoDeclarados.haber))*-1,2) as utilidad from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '5.%')
        and ac.aparece_sri=0
        group by pe.plan
        
        ) as gastosnoDeclarados 
        )*-1 as utilidadGravable from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta )*(porcentaje/100)
        
        ,
        
        IF(regimen_especial=2,
        (
        select ROUND((select (((impuestoCausado.totalIngreso-inicio)*porcentaje)/100)+valor_fraccion from ct_rango_impuesto_sri where inicio<=impuestoCausado.totalIngreso AND fin>=impuestoCausado.totalIngreso limit 1 ),2) as impuestoCausado
        
        from (select (sum(IFNULL(ingreso.debe,0))-sum(IFNULL(ingreso.haber,0)))*-1 as totalIngreso from (select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '4.%')
        group by pe.plan) ingreso) as impuestoCausado
        
        
         )
        
        ,'No existe rango')
        
        )
        
        )
        
        ,0),2) as impuestoCausado from ct_porcentaje_renta where id_empresa=$idEmpresa and anio=SUBSTRING('$desde',1,4))
        
        ,'No existe rango')
        
        )
        
        )
        
        ,0),2) as haber,0 as saldo_acreedor,0 as saldo_deudor,1 as naturaleza,0 as naturaleza_2,0 as total,'PASIVO' as tipo,2 as id_clase,'PASIVOS' as clase,'2.01' as id_grupo,'PASIVOS CORRIENTES' as grupo,'2.01.07' as id_cuenta,'OTRAS OBLIGACIONES CORRIENTES'as cuenta,'2.01.07.01' as id_subcuenta,'CON LA ADMINISTRACION TRIBUTARIA'as subcuenta from ct_porcentaje_renta where id_empresa=$idEmpresa and anio=SUBSTRING('$desde',1,4)) as imp_
        
        
        
        
        UNION ALL
        
        select resul.plan,resul.nombre,resul.debe,resul.haber,resul.saldo_acreedor,resul.haber,resul.naturaleza,resul.naturaleza_2,resul.total,resul.tipo,
        resul.id_clase,resul.clase,resul.id_grupo,resul.grupo,resul.id_cuenta,resul.cuenta,resul.id_subcuenta,resul.subcuenta
        from (
        
        
        
        (select IF(linea.haber>0,'3.07.01','3.07.02') as plan,IF(linea.haber>0,'GANANCIA NETA DEL PERIODO','PERDIDA NETA DEL PERIODO') as nombre,
        IF(linea.haber>0,linea.haber*-1,0) as debe,
        IF(linea.haber<0,linea.haber,0) as haber,
        0 as saldo_acreedor,
        0 as saldo_deudor,
        1 as naturaleza,
        0 as naturaleza_2,
        0 as total,
        'PATRIMONIO' as tipo,
        3 as id_clase,
        'PATRIMONIO NETO' as clase,
        '3.07' as id_grupo, 
        'RESULTADOS DEL EJERCICIO' as grupo,
        IF(linea.haber>0,'3.07.01','3.07.02') as id_cuenta,IF(linea.haber>0,'Ganancia Neta Del Periodo','Perdida Neta Del Periodo') as cuenta,
        '-' as id_subcuenta,'-' as subcuenta
        from (
        select IF(utilidadNeta.utilidad>0,utilidadNeta.utilidad+(
        /*Impuesto Causado*/   
        Select IFNULL(
        
        IF(regimen_especial=0,0,
        
        IF(regimen_especial=1,
        
        (
        select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1
        
        -(select IF(Participacion.saldo>0,(Participacion.saldo)*0.15,0) as participacion from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta) as Participacion )
        
        +(select (sum(gastosnoDeclarados.debe)-sum(gastosnoDeclarados.haber))*-1 as utilidad from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '5.%')
        and ac.aparece_sri=0
        group by pe.plan
        
        ) as gastosnoDeclarados 
        )*-1 as utilidadGravable from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta )*porcentaje/100
        
        ,
        
        IF(regimen_especial=2,
        
        ((
        select ROUND((select (((impuestoCausado.totalIngreso-inicio)*porcentaje)/100)+valor_fraccion from ct_rango_impuesto_sri where inicio<=impuestoCausado.totalIngreso AND fin>=impuestoCausado.totalIngreso limit 1 ),2) as impuestoCausado
        
        from (select (sum(IFNULL(ingreso.debe,0))-sum(IFNULL(ingreso.haber,0)))*-1 as totalIngreso from (select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '4.%')
        group by pe.plan) ingreso) as impuestoCausado
        
        
         ))
        
        ,'No existe rango')
        
        )
        
        )
        
        ,0) as impuestoCausado from ct_porcentaje_renta where id_empresa=$idEmpresa and anio=SUBSTRING('$desde',1,4)
        /*Impuesto Causado*/
        ),utilidadNeta.utilidad-(
        /*Impuesto Causado*/   
        Select IFNULL(
        
        IF(regimen_especial=0,0,
        
        IF(regimen_especial=1,
        
        (
        select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1
        
        -(select IF(Participacion.saldo>0,(Participacion.saldo)*0.15,0) as participacion from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta) as Participacion )
        
        +(select (sum(gastosnoDeclarados.debe)-sum(gastosnoDeclarados.haber))*-1 as utilidad from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '5.%')
        and ac.aparece_sri=0
        group by pe.plan
        
        ) as gastosnoDeclarados 
        )*-1 as utilidadGravable from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta )*porcentaje/100
        
        ,
        
        IF(regimen_especial=2,
        
        ((
        select ROUND((select (((impuestoCausado.totalIngreso-inicio)*porcentaje)/100)+valor_fraccion from ct_rango_impuesto_sri where inicio<=impuestoCausado.totalIngreso AND fin>=impuestoCausado.totalIngreso limit 1 ),2) as impuestoCausado
        
        from (select (sum(IFNULL(ingreso.debe,0))-sum(IFNULL(ingreso.haber,0)))*-1 as totalIngreso from (select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '4.%')
        group by pe.plan) ingreso) as impuestoCausado
        
        
         ))
        
        ,'No existe rango')
        
        )
        
        )
        
        ,0) as impuestoCausado from ct_porcentaje_renta where id_empresa=$idEmpresa and anio=SUBSTRING('$desde',1,4)
        /*Impuesto Causado*/
        )) as haber from(
        select IF((sum(utilidadBruta.debe)-sum(utilidadBruta.haber))>0,
        
        ((sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1)-(select IF(Participacion.saldo>0,(Participacion.saldo)*0.15,0) as participacion from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        
        
        ) as utilidadBruta) as Participacion )
        
        ,(
        
        select IFNULL(sum(debe),0)-IFNULL(sum(haber),0) as perdida from(
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        ) as nuti
        
        )) as utilidad from (
        
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGRESO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta 
        ) as utilidadNeta
        ) as linea)
        
        
        
        
        
        
        ) as resul
        union ALL
        
        select par_.plan,par_.nombre,par_.debe,par_.haber,par_.saldo_acreedor,(par_.haber)*-1,par_.naturaleza,par_.naturaleza_2,par_.total,par_.tipo,par_.id_clase,par_.clase,par_.id_grupo,par_.grupo,par_.id_cuenta,par_.cuenta,
        par_.id_subgrupo,par_.subgrupo
        from (
        (select '2.01.07.05.01' as plan,'PARTICIPACION 15%' as nombre,0 as debe,IF(Participacion.saldo>0,ROUND((Participacion.saldo)*0.15,2),0) as haber,0 as saldo_acreedor,
        0 as saldo_deudor,1 as naturaleza, 0 as naturaleza_2,0 as total,'PASIVO' as tipo,2 as id_clase ,'PASIVOS' as clase,'2.01' as id_grupo, 'PARTICIPACION TRABAJADORES POR PAGAR DEL EJERCICIO' as grupo,
        '2.01.07' as id_cuenta,'OTRAS OBLIGACIONES CORRIENTES' as cuenta,'2.01.07.05' as id_subgrupo,'' as subgrupo
        
        
        from (select (sum(utilidadBruta.debe)-sum(utilidadBruta.haber))*-1 as saldo from (
        select pe.plan,pe.nombre,sum(debe) as debe,sum(haber) as haber,
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,(
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,pe.naturaleza,pe.naturaleza_2,IF(SUBSTRING(pe.plan,1,2)='4.','INGERSO',IF(SUBSTRING(pe.plan,1,3)='5.1','COSTO','GASTO')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$idEmpresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<'$hasta'
        
        and (pe.plan like '4.%' or pe.plan like '5.%')
        group by pe.plan
        
        ) as utilidadBruta) as Participacion ))as par_
        
        ) as balance order by balance.plan;";

        /*echo $sql;exit;*/
        $balance = DB::select($sql);

        return $balance;
    }
    public static function armarBalance($dato, $tipo)
    {
        $html = '';
        $total = 0;
        $saldo = 0;
        $activo = 0;
        $porcentaje = 100;
        $arrayGrupo = [];
        $totalLinea = 0;

        
        if (count($dato) > 0) {
            foreach ($dato as $key => $rowSubcuentas) {
                $contPuntos = explode('.', $rowSubcuentas->plan);
                if (count($contPuntos) == 5) {
                    $activo = (float)$rowSubcuentas->debe - (float)$rowSubcuentas->haber;
                    $totalLinea += $activo;

                    //$arrayGrupo[$rowSubcuentas->id_subcuenta] = (float) $saldo;
                    if ($rowSubcuentas->id_subcuenta != '') {
                        //array_push($arrayGrupo, $rowSubcuentas->id_subcuenta . '|' . $saldo);
                        $arrayGrupo[$rowSubcuentas->id_subcuenta] = (float) $totalLinea;
                        //$contPuntos1 = explode('.', $rowSubcuentas->id_subcuenta);
                        $contPuntos1 = explode('.', $dato[$key + 1]->plan);
                        //echo $rowSubcuentas->plan . ' | ' . $rowSubcuentas->id_subcuenta . ' | ' . $rowSubcuentas->nombre . ' | ' . $rowSubcuentas->debe . ' | ' . $rowSubcuentas->haber . ' | ' . count($contPuntos) . ' | ' . count($contPuntos1) . '<br/>';
                        if (count($contPuntos) != count($contPuntos1))
                            $totalLinea = 0;
                    }
                }
            }

            foreach ($dato as $row) {
                if ($row->id_clase == $tipo) {
                    $activo = (float)$row->debe - (float)$row->haber;
                    $saldo = $activo;
                    if ($tipo == 2) {
                        if ($row->nombre == 'IMPUESTO SRI POR PAGAR') {
                            $saldo = (float)$row->haber;
                            if ($saldo < 0)
                                $saldo = 0;
                        }
                    }
                    $total += $saldo;
                }
            }
            $class = '';
            foreach ($dato as $key => $row) {
                if ($row->id_clase == $tipo) {
                    $activo = (float)$row->debe - (float)$row->haber;
                    $saldo = $activo;
                    if ($tipo == 2) {
                        if ($row->nombre == 'IMPUESTO SRI POR PAGAR') {
                            $saldo = (float)$row->haber;
                            if ($saldo < 0)
                                $saldo = 0;
                        }
                    }
                    if (strlen($row->plan) == 1) {
                        $saldo = $total;
                        $class = 'style="font-weight: bold;"';
                    } else {
                        if ($saldo != 0)
                            $porcentaje = ($saldo * 100) / $total;
                        else
                            $porcentaje = 0;
                    }
                    if ($row->id_clase == 2 || $row->id_clase == 3) {
                        if ($saldo < 0) {
                            $saldo = number_format($saldo * -1, 2);
                            //$saldo = number_format($saldo, 2);
                        }
                    }
                    if ($saldo < 0) {
                        $saldo = $saldo * -1;
                        $saldo = '(' . number_format($saldo, 2) . ')';
                    }

                    if ($porcentaje < 0)
                        $porcentaje = $porcentaje * -1;
                    $sangria = '';
                    if (strlen($row->plan) == 4) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'style="font-weight: bold;"';
                    } elseif (strlen($row->plan) == 7) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'style="font-weight: bold;"';
                    } elseif (strlen($row->plan) == 9 || strlen($row->plan) == 10) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = '';
                    } elseif (strlen($row->plan) == 12 || strlen($row->plan) == 13)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';




                    foreach ($arrayGrupo as $text => $val) {
                        try {
                            if ($text == $row->plan) {
                                if ($row->id_clase == 2) {
                                    if ($val < 0) {
                                        $val = $val * -1;
                                    }

                                    $saldo = number_format($val, 2);
                                } else
                                    $saldo = number_format($val, 2);
                            }
                        } catch (Exception $e) {
                            $saldo = number_format($activo, 2);
                        }
                    }
                    $granArrayCuenta = [];
                    $contarray = 0;
                    foreach ($arrayGrupo as $tex => $val) {
                        $granArrayCuenta[$contarray] = [
                            'plan' => $tex,
                            'valor' => $val
                        ];
                        $contarray++;
                    }
                    $idCuenta = '';
                    $arrayCuenta = [];
                    foreach ($granArrayCuenta as $tex => $val) {
                        $idCuenta = substr((string)$val["plan"], 0, 7);
                        if (array_key_exists(substr((string)$val["plan"], 0, 7), $arrayCuenta)) {
                            $arrayCuenta[$idCuenta] += $val["valor"];
                        } else {
                            $arrayCuenta[$idCuenta] = $val["valor"];
                        }
                    }

                    foreach ($arrayCuenta as $tr => $vahu) {

                        try {
                            if ($tr  == $row->plan) {
                                if ($row->id_clase == 2) {
                                    if ($vahu < 0) {
                                        $vahu = $vahu * -1;
                                    }
                                    $saldo = number_format($vahu, 2);
                                } else
                                    $saldo = number_format($vahu, 2);
                            }
                        } catch (Exception $e) {
                            $saldo = number_format($activo, 2);
                        }
                    }
                    //dd($arrayCuenta);
                    $arrayCuencaSuperior = [];

                    foreach ($arrayCuenta as $tex => $val) {

                        $idCuenta = substr((string)$tex, 0, 4);
                        if (array_key_exists(substr((string)$tex, 0, 4), $arrayCuencaSuperior)) {
                            $arrayCuencaSuperior[$idCuenta] += $val;
                        } else {
                            $arrayCuencaSuperior[$idCuenta] = $val;
                        }
                    }

                    foreach ($arrayCuencaSuperior as $tr => $vahu) {

                        try {
                            if ($tr  == $row->plan) {
                                if ($row->id_clase == 2) {
                                    if ($vahu < 0) {
                                        $vahu = $vahu * -1;
                                    }
                                    $saldo = number_format($vahu, 2);
                                } else
                                    $saldo = number_format($vahu, 2);
                            }
                        } catch (Exception $e) {
                            $saldo = number_format($activo, 2);
                        }
                    }



                    if (is_numeric($saldo)) {
                        $saldo = number_format($saldo, 2);
                    }
                    if ($saldo != 0 || !is_numeric($saldo)) {
                        $html .= '<tr ' . $class . '>
                            <td class="redireccionar" onclick="redireccionar(\'' . $row->plan . '\');">' . $sangria . $row->plan . '</td>
                            <td class="redireccionar" onclick="redireccionar(\'' . $row->plan . '\');">' . $sangria . $row->nombre . '</td>
                            <td align="right">$ ' . $saldo . '</td>
                            <td align="right" style="font-size:9px;">' . number_format($porcentaje, 2) . ' %</td>
                            </tr>';
                        $sangria = '';
                        $class = '';
                    }
                }
            }
        }
        return $html . '|' . $total;
    }
}
