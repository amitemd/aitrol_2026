<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class V3HumanResourcesAccountingService
{
    private $posting;
    public function __construct(V3AccountingPostingService $posting){$this->posting=$posting;}

    public function postAdvance($company,$advance,$ip)
    {
        $accounts=$this->accounts($company,array('ANTICIPOS_EMPLEADOS','BANCO_CAJA_RRHH'));$employee=DB::table('v3_hr_employees')->where('id',$advance->employee_id)->where('id_empresa',$company)->first();if(!$employee)throw new RuntimeException('El empleado del anticipo no pertenece a la empresa.');$description='Anticipo a '.$employee->apellidos.' '.$employee->nombres.' · '.$advance->concepto;$lines=array($this->line($accounts['ANTICIPOS_EMPLEADOS'],$description,$advance->monto,0,$advance->fecha_pago,$ip),$this->line($accounts['BANCO_CAJA_RRHH'],$description,0,$advance->monto,$advance->fecha_pago,$ip));
        return $this->posting->post($this->header($company,$advance->fecha_pago,$description,$advance->monto,'RRHH_ANTICIPO','ANT-'.$advance->id,$ip),$lines,array('modulo'=>'RRHH','tipo_origen'=>'ANTICIPO','id_origen'=>(string)$advance->id));
    }

    public function postLoan($company, $loan, $ip)
    {
        $accounts = $this->accounts($company, array('PRESTAMOS_EMPLEADOS', 'BANCO_CAJA_RRHH'));
        $employee = DB::table('v3_hr_employees')->where('id', $loan->employee_id)->where('id_empresa', $company)->first();
        if (!$employee) throw new RuntimeException('El empleado del préstamo no pertenece a la empresa.');
        $description = 'Préstamo empresarial sin interés a '.$employee->apellidos.' '.$employee->nombres.' · '.$loan->concepto;
        $lines = array(
            $this->line($accounts['PRESTAMOS_EMPLEADOS'], $description, $loan->monto, 0, $loan->fecha_desembolso, $ip),
            $this->line($accounts['BANCO_CAJA_RRHH'], $description, 0, $loan->monto, $loan->fecha_desembolso, $ip),
        );
        return $this->posting->post($this->header($company, $loan->fecha_desembolso, $description, $loan->monto, 'RRHH_PRESTAMO', 'PREST-'.$loan->id, $ip), $lines, array('modulo'=>'RRHH','tipo_origen'=>'PRESTAMO','id_origen'=>(string)$loan->id));
    }

    public function postBenefitPayment($company, $payment, $ip)
    {
        $map = array(
            'decimo_tercero' => 'DECIMO_TERCERO_POR_PAGAR',
            'decimo_cuarto' => 'DECIMO_CUARTO_POR_PAGAR',
            'fondo_reserva_iess' => 'FONDO_RESERVA_POR_PAGAR',
        );
        if (!isset($map[$payment->tipo])) throw new InvalidArgumentException('Tipo de beneficio no soportado.');
        $accounts = $this->accounts($company, array($map[$payment->tipo], 'BANCO_CAJA_RRHH'));
        $employee = DB::table('v3_hr_employees')->where('id', $payment->employee_id)->where('id_empresa', $company)->first();
        if (!$employee) throw new RuntimeException('El empleado del beneficio no pertenece a la empresa.');
        $description = 'Pago acumulado '.$payment->tipo.' · '.$employee->apellidos.' '.$employee->nombres;
        $lines = array(
            $this->line($accounts[$map[$payment->tipo]], $description, $payment->monto, 0, $payment->fecha_pago, $ip),
            $this->line($accounts['BANCO_CAJA_RRHH'], $description, 0, $payment->monto, $payment->fecha_pago, $ip),
        );
        return $this->posting->post($this->header($company, $payment->fecha_pago, $description, $payment->monto, 'RRHH_BENEFICIO', 'BEN-'.$payment->id, $ip), $lines, array('modulo'=>'RRHH','tipo_origen'=>'BENEFICIO','id_origen'=>(string)$payment->id));
    }

    public function postPayroll($company,$period,$ip)
    {
        if($period->estado!=='aprobado')throw new InvalidArgumentException('La nómina debe estar aprobada antes de contabilizar.');
        $required=array('SUELDOS_GASTO','IESS_PATRONAL_GASTO','DECIMO_TERCERO_GASTO','DECIMO_CUARTO_GASTO','VACACIONES_GASTO','FONDO_RESERVA_GASTO','IESS_POR_PAGAR','SUELDOS_POR_PAGAR','DECIMO_TERCERO_POR_PAGAR','DECIMO_CUARTO_POR_PAGAR','VACACIONES_POR_PAGAR','FONDO_RESERVA_POR_PAGAR','ANTICIPOS_EMPLEADOS','PRESTAMOS_EMPLEADOS','RETENCION_RENTA_POR_PAGAR','OTROS_DESCUENTOS_POR_PAGAR');$a=$this->accounts($company,$required);
        $totals=DB::table('v3_hr_payroll_lines')->where('id_empresa',$company)->where('period_id',$period->id)->selectRaw('COALESCE(SUM(ingresos),0) ingresos,COALESCE(SUM(aporte_iess),0) iess_personal,COALESCE(SUM(aporte_patronal),0) iess_patronal,COALESCE(SUM(provision_decimo_tercero),0) d13,COALESCE(SUM(decimo_tercero_pagado),0) d13_pagado,COALESCE(SUM(provision_decimo_cuarto),0) d14,COALESCE(SUM(decimo_cuarto_pagado),0) d14_pagado,COALESCE(SUM(provision_vacaciones),0) vacaciones,COALESCE(SUM(fondo_reserva),0) reserva,COALESCE(SUM(fondo_reserva_pagado),0) reserva_pagado,COALESCE(SUM(anticipos),0) anticipos,COALESCE(SUM(prestamos),0) prestamos,COALESCE(SUM(retencion_renta),0) renta,COALESCE(SUM(descuentos_conceptos),0) descuentos,COALESCE(SUM(neto),0) neto')->first();if(!$totals||(float)$totals->ingresos<=0)throw new RuntimeException('La nómina no contiene roles calculados.');
        $date=$period->fecha_hasta;$description='Nómina '.$period->periodo;$lines=array();$this->add($lines,$a['SUELDOS_GASTO'],$description,$totals->ingresos,0,$date,$ip);$this->add($lines,$a['IESS_PATRONAL_GASTO'],$description,$totals->iess_patronal,0,$date,$ip);$this->add($lines,$a['DECIMO_TERCERO_GASTO'],$description,$totals->d13,0,$date,$ip);$this->add($lines,$a['DECIMO_CUARTO_GASTO'],$description,$totals->d14,0,$date,$ip);$this->add($lines,$a['VACACIONES_GASTO'],$description,$totals->vacaciones,0,$date,$ip);$this->add($lines,$a['FONDO_RESERVA_GASTO'],$description,$totals->reserva,0,$date,$ip);$this->add($lines,$a['IESS_POR_PAGAR'],$description,0,$totals->iess_personal+$totals->iess_patronal,$date,$ip);$this->add($lines,$a['DECIMO_TERCERO_POR_PAGAR'],$description,0,$totals->d13-$totals->d13_pagado,$date,$ip);$this->add($lines,$a['DECIMO_CUARTO_POR_PAGAR'],$description,0,$totals->d14-$totals->d14_pagado,$date,$ip);$this->add($lines,$a['VACACIONES_POR_PAGAR'],$description,0,$totals->vacaciones,$date,$ip);$this->add($lines,$a['FONDO_RESERVA_POR_PAGAR'],$description,0,$totals->reserva-$totals->reserva_pagado,$date,$ip);$this->add($lines,$a['ANTICIPOS_EMPLEADOS'],$description,0,$totals->anticipos,$date,$ip);$this->add($lines,$a['PRESTAMOS_EMPLEADOS'],$description,0,$totals->prestamos,$date,$ip);$this->add($lines,$a['RETENCION_RENTA_POR_PAGAR'],$description,0,$totals->renta,$date,$ip);$this->add($lines,$a['OTROS_DESCUENTOS_POR_PAGAR'],$description,0,$totals->descuentos,$date,$ip);$this->add($lines,$a['SUELDOS_POR_PAGAR'],$description,0,$totals->neto,$date,$ip);
        $debit=round(array_sum(array_map(function($l){return(float)$l['debe'];},$lines)),2);$credit=round(array_sum(array_map(function($l){return(float)$l['haber'];},$lines)),2);if(abs($debit-$credit)>0.01)throw new RuntimeException('El asiento de nómina no cuadra: Debe '.$debit.' / Haber '.$credit.'.');
        return $this->posting->post($this->header($company,$date,$description,$debit,'RRHH_NOMINA','ROL-'.$period->periodo,$ip),$lines,array('modulo'=>'RRHH','tipo_origen'=>'NOMINA','id_origen'=>(string)$period->id));
    }

    private function accounts($company,$codes){$rows=DB::table('v3_hr_accounting_settings')->where('id_empresa',$company)->whereIn('codigo',$codes)->get()->keyBy('codigo');$result=array();$missing=array();foreach($codes as $code){$row=$rows->get($code);if(!$row||!$row->id_plan_cuenta){$missing[]=$code;continue;}$valid=DB::table('plan_cuentas_empresa')->where('id_empresa',$company)->where('id_plan',$row->id_plan_cuenta)->where('estado',2)->exists();if(!$valid)$missing[]=$code;else$result[$code]=$row->id_plan_cuenta;}if($missing)throw new RuntimeException('Configure cuentas contables RRHH: '.implode(', ',$missing).'.');return $result;}
    private function header($company,$date,$description,$value,$module,$reference,$ip){return array('id_empresa'=>$company,'fecha_asiento'=>$date.' 23:59:59','observacion'=>$description,'fact_numero'=>$reference,'valor'=>round($value,2),'modulo'=>$module,'aparece_sri'=>0,'especial'=>0,'tipo'=>1,'ip_creacion'=>$ip,'ip_modificacion'=>$ip,'id_usuariocrea'=>Auth::id(),'id_usuariomod'=>Auth::id());}
    private function line($account,$description,$debit,$credit,$date,$ip){return array('id_plan_cuenta'=>$account,'descripcion'=>$description,'fecha'=>$date.' 23:59:59','debe'=>round($debit,2),'haber'=>round($credit,2),'ip_creacion'=>$ip,'ip_modificacion'=>$ip);}
    private function add(&$lines,$account,$description,$debit,$credit,$date,$ip){if(round($debit+$credit,2)>0)$lines[]=$this->line($account,$description,$debit,$credit,$date,$ip);}
}
