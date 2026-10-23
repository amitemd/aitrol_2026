<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class PlanCuentas extends Model
{
    private static $modelo = 'Plan_Cuentas';
    protected $table = 'plan_cuentas_empresa';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    protected $keyType = 'string';

    public static function buscarIdCuentaPlan($term)
    {
        return Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($term) {

                $q->where('plan_cuentas.nombre', 'LIKE', $term . '%');
            })
            ->where('pe.estado', 2)
            ->groupBy('pe.plan')
            ->first([
                'pe.id_plan'
            ]);
    }
    public static function buscarCuenta($array = null, $perdida = '')
    {
        $dato = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->join('users as uc', 'pe.id_usuariocrea', '=', 'uc.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($perdida) {
                if (($perdida != '' && $perdida == 'PASIVOS')) {
                    $q->where(DB::raw('SUBSTRING(pe.plan, 1, 1)'), 2);
                }
            })
            ->where(function ($q) use ($array, $perdida) {
                if ($array != null) {
                    $cont = 0;
                    foreach ($array as $key => $val) {
                        /*if (($perdida != '' && $perdida == 'PERDIDA') && $cont == 0) {
                            $q->where('plan_cuentas.nombre', 'LIKE', $val . '%');
                        } else {
                            $q->where('plan_cuentas.nombre', 'LIKE', '%' . $val . '%');
                        }*/
                        $q->where('plan_cuentas.nombre', 'LIKE', '%' . $val . '%');
                        $cont++;
                    }
                }
            })
            ->where('pe.estado', 2)
            ->groupBy('pe.plan')
            ->orderBy(DB::Raw('IFNULL(pe.plan, plan_cuentas.id)'))
            ->first([
                'pe.plan'
            ]);
        return $dato;
    }
    public static function exportarPlan()
    {

        $empresa = config('data.empresa');

        $plan = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->join('users as uc', 'pe.id_usuariocrea', '=', 'uc.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->groupBy('pe.plan')
            ->orderBy(DB::Raw('IFNULL(pe.plan, plan_cuentas.id)'))
            ->get([
                'pe.plan',
                DB::Raw('IFNULL(pe.nombre,plan_cuentas.nombre) as nombre'),
                DB::Raw('DATE_FORMAT(pe.updated_at, "%Y-%m-%d") as fecha'),
                'pe.estado',
                'pe.naturaleza',
                'pe.naturaleza_2',
                'pe.estado_cierre_ano',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
            ]);

        // Crear un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Configurar el contenido de la hoja de cálculo
        $objPHPExcel->setActiveSheetIndex(0);
        $hojaActiva = $objPHPExcel->getActiveSheet();

        // Escribir los datos en la hoja de cálculo
        $hojaActiva->mergeCells('A1:B1'); // Fusionar celdas A1 y B1
        $hojaActiva->setCellValue('A1', $empresa->razonsocial); // Nombre de la empresa
        $hojaActiva->getStyle('A1')->getFont()->setBold(true); // Negrita
        $hojaActiva->getStyle('A1')->getFont()->setSize(18); // Tamaño de fuente 18px
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Centrado horizontal

        $hojaActiva->mergeCells('A2:B2'); // Fusionar celdas A1 y B1
        $hojaActiva->setCellValue('A2', 'PLAN DE CUENTAS'); // Título del plan de cuentas
        $hojaActiva->getStyle('A2')->getFont()->setBold(true); // Negrita
        $hojaActiva->getStyle('A2')->getFont()->setSize(18); // Tamaño de fuente 18px
        $hojaActiva->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Centrado horizontal

        $hojaActiva->setCellValue('A4', 'CÓDIGO'); // Nombre de la empresa
        $hojaActiva->getStyle('A4')->getFont()->setBold(true); // Negrita
        $hojaActiva->getStyle('A4')->getFont()->setSize(12); // Tamaño de fuente 18px
        $hojaActiva->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Centrado horizontal

        $hojaActiva->setCellValue('B4', 'NOMBRE'); // Título del plan de cuentas
        $hojaActiva->getStyle('B4')->getFont()->setBold(true); // Negrita
        $hojaActiva->getStyle('B4')->getFont()->setSize(12); // Tamaño de fuente 18px
        $hojaActiva->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Centrado horizontal

        $row = 5; // Fila inicial para los datos del plan de cuentas

        foreach ($plan as $fila) {
            $codigoCuenta = (string) $fila->plan;
            $codigoCuenta = str_replace(',', '.', $codigoCuenta); // Reemplazar comas por puntos en el código de la cuenta
            $hojaActiva->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); // Centrado horizontal
            $hojaActiva->setCellValue('A' .  $row, $codigoCuenta); // Código de la cuenta
            $hojaActiva->setCellValue('B' . $row, $fila->nombre); // Nombre de la cuenta
            $row++; // Avanzar a la siguiente fila
        }

        $lastColumnIndex = PHPExcel_Cell::columnIndexFromString($hojaActiva->getHighestColumn());
        for ($col = 0; $col < $lastColumnIndex; $col++) {
            $hojaActiva->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $lastRow = $hojaActiva->getHighestRow(); // Obtener la última fila con datos
        $lastColumn = $hojaActiva->getHighestColumn(); // Obtener la última columna con datos
        $hojaActiva->getStyle('A2:B2')->applyFromArray([
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '007bff'], // Color de fondo (por ejemplo, amarillo)
            ],
        ]);
        $lastRow = $hojaActiva->getHighestRow(); // Obtener la última fila con datos
        $lastColumn = $hojaActiva->getHighestColumn(); // Obtener la última columna con datos
        $hojaActiva->getStyle('A4:B4')->applyFromArray([
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '80888B'], // Color de fondo (por ejemplo, amarillo)
            ],
        ]);
        $range = 'A4:' . $lastColumn . $lastRow; // Rango de celdas con datos
        $styleArray = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN, // Estilo del borde (delgado)
                    'color' => ['rgb' => '000000'], // Color del borde (negro)
                ],
            ],
        ];

        $hojaActiva->getStyle($range)->applyFromArray($styleArray);
        // Crear el objeto Writer para guardar el archivo Excel
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // Enviar el archivo Excel al navegador para su descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PLANDECUENTAS_' . $empresa->razonsocial . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit;
    }
    public static function modificarPlanCuenta($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            $ip = Utilidades::getRealIP();
            $arrayPlan = [
                'nombre' => strtoupper($data['nombre']),
                'descripcion' => strtoupper($data['nombre']),
                'estado' => $data['tipo'],
                'id_usuariomod' => Auth::user()->id,
                'ip_modificacion' => $ip,
                'updated_at' => date('Y-m-d H:i:s'),
                'naturaleza' => $data['naturaleza'],
                'naturaleza_2' => $data['naturaleza_2'],
            ];
            Plan_Cuentas::where('id', $data['plan'])->update($arrayPlan);
            $arrayPlanEmpresa = [
                'nombre' => strtoupper($data['nombre']),
                'descripcion' => strtoupper($data['nombre']),
                'estado' => $data['tipo'],
                'id_usuariomod' => Auth::user()->id,
                'ip_modificacion' => $ip,
                'updated_at' => date('Y-m-d H:i:s'),
                'naturaleza' => $data['naturaleza'],
                'naturaleza_2' => $data['naturaleza_2'],
                'id_plan' => $data['plan'],
                'plan' => $data['plan'],
                'estado_cierre_ano' => $data['estado_cierre_ano']
            ];
            PlanCuentas::where('id_plan', $data['plan'])->where('id_empresa', session('id_empresa'))->update($arrayPlanEmpresa);
            $r->c = 'Plan_Cuenta';
            $r->s = 'modificarPlanCuenta';
            $r->d = $origin['d'];
            $r->m = PlanCuentas::$modelo;
            $r->o = 'Se actualizo el plan de cuentas con id.: ' . $data['plan'];
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Plan_Cuenta';
            $r->s = 'modificarPlanCuenta';
            $r->d = $origin['d'];
            $r->m = PlanCuentas::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            DB::disableQueryLog();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function savePlanCuenta($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $arrayPlan = [
                'id' => $data['lblcodigo'] . $data['txtcodigo'],
                'nombre' => strtoupper($data['txtnombre']),
                'descripcion' => strtoupper($data['txtnombre']),
                'estado' => $data['cmbtipo'],
                'id_padre' => substr($data['lblcodigo'], 0, -1),
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'naturaleza_2' => $data['cmbnaturaleza_2']
            ];
            $id = Plan_Cuentas::insertGetId($arrayPlan);
            $arrayPlanEmpresa = [
                'nombre' => strtoupper($data['txtnombre']),
                'descripcion' => strtoupper($data['txtnombre']),
                'estado' => $data['cmbtipo'],
                'id_padre' => substr($data['lblcodigo'], 0, -1),
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'naturaleza_2' => $data['cmbnaturaleza_2'],
                'id_empresa' => session('id_empresa'),
                'id_plan' => $data['lblcodigo'] . $data['txtcodigo'],
                'plan' => $data['lblcodigo'] . $data['txtcodigo'],
                'estado_cierre_ano' => $data['cmbcierreanio']
            ];
            $id = PlanCuentas::insertGetId($arrayPlanEmpresa);
            $r->c = 'Plan_Cuenta';
            $r->s = 'savePlanCuenta';
            $r->d = $origin['d'];
            $r->m = PlanCuentas::$modelo;
            $r->o = 'Se creo el plan de cuentas con id.: ' . $id;
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Plan_Cuenta';
            $r->s = 'savePlanCuenta';
            $r->d = $origin['d'];
            $r->m = PlanCuentas::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            DB::disableQueryLog();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function secuenciaCuenta($plan)
    {
        $idPlan = PlanCuentas::where('plan', $plan)->where('id_empresa', session('id_empresa'))->first();
        $cuentas = PlanCuentas::where('id_empresa', session('id_empresa'))
            ->where('id_padre', $idPlan->id_plan)
            ->orderBY('plan', 'DESC')
            ->first();
        if ($cuentas != '') {
            $plan = explode('.', $cuentas->plan);
            $count = count($plan) - 1;
            if (count($plan) > 0) {
                $plan = $plan[$count] + 1;
            }
        } else {
            $plan = 1;
        }
        return str_pad($plan, 2, 0, STR_PAD_LEFT);
    }
    public static function detalleCuenta($plan)
    {
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->join('users as uc', 'pe.id_usuariocrea', '=', 'uc.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.plan', $plan)
            ->groupBy('pe.plan')
            ->orderBy(DB::Raw('IFNULL(pe.plan, plan_cuentas.id)'))
            ->first([
                'pe.plan',
                DB::Raw('IFNULL(pe.nombre,plan_cuentas.nombre) as nombre'),
                DB::Raw('DATE_FORMAT(pe.updated_at, "%Y-%m-%d") as fecha'),
                'pe.estado',
                'pe.naturaleza',
                'pe.naturaleza_2',
                'pe.estado_cierre_ano',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
            ]);
        return json_encode($cuentas);
    }
    public static function traerCuentasPlan($plan)
    {
        $html = '';
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->join('users as uc', 'pe.id_usuariocrea', '=', 'uc.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.id_padre', $plan)
            ->groupBy('pe.plan')
            ->orderBy(DB::Raw('IFNULL(pe.plan, plan_cuentas.id)'))
            ->get([
                'pe.plan',
                DB::Raw('IFNULL(pe.nombre,plan_cuentas.nombre) as nombre'),
                DB::Raw('DATE_FORMAT(pe.updated_at, "%Y-%m-%d") as fecha'),
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
            ]);
        if ($cuentas->count() > 0) {
            foreach ($cuentas as $cuenta) {
                $html .= '<tr>';
                $html .= '<td><a href="javascript:;" onclick="detalleCuenta(\'' . $cuenta->plan . '\')">' . $cuenta->plan . '</a></td>';
                $html .= '<td>' . $cuenta->nombre . '</td>';
                $html .= '<td style="text-align:center;">' . $cuenta->fecha . '</td>';
                $html .= '<td style="text-align:center;">' . $cuenta->usuariocrea . '</td>';
                $html .= '</tr>';
            }
        } else {
            $idPlan = PlanCuentas::where('plan', $plan)->where('id_empresa', session('id_empresa'))->first();
            $cuentas_ = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
                ->join('users as uc', 'pe.id_usuariocrea', '=', 'uc.id')
                ->where('pe.id_empresa', session('id_empresa'))
                ->where('pe.id_padre', $idPlan->id)
                ->groupBy('pe.plan')
                ->orderBy(DB::Raw('IFNULL(pe.plan, plan_cuentas.id)'))
                ->get([
                    'pe.plan',
                    DB::Raw('IFNULL(pe.nombre,plan_cuentas.nombre) as nombre'),
                    DB::Raw('DATE_FORMAT(pe.updated_at, "%Y-%m-%d") as fecha'),
                    'pe.estado',
                    'pe.naturaleza',
                    'pe.naturaleza_2',
                    'pe.estado_cierre_ano',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                ]);
            if ($cuentas_->count() > 0) {
                foreach ($cuentas_ as $cuenta_) {
                    $html .= '<tr>';
                    $html .= '<td><a href="javascript:;" onclick="detalleCuenta(\'' . $cuenta_->plan . '\')">' . $cuenta_->plan . '</a></td>';
                    $html .= '<td>' . $cuenta_->nombre . '</td>';
                    $html .= '<td style="text-align:center;">' . $cuenta_->fecha . '</td>';
                    $html .= '<td style="text-align:center;">' . $cuenta_->usuariocrea . '</td>';
                    $html .= '</tr>';
                }
            }
        }
        echo $html;
    }
    public static function printTree($nodes)
    {
        echo "<div class=\"contenedor-arbol\">";
        echo "<ul>";
        foreach ($nodes as $node) {
            echo "<li>";
            if (isset($node->children) && is_array($node->children)) {
                echo $node->icon . ' ';
                echo "<span onclick=\"mostrarCuenta('$node->plan')\">{$node->descripcion}</span>";
                echo "<ul class=\"collapsed\">";
                self::printTree($node->children);
                echo "</ul>";
            } else {
                echo "<i class=\"fas fa-edit\" style=\"color: #007bff\"></i> ";
                echo "<span onclick=\"mostrarCuenta('$node->plan')\">{$node->descripcion}</span>";
            }
            echo "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    public static function buildTree(array &$elements, $parentId = 0)
    {
        $branch = array();
        foreach ($elements as &$element) {
            if ($element->id_padre == $parentId) {
                $children = PlanCuentas::buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
                unset($element);
            }
        }

        return $branch;
    }
    public static function getPlanCuentaTree()
    {
        $id_empresa = session('id_empresa');
        $sql = "select '' as children,
        IF(pe.plan=1,'<i class=\"fas fa-chart-line\" style=\"color:green\"></i>',
        IF(pe.plan=2,'<i class=\"fas fa-exclamation-triangle\" style=\"color:red\"></i>',
        IF(pe.plan=3,'<i class=\"fas fa-balance-scale\" style=\"color: blue\"></i>',
        IF(pe.plan=4,'<i class=\"fas fa-money-check-alt\" style=\"color: #28a745\"></i>',
        IF(pe.plan=5,'<i class=\"fas fa-money-bill-wave-alt\" style=\"color: #dc3545\"></i>','<i class=\"fas fa-sitemap\" style=\"color: #6c757d\"></i>'))))) as icon,
        pe.plan,IFNULL(pe.nombre,p.nombre) as descripcion,p.id,p.estado,pe.estado,pe.naturaleza,pe.naturaleza_2,count(pe.plan) as level,pe.id_padre
        from plan_cuentas p
        join plan_cuentas_empresa pe on p.id=pe.id_plan where pe.id_empresa='$id_empresa'
        GROUP BY
        pe.plan
        ORDER BY
        IFNULL(pe.plan, p.id);";
        $plan = DB::select($sql);
        return $plan;
    }
    public static function traerCuentaGeneral($r)
    {
        $combo = '';
        $cuenta = PlanCuentas::selectRaw('plan,CONCAT(plan, " | ",nombre) as text')
            ->where('plan',  $r->id)
            ->first();
        if ($cuenta == '') {
            $cuenta = PlanCuentas::selectRaw('plan,CONCAT(plan, " | ",nombre) as text')
                ->where('id_plan',  $r->id)
                ->first();
        }
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->plan . '" selected>' . $cuenta->text . '</option>';
        }
        return $combo;
    }
    public static function getPlanCuenta($id = '')
    {
        if ($id == '') {
            return PlanCuentas::join('users as uc', 'plan_cuentas_empresa.id_usuariocrea', 'uc.id')
                ->join('users as um', 'plan_cuentas_empresa.id_usuariomod', 'um.id')
                ->where('id_empresa', session('id_empresa'))
                ->orderBy('plan', 'asc')
                ->get([
                    'plan_cuentas_empresa.id',
                    'plan_cuentas_empresa.plan',
                    'plan_cuentas_empresa.nombre',
                    'plan_cuentas_empresa.descripcion',
                    'plan_cuentas_empresa.created_at',
                    'plan_cuentas_empresa.updated_at',
                    'uc.nombre1',
                    'uc.nombre2',
                    'uc.apellido1',
                    'uc.apellido2',
                    'um.nombre1 as nombre1_',
                    'um.nombre2 as nombre2_',
                    'um.apellido1 as apellido1_',
                    'um.apellido2 as apellido2_',
                    'plan_cuentas_empresa.estado'
                ]);
        } else {
            return PlanCuentas::where('id_empresa', session('id_empresa'))->where('id', $id)->first();
        }
    }
    public static function getAllCuentas($term = '')
    {
        $cuentas = PlanCuentas::selectRaw('id_plan as id,CONCAT(plan, " | ", nombre) as text')
            ->where('id_empresa', session('id_empresa'))
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('plan', 'like', '%' . $term . '%');
            })
            ->whereNotNull('id_padre')
            ->get();
        return $cuentas;
    }
    public static function cuentaContableCombo($id)
    {
        $combo = '';
        $cuenta = PlanCuentas::find($id);
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->id . '" selected>' . $cuenta->plan . ' | ' . $cuenta->nombre . '</option>';
        }
        return $combo;
    }
    public static function cuentaContable()
    {
        return PlanCuentas::join('plan_cuentas as pc', 'pc.id', 'plan_cuentas_empresa.id_plan')
            ->where('pc.estado', '2')
            //->where('plan_cuentas_empresa.id_empresa',session('id_empresa'))
            ->select('plan_cuentas_empresa.id_plan as id', 'plan_cuentas_empresa.nombre')->get();
    }
    public static function allCuentas()
    {
        return Plan_Cuentas::join('plan_cuentas_empresa as pe', function ($q) {
            $q->on('plan_cuentas.id', 'pe.id_plan')
                ->where('pe.id_empresa', session('id_empresa'));
        })
            ->where("plan_cuentas.estado", 2)
            ->get([
                'plan_cuentas.id',
                DB::raw('IFNULL(pe.nombre,plan_cuentas.nombre) as nombre'),
                'pe.plan',
                'plan_cuentas.estado',
                'pe.naturaleza',
                'pe.naturaleza_2',
                'pe.plan',
            ]);
    }
    public static function traerCuentas()
    {
        return Plan_Cuentas::where('plan_cuentas.estado', '2')->join('plan_cuentas_empresa as pe', 'plan_cuentas.id', '=', 'pe.id_plan')->where('pe.id_empresa', session("id_empresa"))->select('plan_cuentas.*', 'pe.plan as plan_empresa')->get();
    }
    public static function allPlanCuenta(){

        return PlanCuentas::where('estado', '2')->where("id_empresa", session("id_empresa"))->get();
    }
}
