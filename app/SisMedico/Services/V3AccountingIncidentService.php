<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class V3AccountingIncidentService
{
    private $integrity;
    private $certification;
    private $periods;

    public function __construct(V3AccountingIntegrityService $integrity, V3AccountingCertificationService $certification,
        V3AccountingPeriodService $periods)
    {
        $this->integrity = $integrity;
        $this->certification = $certification;
        $this->periods = $periods;
    }

    public function report($companyId, $code, $severity, $category, $title, $description, array $context, $diagnosis, $suggestion, $repair = null)
    {
        if (!Schema::hasTable('v3_accounting_incidents')) return null;
        $companyId = $companyId ?: 'SYSTEM';
        $safeContext = $this->sanitize($context);
        $fingerprint = hash('sha256', $code.'|'.$companyId.'|'.json_encode($safeContext));
        $existing = DB::table('v3_accounting_incidents')->where('id_empresa', $companyId)->where('fingerprint', $fingerprint)->first();
        $values = array('severidad' => strtoupper($severity), 'categoria' => strtoupper($category), 'estado' => 'OPEN',
            'titulo' => substr($title, 0, 190), 'descripcion' => $description, 'contexto' => json_encode($safeContext, JSON_UNESCAPED_UNICODE),
            'diagnostico' => $diagnosis, 'accion_sugerida' => $suggestion, 'reparacion' => $repair,
            'reparacion_estado' => $repair ? 'AVAILABLE' : 'NOT_APPLICABLE', 'ultima_ocurrencia_at' => date('Y-m-d H:i:s'),
            'resuelto_por' => null, 'resuelto_at' => null, 'updated_at' => date('Y-m-d H:i:s'));
        if ($existing) {
            $values['ocurrencias'] = (int) $existing->ocurrencias + 1;
            DB::table('v3_accounting_incidents')->where('id', $existing->id)->update($values);
            return $existing->id;
        }
        return DB::table('v3_accounting_incidents')->insertGetId(array_merge($values, array('id_empresa' => $companyId,
            'codigo' => $code, 'fingerprint' => $fingerprint, 'ocurrencias' => 1, 'created_at' => date('Y-m-d H:i:s'))));
    }

    public function captureException($request, $exception)
    {
        $company = $request->session()->get('id_empresa');
        return $this->report($company, 'APPLICATION_EXCEPTION', 'HIGH', 'CODE', 'Error controlado en contabilidad V3',
            $exception->getMessage(), array('ruta' => $request->path(), 'metodo' => $request->method(),
                'tipo' => get_class($exception), 'archivo' => basename($exception->getFile()), 'linea' => $exception->getLine()),
            'El sistema conservó el error con una huella repetible. Debe revisarse la causa antes de reintentar la mutación.',
            'Corregir el código o la configuración indicada y ejecutar nuevamente el diagnóstico. No se modifica código automáticamente.', null);
    }

    public function scan($companyId, $from, $to)
    {
        $audit = $this->integrity->audit($companyId, $from, $to);
        $periodContext = array('desde' => $from, 'hasta' => $to);
        $companyContext = array('empresa' => $companyId);
        $monthContext = array('periodo' => date('Y-m', strtotime($to)));
        if ($audit['asientos_descuadrados'] > 0) $this->report($companyId, 'UNBALANCED_ENTRIES', 'CRITICAL', 'DATA',
            'Asientos descuadrados', 'Se detectaron '.$audit['asientos_descuadrados'].' asientos activos con diferencia.',
            $periodContext, 'Debe y Haber no coinciden en uno o más asientos.',
            'Revisar el documento de origen y registrar reverso o ajuste autorizado. Nunca completar automáticamente una contrapartida.', null);
        else $this->resolve($companyId, 'UNBALANCED_ENTRIES', $periodContext);
        if ($audit['asientos_sin_detalle'] > 0) $this->report($companyId, 'ENTRIES_WITHOUT_LINES', 'CRITICAL', 'DATA',
            'Asientos sin detalle', 'Se detectaron '.$audit['asientos_sin_detalle'].' cabeceras activas sin líneas.',
            $periodContext, 'Una transacción incompleta dejó una cabecera contable sin detalle.',
            'Anular la cabecera después de verificar que no tenga documento válido asociado.', null);
        else $this->resolve($companyId, 'ENTRIES_WITHOUT_LINES', $periodContext);
        if ($audit['lineas_invalidas'] > 0) $this->report($companyId, 'INVALID_ENTRY_LINES', 'CRITICAL', 'DATA',
            'Líneas contables inválidas', 'Se detectaron '.$audit['lineas_invalidas'].' líneas negativas o con Debe y Haber simultáneos.',
            $periodContext, 'Las líneas incumplen la regla básica de registro de partida doble.',
            'Revisar el documento origen y registrar reverso o ajuste autorizado; no sobrescribir el asiento confirmado.', null);
        else $this->resolve($companyId, 'INVALID_ENTRY_LINES', $periodContext);
        if ($audit['cuentas_sin_categoria_niif'] > 0) $this->report($companyId, 'MISSING_NIIF_CLASSIFICATION', 'HIGH', 'CONFIGURATION',
            'Cuentas sin clasificación NIIF', 'Existen '.$audit['cuentas_sin_categoria_niif'].' cuentas activas sin categoría.',
            $companyContext, 'Los estados financieros no pueden clasificarlas con certeza.',
            'Aplicar clasificación segura por clase 1 a 6 y revisar manualmente las excepciones.', 'CLASSIFY_NIIF_SAFE');
        else $this->resolve($companyId, 'MISSING_NIIF_CLASSIFICATION', $companyContext);
        if ($audit['cuentas_categoria_niif_invalida'] > 0) $this->report($companyId, 'INVALID_NIIF_CLASSIFICATION', 'HIGH', 'CONFIGURATION',
            'Clasificaciones NIIF no reconocidas', 'Existen '.$audit['cuentas_categoria_niif_invalida'].' cuentas con categorías no admitidas.',
            $companyContext, 'Una categoría personalizada o mal escrita impide clasificar el estado financiero de forma determinista.',
            'Corregir manualmente la categoría a ACTIVO, PASIVO, PATRIMONIO, INGRESO, COSTO o GASTO.', null);
        else $this->resolve($companyId, 'INVALID_NIIF_CLASSIFICATION', $companyContext);
        if ($audit['grupos_posibles_duplicados'] > 0) $this->report($companyId, 'POSSIBLE_DUPLICATE_ENTRIES', 'MEDIUM', 'DATA',
            'Posibles asientos duplicados', 'Se detectaron '.$audit['grupos_posibles_duplicados'].' grupos con cabecera contable coincidente.',
            $periodContext, 'La coincidencia es una señal de revisión, no prueba suficiente para borrar un asiento.',
            'Comparar documento de origen y detalle. Si se confirma duplicidad, registrar anulación o reverso autorizado conservando trazabilidad.', null);
        else $this->resolve($companyId, 'POSSIBLE_DUPLICATE_ENTRIES', $periodContext);
        $snapshot = $this->certification->evaluate($companyId, $from, $to);
        if (!$snapshot['financiero']['balance_comprobacion']['cuadrado'] || !$snapshot['financiero']['situacion_financiera']['cuadrado']
            || !$snapshot['financiero']['cambios_patrimonio']['conciliado']) $this->report($companyId, 'FINANCIAL_EQUATION_MISMATCH', 'CRITICAL', 'DATA',
            'Ecuaciones financieras con diferencia', 'El paquete contable no satisface una o más ecuaciones de comprobación, situación o patrimonio.',
            $monthContext, 'Los saldos canónicos no permiten certificar el periodo.',
            'Revisar clasificación NIIF, anulaciones, saldos iniciales y asientos descuadrados; registrar ajuste autorizado si corresponde.', null);
        else $this->resolve($companyId, 'FINANCIAL_EQUATION_MISMATCH', $monthContext);
        if (!$snapshot['financiero']['flujo_efectivo']['conciliado']) $this->report($companyId, 'CASH_FLOW_NOT_RECONCILED', 'HIGH', 'CONFIGURATION',
            'Flujo de efectivo sin conciliar', 'La variación de caja y bancos no coincide con las actividades clasificadas.',
            $monthContext,
            'Existen cuentas sin actividad de flujo o una diferencia entre movimientos y efectivo final.',
            'Clasificar las cuentas como OPERACION, INVERSION o FINANCIACION y revisar transferencias entre cuentas de efectivo.', null);
        else $this->resolve($companyId, 'CASH_FLOW_NOT_RECONCILED', $monthContext);
        if (!$snapshot['auxiliares']['certificado']) $this->report($companyId, 'AUXILIARIES_PENDING', 'HIGH', 'DATA',
            'Auxiliares pendientes de conciliación', implode(', ', $snapshot['auxiliares']['pendientes']),
            $monthContext, 'No existe evidencia aprobada de que todos los auxiliares coincidan con el mayor.',
            'Registrar y aprobar las conciliaciones de clientes, proveedores, inventario, activos, impuestos y bancos.', null);
        else $this->resolve($companyId, 'AUXILIARIES_PENDING', $monthContext);
        return array('auditoria' => $audit, 'certificacion' => $snapshot, 'incidencias_abiertas' => $this->open($companyId)->count());
    }

    public function repair($companyId, $incidentId)
    {
        if (!Schema::hasTable('v3_accounting_repairs')) throw new RuntimeException('Falta la migración de gobierno contable.');
        return DB::transaction(function () use ($companyId, $incidentId) {
            $incident = DB::table('v3_accounting_incidents')->where('id', $incidentId)
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$incident) throw new RuntimeException('La incidencia no existe para esta empresa.');
            if ($incident->reparacion !== 'CLASSIFY_NIIF_SAFE') throw new RuntimeException('Esta incidencia requiere revisión humana y no admite reparación automática.');
            $unclassified = function ($query) { $query->whereNull('categoria_niif')->orWhere('categoria_niif', ''); };
            $before = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 1)->where($unclassified)->count();
            $repairable = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 1)
                ->where($unclassified)->whereIn(DB::raw('LEFT(TRIM(plan), 1)'), array('1', '2', '3', '4', '5', '6'))->count();
            DB::statement("UPDATE plan_cuentas_empresa SET categoria_niif=CASE LEFT(TRIM(plan),1) WHEN '1' THEN 'ACTIVO' WHEN '2' THEN 'PASIVO' WHEN '3' THEN 'PATRIMONIO' WHEN '4' THEN 'INGRESO' WHEN '5' THEN 'COSTO' WHEN '6' THEN 'GASTO' ELSE categoria_niif END WHERE id_empresa=? AND estado=1 AND (categoria_niif IS NULL OR categoria_niif='') AND LEFT(TRIM(plan),1) IN ('1','2','3','4','5','6')", array($companyId));
            $after = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 1)->where($unclassified)->count();
            $classified = $before - $after;
            DB::table('v3_accounting_repairs')->insert(array('incidencia_id' => $incident->id, 'accion' => $incident->reparacion,
                'estado' => $after < $before ? 'APPLIED' : 'NO_CHANGE', 'antes' => json_encode(array('sin_clasificar' => $before)),
                'despues' => json_encode(array('sin_clasificar' => $after, 'clasificadas' => $classified,
                    'no_clasificables' => $after)), 'resultado' => 'Solo se clasificaron clases contables reconocidas (1 a 6).',
                'ejecutado_por' => Auth::check() ? Auth::user()->id : null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
            DB::table('v3_accounting_incidents')->where('id', $incident->id)->update(array(
                'estado' => $after === 0 ? 'RESOLVED' : 'OPEN', 'reparacion_estado' => $after < $before ? 'APPLIED' : 'NO_CHANGE',
                'resuelto_por' => $after === 0 && Auth::check() ? Auth::user()->id : null,
                'resuelto_at' => $after === 0 ? date('Y-m-d H:i:s') : null, 'updated_at' => date('Y-m-d H:i:s')));
            return array('antes' => $before, 'reparables' => $repairable, 'clasificadas' => $classified,
                'despues' => $after, 'requieren_revision' => $after);
        });
    }

    public function open($companyId)
    {
        if (!Schema::hasTable('v3_accounting_incidents')) return collect();
        return DB::table('v3_accounting_incidents')->where('id_empresa', $companyId)->where('estado', 'OPEN')
            ->orderByRaw("FIELD(severidad,'CRITICAL','HIGH','MEDIUM','LOW')")->orderBy('ultima_ocurrencia_at', 'desc')->get();
    }

    private function sanitize(array $context)
    {
        $safe = array();
        foreach ($context as $key => $value) {
            if (preg_match('/password|clave|token|secret|authorization|cookie/i', (string) $key)) $safe[$key] = '[PROTECTED]';
            elseif (is_scalar($value) || is_null($value)) $safe[$key] = is_string($value) ? substr($value, 0, 500) : $value;
        }
        return $safe;
    }

    private function resolve($companyId, $code, array $context)
    {
        if (!Schema::hasTable('v3_accounting_incidents')) return;
        $fingerprint = hash('sha256', $code.'|'.($companyId ?: 'SYSTEM').'|'.json_encode($this->sanitize($context)));
        DB::table('v3_accounting_incidents')->where('id_empresa', $companyId ?: 'SYSTEM')->where('fingerprint', $fingerprint)
            ->where('estado', 'OPEN')->update(array('estado' => 'RESOLVED', 'reparacion_estado' => 'NOT_NEEDED',
                'resuelto_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
    }
}
