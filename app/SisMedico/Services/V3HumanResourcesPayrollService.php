<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class V3HumanResourcesPayrollService
{
    public function generate($company, $period)
    {
        $parameters = array(
            'iess_personal' => $this->parameter($company, 'IESS_PERSONAL', $period->fecha_hasta, 9.45),
            'iess_patronal' => $this->parameter($company, 'IESS_PATRONAL', $period->fecha_hasta, 11.15),
            'sbu' => $this->parameter($company, 'SBU', $period->fecha_hasta, 482),
            'horas_mes' => $this->parameter($company, 'HORAS_MES', $period->fecha_hasta, 240),
            'recargo_nocturno' => $this->parameter($company, 'RECARGO_NOCTURNO', $period->fecha_hasta, 25),
            'd13_factor' => $this->parameter($company, 'DECIMO_TERCERO_FACTOR', $period->fecha_hasta, 8.333333),
            'd14_factor' => $this->parameter($company, 'DECIMO_CUARTO_FACTOR', $period->fecha_hasta, 8.333333),
            'vacaciones_factor' => $this->parameter($company, 'VACACIONES_FACTOR', $period->fecha_hasta, 4.166667),
            'vacaciones_dias' => $this->parameter($company, 'VACACIONES_DIAS', $period->fecha_hasta, 15),
            'vacaciones_adicionales_max' => $this->parameter($company, 'VACACIONES_ADICIONALES_MAX', $period->fecha_hasta, 15),
            'reserva_factor' => $this->parameter($company, 'FONDO_RESERVA_PCT', $period->fecha_hasta, 8.333333),
        );

        return DB::transaction(function () use ($company, $period, $parameters) {
            $locked = DB::table('v3_hr_payroll_periods')->where('id_empresa', $company)->where('id', $period->id)->lockForUpdate()->first();
            if (!$locked) throw new RuntimeException('El periodo de nómina no existe.');
            if (!in_array($locked->estado, array('borrador', 'calculado'), true)) throw new RuntimeException('La nómina aprobada, contabilizada o cerrada ya no puede recalcularse.');

            DB::table('v3_hr_payroll_lines')->where('id_empresa', $company)->where('period_id', $period->id)->delete();
            $employees = DB::table('v3_hr_employees')->where('id_empresa', $company)->where('estado', 'activo')->orderBy('id')->get();
            $generated = 0;
            foreach ($employees as $employee) {
                $contract = DB::table('v3_hr_contracts')->where('id_empresa', $company)->where('employee_id', $employee->id)
                    ->where('estado', 'vigente')->where('fecha_inicio', '<=', $period->fecha_hasta)
                    ->where(function ($query) use ($period) { $query->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $period->fecha_desde); })
                    ->orderBy('fecha_inicio', 'desc')->first();
                if (!$contract) continue;

                $line = $this->calculateEmployee($company, $period, $employee, $contract, $parameters);
                $line['id_empresa'] = $company;
                $line['period_id'] = $period->id;
                $line['employee_id'] = $employee->id;
                $line['created_at'] = date('Y-m-d H:i:s');
                $line['updated_at'] = $line['created_at'];
                DB::table('v3_hr_payroll_lines')->insert($line);
                $generated++;
            }
            if ($generated === 0) throw new RuntimeException('No existen empleados activos con contratos vigentes para el periodo seleccionado.');
            DB::table('v3_hr_payroll_periods')->where('id_empresa', $company)->where('id', $period->id)->update(array('estado' => 'calculado', 'updated_at' => date('Y-m-d H:i:s')));
            return $generated;
        }, 3);
    }

    private function calculateEmployee($company, $period, $employee, $contract, array $p)
    {
        $activeFrom = max(strtotime($period->fecha_desde), strtotime($contract->fecha_inicio));
        $activeTo = min(strtotime($period->fecha_hasta), $contract->fecha_fin ? strtotime($contract->fecha_fin) : strtotime($period->fecha_hasta));
        $calendarDays = max(0, (int) floor(($activeTo - $activeFrom) / 86400) + 1);
        $fullCalendarMonth = $period->fecha_desde === date('Y-m-01', strtotime($period->fecha_desde))
            && $period->fecha_hasta === date('Y-m-t', strtotime($period->fecha_hasta))
            && $contract->fecha_inicio <= $period->fecha_desde
            && (!$contract->fecha_fin || $contract->fecha_fin >= $period->fecha_hasta);
        $payableDays = $fullCalendarMonth ? 30 : min(30, $calendarDays);
        $unpaidDays = (float) DB::table('v3_hr_leave_requests')->where('id_empresa', $company)->where('employee_id', $employee->id)
            ->where('estado', 'aprobado')->where('remunerado', 0)->where('fecha_desde', '<=', $period->fecha_hasta)
            ->where('fecha_hasta', '>=', $period->fecha_desde)->sum('dias');
        $payableDays = max(0, $payableDays - min($payableDays, $unpaidDays));
        $salary = round((float) $contract->sueldo * $payableDays / 30, 2);

        $time = DB::table('v3_hr_time_entries')->where('id_empresa', $company)->where('employee_id', $employee->id)
            ->whereBetween('fecha', array($period->fecha_desde, $period->fecha_hasta))
            ->selectRaw('COALESCE(SUM(minutos_extra_50),0) m50, COALESCE(SUM(minutos_extra_100),0) m100, COALESCE(SUM(minutos_nocturnos),0) mn')->first();
        $hours50 = round((float) $time->m50 / 60, 2);
        $hours100 = round((float) $time->m100 / 60, 2);
        $nightHours = round((float) $time->mn / 60, 2);
        $hourValue = (float) $contract->sueldo / max(1, $p['horas_mes']);
        $overtime = round($hours50 * $hourValue * 1.5 + $hours100 * $hourValue * 2, 2);
        $nightSurcharge = round($nightHours * $hourValue * $p['recargo_nocturno'] / 100, 2);

        $conceptRows = DB::table('v3_hr_concept_assignments as a')->join('v3_hr_concepts as c', 'c.id', '=', 'a.concept_id')
            ->where('a.id_empresa', $company)->where('a.employee_id', $employee->id)->where('a.estado', 1)->where('c.estado', 1)
            ->where('a.periodo_desde', '<=', $period->periodo)
            ->where(function ($query) use ($period) { $query->whereNull('a.periodo_hasta')->orWhere('a.periodo_hasta', '>=', $period->periodo); })
            ->select('a.id', 'a.monto', 'c.codigo', 'c.nombre', 'c.tipo', 'c.categoria', 'c.afecta_iess', 'c.afecta_decimo_tercero', 'c.afecta_vacaciones', 'c.grava_renta')->get();
        $bonuses = 0.0; $otherIncome = 0.0; $conceptDeductions = 0.0; $iessConcepts = 0.0; $thirdConcepts = 0.0; $vacationConcepts = 0.0; $conceptDetail = array();
        foreach ($conceptRows as $concept) {
            $amount = round((float) $concept->monto, 2);
            if ($concept->tipo === 'ingreso') {
                if ($concept->categoria === 'bono') $bonuses += $amount; else $otherIncome += $amount;
                if ($concept->afecta_iess) $iessConcepts += $amount;
                if ($concept->afecta_decimo_tercero) $thirdConcepts += $amount;
                if ($concept->afecta_vacaciones) $vacationConcepts += $amount;
            } else {
                $conceptDeductions += $amount;
            }
            $conceptDetail[] = array('codigo' => $concept->codigo, 'nombre' => $concept->nombre, 'tipo' => $concept->tipo, 'monto' => $amount);
        }

        $income = round($salary + $overtime + $nightSurcharge + $bonuses + $otherIncome, 2);
        $iessBase = round($salary + $overtime + $nightSurcharge + $iessConcepts, 2);
        $thirdBase = round($salary + $overtime + $nightSurcharge + $thirdConcepts, 2);
        $vacationBase = round($salary + $overtime + $nightSurcharge + $vacationConcepts, 2);
        $personalIess = round($iessBase * $p['iess_personal'] / 100, 2);
        $employerIess = round($iessBase * $p['iess_patronal'] / 100, 2);
        $third = round($thirdBase * $p['d13_factor'] / 100, 2);
        $fourth = round($p['sbu'] * $p['d14_factor'] / 100 * ($payableDays / 30), 2);
        $completedYears = max(0, (int) date_diff(new \DateTime($employee->fecha_ingreso), new \DateTime($period->fecha_hasta))->y);
        $vacationEntitlement = $p['vacaciones_dias'] + min($p['vacaciones_adicionales_max'], max(0, $completedYears - 5));
        $vacation = round($vacationBase * ($p['vacaciones_factor'] * $vacationEntitlement / max(1, $p['vacaciones_dias'])) / 100, 2);
        $reserveEligibility = strtotime('+1 year', strtotime($employee->fecha_ingreso));
        $reserveRatio = 0.0;
        if ($reserveEligibility <= strtotime($period->fecha_desde)) $reserveRatio = 1.0;
        elseif ($reserveEligibility <= strtotime($period->fecha_hasta)) {
            $periodCalendarDays = max(1, (int) ((strtotime($period->fecha_hasta) - strtotime($period->fecha_desde)) / 86400) + 1);
            $eligibleDays = max(0, (int) ((strtotime($period->fecha_hasta) - $reserveEligibility) / 86400) + 1);
            $reserveRatio = min(1, $eligibleDays / $periodCalendarDays);
        }
        $reserve = round($iessBase * $p['reserva_factor'] / 100 * $reserveRatio, 2);

        $thirdPaid = $employee->decimo_tercero_modalidad === 'mensual' ? $third : 0.0;
        $fourthPaid = $employee->decimo_cuarto_modalidad === 'mensual' ? $fourth : 0.0;
        $reservePaid = $employee->fondo_reserva_modalidad === 'mensual' ? $reserve : 0.0;
        $advance = (float) DB::table('v3_hr_advances')->where('id_empresa', $company)->where('employee_id', $employee->id)
            ->where('periodo_descuento', $period->periodo)->where('estado', 'contabilizado')->sum('monto');
        $loan = (float) DB::table('v3_hr_loan_installments as i')->join('v3_hr_loans as l', 'l.id', '=', 'i.loan_id')
            ->where('i.id_empresa', $company)->where('l.employee_id', $employee->id)->where('i.periodo', $period->periodo)
            ->where('i.estado', 'pendiente')->whereIn('l.estado', array('contabilizado', 'vigente'))->sum('i.monto');
        $incomeTax = round((float) $employee->retencion_renta_mensual, 2);
        $deductions = round($personalIess + $advance + $loan + $incomeTax + $conceptDeductions, 2);
        $net = round($income + $thirdPaid + $fourthPaid + $reservePaid - $deductions, 2);
        if ($net < 0) throw new RuntimeException('Los descuentos superan el neto del empleado '.$employee->identificacion.'. Revise préstamos, anticipos y conceptos.');

        return array(
            'sueldo' => $salary, 'horas_50' => $hours50, 'horas_100' => $hours100, 'recargo_nocturno' => $nightSurcharge, 'ingresos' => $income,
            'base_iess' => $iessBase, 'bonos' => round($bonuses, 2), 'otros_ingresos' => round($otherIncome, 2),
            'aporte_iess' => $personalIess, 'otros_descuentos' => round($advance + $loan + $incomeTax + $conceptDeductions, 2),
            'provision_decimo_tercero' => $third, 'decimo_tercero_pagado' => $thirdPaid,
            'provision_decimo_cuarto' => $fourth, 'decimo_cuarto_pagado' => $fourthPaid,
            'provision_vacaciones' => $vacation, 'fondo_reserva' => $reserve, 'fondo_reserva_pagado' => $reservePaid,
            'aporte_patronal' => $employerIess, 'retencion_renta' => $incomeTax, 'anticipos' => round($advance, 2),
            'prestamos' => round($loan, 2), 'descuentos_conceptos' => round($conceptDeductions, 2), 'neto' => $net,
            'detalle' => json_encode(array(
                'dias_pagados' => $payableDays, 'dias_sin_remuneracion' => $unpaidDays, 'horas_extra_valor' => $overtime, 'horas_nocturnas' => $nightHours, 'recargo_nocturno_porcentaje' => $p['recargo_nocturno'], 'vacaciones_dias_anuales' => $vacationEntitlement,
                'iess_personal_porcentaje' => $p['iess_personal'], 'iess_patronal_porcentaje' => $p['iess_patronal'],
                'modalidades' => array('decimo_tercero' => $employee->decimo_tercero_modalidad, 'decimo_cuarto' => $employee->decimo_cuarto_modalidad, 'fondo_reserva' => $employee->fondo_reserva_modalidad),
                'conceptos' => $conceptDetail,
            ), JSON_UNESCAPED_UNICODE),
        );
    }

    private function parameter($company, $code, $date, $default)
    {
        $row = DB::table('v3_hr_legal_parameters')->whereIn('id_empresa', array('*', $company))->where('codigo', $code)
            ->where('vigente_desde', '<=', $date)->where(function ($query) use ($date) { $query->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>=', $date); })
            ->orderByRaw('CASE WHEN id_empresa = ? THEN 0 ELSE 1 END', array($company))->orderBy('vigente_desde', 'desc')->first();
        return $row ? (float) $row->valor : (float) $default;
    }
}
