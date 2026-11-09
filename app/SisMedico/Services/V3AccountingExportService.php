<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

class V3AccountingExportService
{
    private $reports;

    public function __construct(V3FinancialReportService $reports)
    {
        $this->reports = $reports;
    }

    public function incomeExcel($companyId, $from, $to)
    {
        $report = $this->reports->incomeStatement($companyId, $from, $to);
        $rows = array(array('ESTADO DE RESULTADOS', '', ''), array('Desde', $report['desde'], 'Hasta '.$report['hasta']),
            array('Código', 'Cuenta', 'Valor'));
        foreach (array('INGRESO', 'COSTO', 'GASTO') as $category) {
            $rows[] = array($category, '', '');
            foreach ($report['detalle'][$category] as $line) $rows[] = array($line['plan'], $line['nombre'], $line['valor']);
            $rows[] = array('', 'Total '.$category, $report['categorias'][$category]);
        }
        $rows[] = array('', 'RESULTADO DEL PERIODO', $report['resultado']);
        return $this->excel('Estado de resultados', $rows);
    }

    public function positionExcel($companyId, $asOf, $from = null)
    {
        $report = $this->reports->financialPosition($companyId, $asOf);
        $rows = array(array('ESTADO DE SITUACIÓN FINANCIERA', '', ''), array('Fecha', $report['fecha'], ''),
            array('Código', 'Cuenta', 'Valor'));
        foreach (array('ACTIVO', 'PASIVO', 'PATRIMONIO') as $category) {
            $rows[] = array($category, '', '');
            foreach ($report['detalle'][$category] as $line) {
                $rows[] = array($line['plan'], $line['nombre'], abs((float) $line['valor']));
            }
            $rows[] = array('', 'Total '.$category, abs((float) $report['categorias'][$category]));
        }
        $rows[] = array('', 'DIFERENCIA DE ECUACIÓN', abs((float) $report['diferencia']));
        return $this->excel('Situación financiera', $rows);
    }

    public function incomePdf($companyId, $from, $to)
    {
        $report = $this->reports->incomeStatement($companyId, $from, $to);
        $sections = array();
        foreach (array('INGRESO', 'COSTO', 'GASTO') as $category) {
            $sections[] = array('titulo' => $category, 'filas' => $report['detalle'][$category], 'total' => $report['categorias'][$category]);
        }
        return $this->pdf($companyId, 'Estado de resultados', 'Desde '.$report['desde'].' hasta '.$report['hasta'], $sections,
            'Resultado del periodo', $report['resultado']);
    }

    public function positionPdf($companyId, $asOf, $from = null)
    {
        $report = $this->reports->financialPosition($companyId, $asOf);
        $sections = array();
        foreach (array('ACTIVO', 'PASIVO', 'PATRIMONIO') as $category) {
            $rows = array();
            foreach ($report['detalle'][$category] as $line) {
                $line['valor'] = abs((float) $line['valor']);
                $rows[] = $line;
            }
            $sections[] = array('titulo' => $category, 'filas' => $rows, 'total' => abs((float) $report['categorias'][$category]));
        }
        return $this->pdf($companyId, 'Estado de situación financiera', 'Al '.$report['fecha'], $sections,
            'Diferencia de ecuación', abs((float) $report['diferencia']));
    }

    private function excel($name, array $rows)
    {
        return \Excel::create($name, function ($excel) use ($name, $rows) {
            $excel->sheet($name, function ($sheet) use ($rows) {
                foreach ($rows as $row) $sheet->appendRow($row);
                $sheet->setWidth(array('A' => 22, 'B' => 55, 'C' => 20));
            });
        })->export('xlsx');
    }

    private function flattenAccountGroups(array $groups)
    {
        $rows = array();
        $walk = function (array $group) use (&$walk, &$rows) {
            $level = isset($group['nivel']) ? (int) $group['nivel'] : 0;
            $rows[] = array('plan' => $group['plan'], 'nombre' => $group['nombre'], 'valor' => $group['valor'],
                'tipo' => 'grupo', 'nivel' => $level);
            foreach ($group['subgrupos'] as $subgroup) $walk($subgroup);
            foreach ($group['detalles'] as $detail) {
                $detail['tipo'] = 'detalle';
                $detail['nivel'] = $level + 1;
                $rows[] = $detail;
            }
        };
        foreach ($groups as $group) $walk($group);
        return $rows;
    }

    private function pdf($companyId, $title, $period, array $sections, $footerTitle, $footerValue)
    {
        $company = DB::table('empresa')->where('id', $companyId)->first();
        $companyName = $company && isset($company->razonsocial) ? $company->razonsocial : 'Empresa';
        $html = '<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;color:#17324d;font-size:11px}'
            .'h1{text-align:center;font-size:20px;margin:0}h2{text-align:center;font-size:13px;margin:5px 0 18px}'
            .'h3{background:#eaf6fa;padding:7px;margin:14px 0 0}table{width:100%;border-collapse:collapse}'
            .'th,td{border:1px solid #ccdbe4;padding:6px}.number{text-align:right}.total{font-weight:bold;background:#f3f7fa}'
            .'.group{font-weight:bold;background:#eaf6fa}.detail td:first-child{padding-left:18px}.detail td:nth-child(2){padding-left:18px}</style></head><body>'
            .'<h1>'.e($companyName).'</h1><h2>'.e($title).' — '.e($period).'</h2>';
        foreach ($sections as $section) {
            $html .= '<h3>'.e($section['titulo']).'</h3><table><thead><tr><th>Código</th><th>Cuenta</th><th>Valor</th></tr></thead><tbody>';
            foreach ($section['filas'] as $row) {
                $rowClass = isset($row['tipo']) && $row['tipo'] === 'grupo' ? ' class="group"'
                    : (isset($row['tipo']) && $row['tipo'] === 'detalle' ? ' class="detail"' : '');
                $padding = 6 + ((isset($row['nivel']) ? (int) $row['nivel'] : 0) * 10);
                $html .= '<tr'.$rowClass.'><td style="padding-left:'.$padding.'px">'.e($row['plan']).'</td>'
                    .'<td style="padding-left:'.$padding.'px">'.e($row['nombre']).'</td><td class="number">'.number_format($row['valor'], 2).'</td></tr>';
            }
            $html .= '<tr class="total"><td colspan="2">Total '.e($section['titulo']).'</td><td class="number">'.number_format($section['total'], 2).'</td></tr></tbody></table>';
        }
        $html .= '<h3>'.e($footerTitle).': '.number_format($footerValue, 2).'</h3></body></html>';
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html)->setPaper('a4', 'portrait');
        return $pdf->stream(str_slug($title).'.pdf');
    }
}
