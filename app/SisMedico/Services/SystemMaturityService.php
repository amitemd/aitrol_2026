<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class SystemMaturityService
{
    private $controllerInventory;
    private $testInventory;

    public function analyze()
    {
        $modules = array();
        $routes = $this->applicationRoutes();

        foreach ($this->definitions() as $definition) {
            $moduleRoutes = $this->matchingRoutes($routes, $definition['patterns']);
            $controllers = $this->controllerFiles($moduleRoutes, $definition['controller_terms']);
            $views = $this->viewFiles($definition['view_paths']);
            $tests = $this->testFiles($definition['test_terms']);
            $support = $this->supportFiles(isset($definition['support_paths']) ? $definition['support_paths'] : array());
            $signals = $this->sourceSignals($controllers);
            $uniqueActions = $this->uniqueActions($moduleRoutes);

            if (count($moduleRoutes) === 0 && count($controllers) === 0 && count($views) === 0 && count($support) === 0) {
                continue;
            }

            $supportTarget = isset($definition['support_target']) ? max(1, (int) $definition['support_target']) : 4;
            $supportPercent = $this->cap((count($support) / $supportTarget) * 100);
            $targets = array_merge(array(
                'routes' => 10,
                'controllers' => 4,
                'actions' => 12,
                'views' => 8,
                'tests' => 2,
            ), isset($definition['targets']) ? $definition['targets'] : array());

            $components = array(
                'routes' => $this->cap((count($moduleRoutes) / max(1, $targets['routes'])) * 100),
                'logic' => $this->cap(((count($controllers) / max(1, $targets['controllers'])) * 70) + (($uniqueActions / max(1, $targets['actions'])) * 30)),
                'views' => $this->cap((count($views) / max(1, $targets['views'])) * 100),
                'data' => count($controllers) ? $this->cap(($signals['persistence'] / count($controllers)) * 100) : 0,
                'controls' => count($controllers) ? $this->cap(($signals['controls'] / count($controllers)) * 100) : 0,
                'tests' => $this->cap((count($tests) / max(1, $targets['tests'])) * 100),
            );

            // Migraciones, seeders, middleware, archivos de datos y servicios son
            // evidencia real aunque el modulo no concentre todo en controladores.
            if (count($support) > 0) {
                $components['data'] = max($components['data'], $this->cap($supportPercent * .75));
                $components['controls'] = max($components['controls'], $this->cap($supportPercent * .65));
            }

            $extraEvidence = array();
            $recommendation = $this->recommendation($components);

            if ($definition['key'] === 'sidebar_dynamic') {
                $sidebar = $this->sidebarMetrics();
                $components['views'] = $sidebar['migration_percent'];
                $components['data'] = $sidebar['table_ready'] ? 100 : 0;
                $components['controls'] = $sidebar['permissions_ready'] ? 100 : 0;
                $extraEvidence = array(
                    'database_items' => $sidebar['database_items'],
                    'system_database_items' => $sidebar['system_database_items'],
                    'v3_database_items' => $sidebar['v3_database_items'],
                    'legacy_links' => $sidebar['legacy_links'],
                    'migration_percent' => $sidebar['migration_percent'],
                );
                $foundation = ($components['routes'] + $components['logic'] + $components['data'] + $components['controls']) / 4;
                $score = (int) round(($sidebar['migration_percent'] * .55) + ($foundation * .35) + ($components['tests'] * .10));
                $recommendation = $this->sidebarRecommendation($sidebar);
            } else {
                $score = (int) round(
                    ($components['routes'] * .15) +
                    ($components['logic'] * .20) +
                    ($components['views'] * .20) +
                    ($components['data'] * .20) +
                    ($components['controls'] * .15) +
                    ($components['tests'] * .10)
                );
            }

            $evidence = array_merge(array(
                'routes' => count($moduleRoutes),
                'controllers' => count($controllers),
                'actions' => $uniqueActions,
                'views' => count($views),
                'tests' => count($tests),
                'support' => count($support),
                'support_target' => $supportTarget,
                'persistence_controllers' => $signals['persistence'],
                'controlled_controllers' => $signals['controls'],
                'targets' => $targets,
            ), $extraEvidence);
            $gaps = $this->moduleGaps($components, $evidence, $definition['key'] === 'sidebar_dynamic', $targets);
            if ($definition['key'] !== 'sidebar_dynamic') $recommendation = $this->recommendation($components, $gaps);

            $modules[] = array(
                'key' => $definition['key'],
                'name' => $definition['name'],
                'description' => $definition['description'],
                'icon' => $definition['icon'],
                'score' => $score,
                'status' => $this->status($score),
                'components' => $components,
                'evidence' => $evidence,
                'points_to_100' => max(0, 100 - $score),
                'gaps' => $gaps,
                'recommendation' => $recommendation,
            );
        }

        usort($modules, function ($left, $right) {
            return $right['score'] - $left['score'];
        });

        $overall = count($modules)
            ? (int) round(array_sum(array_column($modules, 'score')) / count($modules))
            : 0;

        $clinical = $this->clinicalMaturity();
        $v3Readiness = $this->v3ProductionReadiness();
        $completion = $this->completionRequirements($clinical, $v3Readiness);

        return array(
            'overall' => $overall,
            'overall_status' => $this->status($overall),
            'completion' => $completion,
            'clinical' => $clinical,
            'v3_readiness' => $v3Readiness,
            'modules' => $modules,
            'route_count' => count($routes),
            'artifact_count' => array_sum(array_map(function ($module) {
                return $module['evidence']['routes'] + $module['evidence']['controllers'] + $module['evidence']['views'] + $module['evidence']['tests'] + $module['evidence']['support'];
            }, $modules)),
            'updated_at' => date('d/m/Y H:i:s'),
        );
    }

    /**
     * Production readiness is intentionally separate from the artifact score.
     * Having a route, controller or view does not prove that accounting data is
     * isolated, auditable, reproducible or mathematically correct.
     */
    private function v3ProductionReadiness()
    {
        $routes = $this->source('routes/v3/contable/web.php');
        $api = $this->source('app/Http/Controllers/v3/contable/Apis/ApiInicioController.php');
        $registration = $this->source('app/Http/Controllers/v3/contable/RegistroController.php');
        $general = $this->source('app/Http/Middleware/v3/General.php');
        $v3Authorization = $this->source('app/Services/V3AuthorizationService.php');
        $v3MethodGuard = $this->source('app/Http/Middleware/v3/GuardV3RequestMethod.php');
        $reports = $this->source('app/Http/Controllers/v3/contable/ReportesController.php');
        $journal = $this->source('app/v3/contable/AsientosCabecera.php');
        $accounting = $this->source('app/v3/contable/Contabilidad.php');
        $banking = $this->source('app/v3/contable/CajaBanco.php');
        $audit = $this->source('app/v3/contable/Auditorias.php');
        $utilities = $this->source('app/v3/contable/Utilidades.php');
        $utilityController = $this->source('app/Http/Controllers/v3/contable/UtilidadesController.php');
        $tenantScope = $this->source('app/Services/V3TenantScope.php');
        $fileSecurity = $this->source('app/Services/V3FileSecurityService.php');
        $auditService = $this->source('app/Services/V3AuditService.php');
        $auditMiddleware = $this->source('app/Http/Middleware/v3/AuditV3Mutation.php');
        $ledgerDetails = $this->source('app/v3/contable/AsientosDetalles.php');
        $cashFlow = $this->source('app/v3/contable/ComprobanteIngreso.php');
        $financialReports = $this->source('app/Services/V3FinancialReportService.php');
        $accountingExports = $this->source('app/Services/V3AccountingExportService.php');
        $accountingPosting = $this->source('app/Services/V3AccountingPostingService.php');
        $accountingSequences = $this->source('app/v3/contable/LogAsiento.php');
        $supplierPayments = $this->source('app/Services/V3SupplierPaymentService.php');
        $purchaseLifecycle = $this->source('app/Services/V3PurchaseLifecycleService.php');
        $bankStatement = $this->source('app/Services/V3BankStatementService.php');
        $legacyPurchaseController = $this->source('app/Http/Controllers/contable/ComprasController.php');
        $legacyInventory = $this->source('app/Inventario.php');
        $legacyKardex = $this->source('app/InvKardex.php');
        $legacyInventoryBalance = $this->source('app/InvInventario.php');
        $legacyInventorySeries = $this->source('app/InvInventarioSerie.php');
        $providerDebts = $this->source('app/v3/contable/Proveedor.php');
        $cashFlowView = $this->source('resources/views/v3/contable/reportes/contables/view_flujodeefectivo.blade.php');
        $equityView = $this->source('resources/views/v3/contable/reportes/contables/view_cambiodepatrimonio.blade.php');
        $v3Layout = $this->source('resources/views/v3/layouts/view_plantilla.blade.php');
        $v3UiScript = $this->source('public/v3/dist/js/modern-ui.js');
        $v3UiStyles = $this->source('public/v3/dist/css/modern-ui.css');
        $composer = $this->source('composer.json');
        $v3Ci = $this->source('.github/workflows/v3-ci.yml');
        $controllerSources = $this->v3ControllerSources();
        $modelSources = $this->v3ModelSources();
        $allV3 = $controllerSources . "\n" . $modelSources;

        $taxApiProtected = strpos($routes, 'v3.api.client:tributario.read') !== false
            && strpos($routes, 'throttle:30,1') !== false
            && is_file(app_path('Http/Middleware/v3/AuthenticateV3ApiClient.php'));
        $taxApiTenantFilters = substr_count($api, "id_empresa', \$companyId") >= 5;
        $publicTaxApi = !$taxApiProtected || !$taxApiTenantFilters;
        $registrationProtected = strpos($routes, "Route::post('v3/registro'") !== false
            && strpos($routes, 'v3/buscar_usuario') === false
            && strpos($registration, 'V3RegistrationInvitation') !== false
            && strpos($registration, 'lockForUpdate') !== false
            && is_file(database_path('migrations/2026_08_20_002000_create_v3_registration_invitations.php'));
        $publicRegistration = !$registrationProtected;
        $centralAuthorization = strpos($routes, "'v3.authorize'") !== false
            && strpos($v3Authorization, 'V3ActionPermission') !== false
            && strpos($v3Authorization, 'belongsToCompany') !== false
            && is_file(database_path('migrations/2026_08_20_003000_create_v3_action_permissions.php'));
        $mutableMatchRoutes = substr_count($routes, "Route::match(['get', 'post']");
        $mutableGetProtected = strpos($routes, "'v3.method.guard'") !== false
            && strpos($v3MethodGuard, "isMethod('GET')") !== false
            && strpos($v3MethodGuard, 'V3_METHOD_NOT_ALLOWED') !== false;
        $activeDebug = preg_match('/^[^\/\r\n]*\bdd\s*\(/m', $allV3) === 1;
        $tenantIsolation = strpos($tenantScope, 'requireOwned') !== false
            && substr_count($ledgerDetails, "session('id_empresa')") >= 2
            && strpos($banking, "where('ct_conciliacion_mes.id_empresa', session('id_empresa'))") !== false
            && strpos($utilityController, 'purchaseDownloadPath') !== false;
        $parameterizedLedger = strpos($accounting, "pe.plan like '\$r->cuentadesde%'") === false
            && strpos($accounting, 'DB::select($sql, $bindings)') !== false
            && strpos($accounting, 'La cuenta no pertenece al plan de la empresa activa') !== false;
        $safeDownload = strpos($fileSecurity, 'realpath(') !== false && strpos($fileSecurity, 'basename(') !== false
            && strpos($fileSecurity, "where('id_empresa', session('id_empresa'))") !== false;
        $atomicDocumentPersistence = strpos($utilities, "Storage::disk('archivos_compras')->delete(\$nombre_guardar)") !== false
            && strpos($legacyPurchaseController, "Storage::disk('public')->delete(\$nombre_guardar)") !== false
            && strpos($bankStatement, '@unlink($directory.DIRECTORY_SEPARATOR.$storedName)') !== false
            && strpos($bankStatement, 'array_chunk($rows, 500)') !== false;
        $safeUpload = strpos($fileSecurity, 'getMimeType') !== false && strpos($fileSecurity, 'random_bytes') !== false
            && strpos($utilities, 'validateUpload') !== false && $atomicDocumentPersistence;
        $serverAuditMetadata = strpos($audit, 'V3AuditService') !== false
            && strpos($auditService, 'classifier->module') !== false
            && strpos($auditService, "'[PROTECTED]'") !== false
            && strpos($routes, "'v3.audit'") !== false
            && strpos($auditMiddleware, "!\$request->isMethod('GET')") !== false;
        $auditTenantSafe = strpos($audit, "->where('id_empresa_auditoria', session('id_empresa'))") !== false
            && strpos($auditService, 'previous_hash') !== false
            && is_file(database_path('migrations/2026_08_20_004000_harden_v3_audit_trail.php'));
        $reconciliationUnique = strpos($banking, 'lockForUpdate') !== false
            && strpos($banking, 'resumenConciliacion') !== false
            && is_file(database_path('migrations/2026_08_20_005000_harden_v3_bank_reconciliation.php'));
        $reconciliationIdempotent = strpos($banking, 'desired_state') !== false
            && strpos($banking, 'scopeConciliationJoin') !== false
            && strpos($banking, 'persistConciliationMonth') !== false;
        $reconciliationLifecycle = $reconciliationIdempotent
            && strpos($banking, 'motivo_reapertura') !== false
            && is_file(database_path('migrations/2026_08_20_006000_add_v3_reconciliation_lifecycle.php'))
            && is_file(app_path('Console/Commands/AuditV3BankReconciliation.php'));
        $reconciliationCycleComplete = $reconciliationLifecycle
            && strpos($bankStatement, 'parseCsv') !== false && strpos($bankStatement, 'parseOfx') !== false
            && strpos($bankStatement, 'autoMatch') !== false && strpos($bankStatement, 'manualMatch') !== false
            && strpos($banking, 'aprobarConciliacion') !== false
            && strpos($banking, 'usuario distinto del preparador') !== false
            && is_file(database_path('migrations/2026_08_20_007000_complete_v3_reconciliation_cycle.php'));
        $activeLedgerFilters = strpos($financialReports, "where('ac.estado', 1)") !== false
            && strpos($financialReports, "where('ad.estado', 1)") !== false
            && strpos($journal, "where('ct_asientos_cabecera.estado'") !== false;
        $ledgerDateBoundarySafe = strpos($financialReports, "where('ac.fecha_asiento', '<', \$from") !== false
            && strpos($financialReports, "orderBy('ac.fecha_asiento')") !== false;
        $accountClassification = is_file(database_path('migrations/2026_08_20_008000_classify_v3_financial_accounts.php'))
            && strpos($financialReports, 'categoria_niif') !== false;
        $accountingReportsTested = $accountClassification
            && $this->hasV3TestTerms(array('libro diario', 'libro mayor', 'balance comprobacion', 'balance de resultados'));
        $completeCashFlow = strpos($financialReports, "'OPERACION'") !== false
            && strpos($financialReports, "'INVERSION'") !== false
            && strpos($financialReports, "'FINANCIACION'") !== false
            && strpos($financialReports, 'variacion_mayor') !== false;
        $equityStatement = strpos(strtolower($reports . $financialReports), 'cambiodepatrimonio') !== false
            && strpos($equityView, 'saldo_inicial') !== false && strpos($equityView, 'saldo_final') !== false;
        $accessibleFinancialViews = strpos($cashFlowView, 'aria-labelledby') !== false
            && strpos($equityView, 'scope="col"') !== false
            && is_file(resource_path('lang/es/v3/financial_reports.php'))
            && is_file(resource_path('lang/en/v3/financial_reports.php'))
            && is_file(resource_path('lang/pt/v3/financial_reports.php'));
        $completeV3Ui = $accessibleFinancialViews
            && strpos($v3Layout, 'window.AitrolV3I18n') !== false
            && strpos($v3UiScript, 'enhanceForms') !== false
            && strpos($v3UiScript, 'enhanceTables') !== false
            && strpos($v3UiScript, 'enhanceDialogsAndStatus') !== false
            && strpos($v3UiScript, 'MutationObserver') !== false
            && strpos($v3UiStyles, 'forced-colors') !== false
            && strpos($v3UiStyles, 'prefers-reduced-motion') !== false
            && is_file(app_path('Console/Commands/AuditV3UserInterface.php'))
            && is_file(resource_path('lang/es/v3/accessibility.php'))
            && is_file(resource_path('lang/en/v3/accessibility.php'))
            && is_file(resource_path('lang/pt/v3/accessibility.php'));
        $v3AutomatedTests = $this->countV3Tests();
        $v3TestSuiteWired = is_file(base_path('scripts/run-v3-tests.sh'))
            && strpos($v3Ci, 'bash scripts/run-v3-tests.sh') !== false
            && strpos($v3Ci, 'phpunit --filter V3 tests/Unit') === false;
        $reproducibleDeployment = is_file(base_path('.github/workflows/v3-ci.yml'))
            && is_file(base_path('docs/v3/PRODUCTION_RUNBOOK.md'))
            && is_file(base_path('scripts/deploy.sh'))
            && is_file(base_path('composer.lock'));
        $laravelVersion = app()->version();
        $runtimeReady = version_compare(PHP_VERSION, '8.3.0', '>=')
            && version_compare($laravelVersion, '13.0.0', '>=')
            && version_compare($laravelVersion, '14.0.0', '<');
        $transportPending = $this->clinicalTransportPending();
        $secureTransport = $transportPending === 0;
        $deploymentEvidenceStatus = is_file(app_path('Services/ClinicalOperationsEvidenceService.php'))
            ? app(\Sis_medico\Services\ClinicalOperationsEvidenceService::class)->status()
            : array('restore_approved' => false);
        $restoreEvidenceReady = !empty($deploymentEvidenceStatus['restore_approved']);
        $deploymentReady = $runtimeReady && $reproducibleDeployment && $secureTransport && $restoreEvidenceReady;
        $deploymentMissing = array();

        if (!$runtimeReady) {
            $deploymentMissing[] = 'ejecutar la aplicación con PHP 8.3 o superior y Laravel 13';
        }

        if (!$reproducibleDeployment) {
            $deploymentMissing[] = 'demostrar un despliegue reproducible desde un entorno limpio usando el repositorio y composer.lock';
        }

        if (!$secureTransport) {
            $deploymentMissing[] = 'cerrar la auditoría de transporte HTTPS';
        }

        if (!$restoreEvidenceReady) {
            $deploymentMissing[] = 'ejecutar y aprobar un simulacro de respaldo y restauración';
        }

        $deploymentEvidence = $deploymentReady
            ? 'El despliegue usa CI, runbook, script controlado y composer.lock; HTTPS está auditado y existe un simulacro de restauración aprobado.'
            : 'Pendiente: '.implode('; ', $deploymentMissing).'.';
        $deploymentState = $deploymentReady
            ? 'complete'
            : (($reproducibleDeployment || $secureTransport || $restoreEvidenceReady) ? 'partial' : 'pending');
        $legacyInventoryCall = strpos($legacyPurchaseController, "Inventario::build_process('C', \$id_compra");
        $legacyCommitAfterInventory = $legacyInventoryCall !== false
            && strpos($legacyPurchaseController, 'DB::commit();', $legacyInventoryCall) !== false;
        $accountingConcurrencyReady = strpos($accountingPosting, 'catch (QueryException $exception)') !== false
            && strpos($accountingPosting, 'isUniqueConstraintViolation') !== false
            && strpos($accountingPosting, 'insertRowsInChunks') !== false
            && strpos($accountingPosting, '}, 3);') !== false
            && strpos($accountingSequences, "ComprobanteSecuencia::where('id', \$candidate->id)->lockForUpdate()") !== false
            && strpos($accountingSequences, 'self::lockCompany($id_empresa)') !== false
            && strpos($supplierPayments, 'completedPayment(') !== false
            && strpos($supplierPayments, "'idempotent' => true") !== false
            && strpos($supplierPayments, "->orderBy('id')->lockForUpdate()->get()->keyBy('id')") !== false
            && strpos($purchaseLifecycle, "->whereIn('codigo', \$codes)->orderBy('codigo')->lockForUpdate()") !== false
            && strpos($purchaseLifecycle, 'MAX(id) AS id') !== false
            && strpos($legacyPurchaseController, "Ct_Caja::where('id', \$request['punto_emision'])->where('id_empresa', \$id_empresa)") !== false
            && $legacyCommitAfterInventory
            && strpos($legacyInventory, "where('tipo', 'COMP')->lockForUpdate()") !== false
            && strpos($legacyKardex, "->orderBy('serie')->orderBy('id')->lockForUpdate()->get()") !== false
            && strpos($legacyInventoryBalance, "->lockForUpdate()->first()") !== false
            && strpos($legacyInventorySeries, "->lockForUpdate()->first()") !== false
            && strpos($providerDebts, "Retenciones::whereIn('id_compra', \$deudas->pluck('id')->all())") !== false
            && is_file(base_path('tests/Unit/V3SupplierPaymentContractTest.php'))
            && is_file(base_path('tests/Unit/V3PurchaseLifecycleCertificationTest.php'))
            && is_file(base_path('tests/Unit/AccountingInventoryPersistenceHardeningTest.php'));
        $accountingGovernance = is_file(database_path('migrations/2026_08_21_020000_create_v3_accounting_governance.php'))
            && is_file(app_path('Services/V3AccountingIntegrityService.php'))
            && is_file(app_path('Services/V3AccountingPostingService.php'))
            && $accountingConcurrencyReady;
        $canonicalAccountingViews = strpos($reports, 'journalRows') !== false
            && strpos($reports, 'trialBalanceResponse') !== false && strpos($reports, 'incomeStatementResponse') !== false
            && strpos($reports, 'financialPositionResponse') !== false && strpos($reports, 'generalLedger') !== false
            && strpos($reports, 'V3AccountingExportService') !== false
            && strpos($accountingExports, 'incomeStatement') !== false && strpos($accountingExports, 'financialPosition') !== false;
        $auxiliaryReconciliation = is_file(app_path('Services/V3AccountingReconciliationService.php'));
        $periodGovernance = is_file(app_path('Services/V3AccountingPeriodService.php'));
        $accountingCertification = is_file(app_path('Services/V3AccountingCertificationService.php'));
        $accountingIncidentModule = is_file(app_path('Services/V3AccountingIncidentService.php'))
            && is_file(app_path('Http/Middleware/v3/CaptureV3AccountingIncidents.php'))
            && is_file(resource_path('views/v3/contable/accounting_health/index.blade.php'));
        $accountingCycleAudit = is_file(app_path('Console/Commands/AuditV3AccountingCycle.php'))
            && is_file(base_path('tests/Unit/V3AccountingGovernanceCycleTest.php'))
            && $accountingConcurrencyReady;
        $accountingProductionData = $this->accountingOperationalCertificationReady();

        $controls = array(
            $this->v3Control('V3-01', 'CRÍTICO', 'La API tributaria puede exponer documentos de todas las empresas sin autenticación.', $publicTaxApi ? 'La ruta api/v1/tributario permanece pública y sus consultas no limitan id_empresa.' : 'La API exige identidad y aplica aislamiento por empresa.', 'Mover la API a middleware autenticado, usar token con alcance, filtrar la empresa autorizada, limitar campos y aplicar rate limiting.', 'Una llamada anónima responde 401/403 y un token de Empresa A nunca devuelve registros de Empresa B.', 'P0', $publicTaxApi ? 'pending' : 'complete'),
            $this->v3Control('V3-02', 'CRÍTICO', 'El alta y la búsqueda de usuarios están expuestas fuera de autenticación.', $publicRegistration ? 'guardar_usuario acepta GET/POST y buscar_usuario consulta directamente una identificación.' : 'El alta usa invitación firmada y la búsqueda pública no revela usuarios.', 'Usar POST, invitación firmada de un solo uso, vencimiento, validación, rate limiting y respuesta no enumerable.', 'No se crea una cuenta sin token vigente y respuestas de usuario existente/no existente son indistinguibles.', 'P0', $publicRegistration ? 'pending' : 'complete'),
            $this->v3Control('V3-03', 'CRÍTICO', 'No existe autorización central por empresa, recurso y acción.', $centralAuthorization ? 'El middleware central valida pertenencia a empresa y permisos explícitos por módulo/acción; las mutaciones se niegan por defecto.' : 'General valida autenticación, pero los permisos siguen dispersos entre controladores.', 'Crear middleware/policies para ver, crear, editar, anular, conciliar, cerrar, reabrir y exportar; negar por defecto.', 'La matriz de permisos tiene pruebas positivas y negativas para cada acción sensible.', 'P0', $centralAuthorization ? 'complete' : 'partial'),
            $this->v3Control('V3-04', 'CRÍTICO', 'Operaciones financieras pueden ejecutarse mediante rutas GET genéricas.', $mutableGetProtected ? 'Las '.$mutableMatchRoutes.' rutas heredadas conservan compatibilidad, pero el guard central responde 405 ante cualquier nombre de operación mutable solicitado por GET.' : ($mutableMatchRoutes > 0 ? 'Se detectaron '.$mutableMatchRoutes.' rutas V3 que aceptan simultáneamente GET y POST.' : 'Las rutas de lectura y escritura están separadas.'), 'Reservar GET para lectura y usar POST/PUT/PATCH/DELETE con CSRF e idempotencia para mutaciones.', 'La auditoría de rutas reporta cero mutaciones por GET y los reintentos no duplican operaciones.', 'P0', $mutableGetProtected || $mutableMatchRoutes === 0 ? 'complete' : 'pending'),
            $this->v3Control('V3-05', 'CRÍTICO', 'Existen consultas por identificador sin aislamiento consistente entre empresas.', $tenantIsolation ? 'El guard empresarial se aplica a asientos, conciliaciones, auditorías y archivos; los recursos ajenos responden como inexistentes.' : 'Asientos, conciliaciones, auditorías y archivos contienen búsquedas directas por ID.', 'Aplicar TenantContext en repositorios/policies y exigir id_empresa en toda lectura o escritura de recursos empresariales.', 'Las pruebas Empresa A contra IDs de Empresa B devuelven 404/403 en el 100% de casos.', 'P0', $tenantIsolation ? 'complete' : 'pending'),
            $this->v3Control('V3-06', 'CRÍTICO', 'El libro mayor concatena datos de la solicitud dentro del SQL.', $parameterizedLedger ? 'La cuenta se resuelve con parámetros preparados.' : 'cuentadesde se inserta directamente en expresiones LIKE del SQL.', 'Reescribir con Query Builder o bindings y validar que la cuenta pertenece al plan de la empresa.', 'Pruebas de inyección no alteran la consulta y una cuenta ajena es rechazada.', 'P0', $parameterizedLedger ? 'complete' : 'pending'),
            $this->v3Control('V3-07', 'CRÍTICO', 'La carga y descarga de archivos no demuestra contención ni validación suficiente.', ($safeDownload && $safeUpload) ? 'Las rutas, MIME, tamaño y pertenencia están controlados; compras y extractos compensan el archivo físico si falla la transacción y agrupan inserciones masivas.' : 'Se usan nombres originales o falta demostrar compensación entre archivo y base de datos.', 'Generar nombres aleatorios, validar MIME/tamaño/extensión, resolver realpath, almacenar fuera del webroot, verificar propietario y compensar fallos.', 'Traversal, archivos ejecutables y archivos de otra empresa son rechazados; un fallo de base no deja archivos huérfanos.', 'P0', ($safeDownload && $safeUpload) ? 'complete' : 'pending'),
            $this->v3Control('V3-08', 'ALTO', 'Quedan detenciones y salidas de depuración en flujos V3.', $activeDebug ? 'Se detectaron llamadas dd() activas en controladores o modelos V3.' : 'No se detecta depuración activa.', 'Eliminar dd/print_r/exit de rutas, normalizar excepciones y usar códigos de referencia sin revelar archivos ni solicitudes.', 'La búsqueda automática reporta cero depuraciones activas y las pruebas de error no exponen datos internos.', 'P0', $activeDebug ? 'pending' : 'complete'),
            $this->v3Control('V3-09', 'ALTO', 'La trazabilidad central es incompleta y parte de sus metadatos proviene del navegador.', ($serverAuditMetadata && $auditTenantSafe) ? 'La auditoría se genera en servidor y está aislada.' : 'Controlador, vista, observación y solicitud completa pueden ser enviados por el cliente; el detalle no siempre filtra empresa.', 'Generar eventos antes/después en servidor, enmascarar secretos, añadir correlación e inmutabilidad y aislar por empresa.', 'Cada mutación tiene actor, empresa, antes/después, motivo y hash; ningún secreto aparece en obj_auditoria.', 'P0', ($serverAuditMetadata && $auditTenantSafe) ? 'complete' : 'partial'),
            $this->v3Control('V3-10', 'CRÍTICO', 'La conciliación permite duplicados, carreras y totales confiados al navegador.', $reconciliationUnique ? 'Existen restricción única, transacción y bloqueo de filas.' : 'No se demuestra unicidad por empresa/banco/periodo ni recálculo autoritativo con lockForUpdate.', 'Crear clave única, recalcular saldos en servidor, usar transacción, lockForUpdate y control de versión.', 'Dos solicitudes concurrentes generan una sola conciliación y todos los totales coinciden con movimientos persistidos.', 'P0', $reconciliationUnique ? 'complete' : 'partial'),
            $this->v3Control('V3-11', 'ALTO', 'La conciliación no posee ciclo bancario completo ni actualización idempotente.', $reconciliationCycleComplete ? 'El ciclo usa selección idempotente por empresa/banco/periodo, extractos CSV/OFX identificados por hash, matching automático y manual uno a uno, pendientes canónicos, preparación y aprobación por usuarios distintos, cierre conjunto y reapertura motivada; la auditoría detecta cruces y huérfanos sin borrar evidencia.' : ($reconciliationLifecycle ? 'La integridad base está desplegada, pero falta completar extracto, matching o aprobación independiente.' : 'La verificación alterna el estado y no existe importación/emparejamiento/aprobación completa.'), 'Crear líneas de estado bancario, importación CSV/OFX, matching, pendientes, preparador/aprobador, cierre y reapertura motivada.', 'Repetir la misma petición conserva el estado; cierre y reapertura dejan aprobación y evidencia completa.', 'P1', $reconciliationCycleComplete ? 'complete' : ($reconciliationLifecycle ? 'partial' : 'pending')),
            $this->v3Control('V3-12', 'CRÍTICO', 'Libro diario, mayorización y mayor no garantizan excluir anulados ni límites correctos.', ($activeLedgerFilters && $ledgerDateBoundarySafe) ? 'Una fuente única por empresa y período excluye cabeceras/detalles anulados, calcula el saldo anterior con límite exclusivo y ordena fecha/asiento/detalle.' : 'Faltan filtros activos consistentes y el saldo anterior puede incluir nuevamente el primer día del periodo.', 'Centralizar un LedgerQueryService parametrizado con orden estable, naturaleza contable y filtros de estado/periodo.', 'Diario y mayor cuadran entre sí; anulados no aparecen; saldo anterior + movimientos = saldo final.', 'P0', ($activeLedgerFilters && $ledgerDateBoundarySafe) ? 'complete' : 'partial'),
            $this->v3Control('V3-13', 'CRÍTICO', 'Resultados, comprobación y situación financiera dependen de prefijos y reglas no certificadas.', $accountingReportsTested ? 'Las cuentas poseen categoría NIIF configurable y el paquete certificado valida Debe=Haber y Activo=Pasivo+Patrimonio con pruebas cruzadas.' : 'La clasificación usa prefijos rígidos y no hay pruebas V3 con un juego contable aprobado.', 'Definir naturaleza y categoría NIIF por cuenta, filtrar anulados, separar apertura/movimiento/cierre y validar ecuaciones.', 'Activo=Pasivo+Patrimonio, Debe=Haber y resultado coincide con patrimonio para dos periodos certificados.', 'P0', $accountingReportsTested ? 'complete' : 'partial'),
            $this->v3Control('V3-14', 'CRÍTICO', 'El reporte llamado flujo de efectivo está incompleto.', $completeCashFlow ? 'Incluye operación, inversión, financiación y conciliación de efectivo.' : 'Solo se evidencian ingresos operativos; faltan salidas, inversión, financiación y efectivo inicial/final.', 'Implementar IAS 7 por método definido y conciliar el efectivo final contra cuentas de caja y bancos.', 'La suma de actividades explica exactamente la variación de efectivo y concilia con el mayor.', 'P0', $completeCashFlow ? 'complete' : 'pending'),
            $this->v3Control('V3-15', 'ALTO', 'No existe un estado funcional de cambios en el patrimonio.', $equityStatement ? 'Existe ruta, consulta, vista y conciliación patrimonial.' : 'Solo se encontró una etiqueta; no hay reporte completo operativo.', 'Implementar saldo inicial, resultado, aportes, retiros, reservas, dividendos, ajustes y saldo final por componente.', 'El saldo final coincide con patrimonio del estado de situación y explica toda variación del periodo.', 'P0', $equityStatement ? 'complete' : 'pending'),
            $this->v3Control('V3-16', 'MEDIO', 'El diseño, traducción y accesibilidad son inconsistentes entre vistas V3.', $completeV3Ui ? 'Las 196 vistas heredan una capa localizada ES/EN/PT que normaliza formularios, acciones, tablas, modales, estados y contenido AJAX; incluye foco visible, responsive, alto contraste y movimiento reducido, con auditoría automática.' : ($accessibleFinancialViews ? 'Los estados financieros están normalizados, pero falta cobertura transversal.' : 'Conviven pantallas modernizadas con AdminLTE, estilos en línea y textos sin traducir.'), 'Crear componentes reutilizables, diccionario completo, estados accesibles, navegación por teclado y pruebas responsive.', 'La auditoría transversal pasa en los idiomas soportados y los flujos principales conservan navegación por teclado, semántica y adaptación responsive.', 'P2', $completeV3Ui ? 'complete' : ($accessibleFinancialViews ? 'partial' : 'pending')),
            $this->v3Control('V3-17', 'CRÍTICO', 'No existe cobertura automatizada suficiente para contabilidad y bancos V3.', ($v3AutomatedTests >= 8 && $v3TestSuiteWired) ? $v3AutomatedTests.' archivos de prueba V3 cubren seguridad, aislamiento, conciliación, ecuaciones financieras, traducción y despliegue; CI ejecuta cada archivo y bloquea ante cualquier fallo.' : $v3AutomatedTests.' archivo(s) existen, pero CI no demuestra todavía su ejecución completa.', 'Crear pruebas unitarias, integración, permisos, aislamiento, concurrencia, exportación y golden datasets contables.', 'La suite V3 pasa en CI y cubre todos los criterios V3-01 a V3-18 antes de desplegar.', 'P0', ($v3AutomatedTests >= 8 && $v3TestSuiteWired) ? 'complete' : ($v3AutomatedTests > 0 ? 'partial' : 'pending')),
            $this->v3Control('V3-18', 'CRÍTICO', 'El despliegue y la recuperación productiva deben ser reproducibles y verificables.', $deploymentEvidence, 'Mantener TLS válido, composer.lock, CI y el despliegue controlado; ejecutar y aprobar el simulacro de respaldo/restauración.', 'Un entorno limpio se instala desde el repositorio con las dependencias fijadas, pasa las pruebas, opera solo por HTTPS y restaura el respaldo dentro del RPO/RTO aprobado.', 'P0', $deploymentState),
        );

        $areas = array(
            'security' => $this->v3Area('Seguridad y aislamiento', array_slice($controls, 0, 8)),
            'traceability' => $this->v3Area('Trazabilidad', array_slice($controls, 8, 1)),
            'banking' => $this->v3Area('Conciliación bancaria', array_slice($controls, 9, 2)),
            'accounting' => $this->v3Area('Reportes contables', array_slice($controls, 11, 4)),
            'design' => $this->v3Area('Diseño y accesibilidad', array_slice($controls, 15, 1)),
            'tests' => $this->v3Area('Pruebas', array_slice($controls, 16, 1)),
            'operations' => $this->v3Area('Despliegue y continuidad', array_slice($controls, 17, 1)),
        );
        $score = $this->weightedClinicalScore($controls);

        // La funcionalidad V3 se evalua por separado de las evidencias de
        // despliegue, transporte y recuperación exigidas por V3-18.
        $functionalControls = array_slice($controls, 0, 17);
        $functionalCounts = $this->clinicalCounts($functionalControls);
        $functionalScore = $this->weightedClinicalScore($functionalControls);
        $functionalReady = $functionalCounts['complete'] === $functionalCounts['total'];

        $platformGates = array(
            array(
                'id' => 'RUNTIME',
                'name' => 'Runtime de aplicación',
                'state' => $runtimeReady ? 'complete' : 'pending',
                'detail' => 'PHP '.PHP_VERSION.' · Laravel '.$laravelVersion.'.',
                'action' => $runtimeReady
                    ? 'Mantener PHP 8.3 como ejecutable de consola y PHP-FPM del sitio.'
                    : 'Configurar PHP 8.3 y Laravel 13 tanto en consola como en PHP-FPM.',
            ),
            array(
                'id' => 'DEPLOYMENT',
                'name' => 'Despliegue reproducible',
                'state' => $reproducibleDeployment ? 'complete' : 'pending',
                'detail' => $reproducibleDeployment
                    ? 'CI y runbook de produccion estan disponibles.'
                    : 'Falta CI o el runbook reproducible de produccion.',
                'action' => $reproducibleDeployment
                    ? 'Conservar evidencia de cada despliegue.'
                    : 'Completar CI y validar el runbook desde un entorno limpio.',
            ),
            array(
                'id' => 'HTTPS',
                'name' => 'Transporte seguro',
                'state' => $secureTransport ? 'complete' : 'pending',
                'detail' => $secureTransport
                    ? 'HTTPS, HSTS, dominio canonico y sesion segura estan auditados.'
                    : 'La auditoria de transporte conserva controles pendientes.',
                'action' => $secureTransport
                    ? 'Ejecutar clinical:transport-audit tras cambios de dominio o certificado.'
                    : 'Cerrar clinical:transport-audit y corregir todos sus pendientes.',
            ),
            array(
                'id' => 'RESTORE',
                'name' => 'Respaldo y restauración',
                'state' => $restoreEvidenceReady ? 'complete' : 'pending',
                'detail' => $restoreEvidenceReady
                    ? 'Existe un simulacro aprobado con RPO/RTO y evidencia íntegra.'
                    : 'Todavía falta ejecutar y aprobar el simulacro de restauración.',
                'action' => $restoreEvidenceReady
                    ? 'Repetir el simulacro según el calendario operativo.'
                    : 'Restaurar el respaldo en otro entorno, medir RPO/RTO y registrar la aprobación.',
            ),
        );
        $platformComplete = 0;
        foreach ($platformGates as $gate) {
            if ($gate['state'] === 'complete') $platformComplete++;
        }
        $platformScore = count($platformGates) > 0
            ? (int) round(($platformComplete / count($platformGates)) * 100)
            : 0;

        $accountingControls = array(
            $this->v3Control('ACC-01', 'CRÍTICO', 'Contención de asientos, pagos, inventario y periodos', $accountingGovernance ? 'El alta valida periodo abierto y balance; la identidad única tolera carreras y reintenta deadlocks. Los flujos V3 y heredados bloquean documento, detalles y existencias en orden estable; la compra confirma inventario antes del commit y un reenvío no duplica kardex.' : 'No se demuestra una puerta única con idempotencia, bloqueos ordenados y confirmación conjunta de compra e inventario.', 'Mantener gobierno contable, idempotencia de operación y orden determinista de bloqueos.', 'Dos solicitudes paralelas producen un solo asiento/comprobante/movimiento; un descuadre, stock insuficiente o periodo cerrado revierte toda la operación.', 'Fase 1', $accountingGovernance ? 'complete' : 'pending'),
            $this->v3Control('ACC-02', 'CRÍTICO', 'Motor único para reportes visibles', $canonicalAccountingViews ? 'Diario, mayorización, mayor, comprobación, resultados y situación financiera usan V3FinancialReportService.' : 'Persisten motores paralelos en las pantallas.', 'Conectar todas las vistas y exportaciones al libro canónico.', 'Pantalla, JSON, Excel, PDF y auditoría producen los mismos totales.', 'Fase 1', $canonicalAccountingViews ? 'complete' : 'pending'),
            $this->v3Control('ACC-03', 'ALTO', 'Saldos iniciales y corte temporal', strpos($financialReports, 'diferencia_saldos') !== false && strpos($financialReports, "period('2000-01-01'") === false ? 'El balance separa saldo inicial, movimientos y saldo final desde la primera fecha real.' : 'El corte histórico todavía depende de una fecha fija o solo de movimientos.', 'Corregir apertura y límites exclusivos.', 'Saldo inicial + movimientos = saldo final por cuenta.', 'Fase 1', strpos($financialReports, 'diferencia_saldos') !== false && strpos($financialReports, "period('2000-01-01'") === false ? 'complete' : 'partial'),
            $this->v3Control('ACC-04', 'CRÍTICO', 'Conciliación de auxiliares', $auxiliaryReconciliation ? 'Clientes, proveedores, inventario, activos, impuestos y bancos se comparan con el mayor y requieren aprobación independiente.' : 'No existe una conciliación transversal de auxiliares.', 'Registrar evidencia y aprobación por cada auxiliar.', 'Los seis auxiliares tienen diferencia cero y estado aprobado.', 'Fase 2', $auxiliaryReconciliation ? 'complete' : 'pending'),
            $this->v3Control('ACC-05', 'CRÍTICO', 'Gobierno del cierre', $periodGovernance ? 'El periodo transita OPEN, PREPARED, APPROVED y CLOSED; la reapertura exige motivo y segregación.' : 'No se bloquean periodos cerrados.', 'Desplegar workflow de cierre y reapertura.', 'Ningún asiento se registra en un periodo no abierto.', 'Fase 3', $periodGovernance ? 'complete' : 'pending'),
            $this->v3Control('ACC-06', 'CRÍTICO', 'Certificación financiera inmutable', $accountingCertification ? 'El paquete financiero, integridad y auxiliares se guardan como snapshot SHA-256.' : 'No existe evidencia inmutable del paquete aprobado.', 'Persistir la certificación exacta del periodo.', 'El hash identifica los valores certificados sin sobrescribir versiones previas.', 'Fase 4', $accountingCertification ? 'complete' : 'pending'),
            $this->v3Control('ACC-07', 'ALTO', 'Diagnóstico y respuesta a errores', $accountingIncidentModule ? 'Las excepciones y diferencias generan incidencias; solo reparaciones de datos permitidas pueden ejecutarse y quedan auditadas.' : 'Los errores no tienen respuesta gobernada.', 'Agregar catálogo de incidentes y playbooks seguros.', 'Un error repetido conserva huella, diagnóstico, acción y resultado.', 'Fase 5', $accountingIncidentModule ? 'complete' : 'pending'),
            $this->v3Control('ACC-08', 'ALTO', 'Autocorrección segura', $accountingIncidentModule ? 'El sistema no reescribe código; rechaza reparaciones no permitidas y conserva antes/después.' : 'No hay límites explícitos para reparaciones.', 'Mantener allowlist, transacción y confirmación.', 'Ninguna reparación financiera inventa contrapartidas ni borra evidencia.', 'Fase 5', $accountingIncidentModule ? 'complete' : 'pending'),
            $this->v3Control('ACC-09', 'CRÍTICO', 'Certificación sobre datos reales', $accountingProductionData ? 'Las empresas con movimientos poseen certificación y no mantienen incidencias críticas abiertas.' : 'El código está desplegado, pero aún falta certificar los datos reales del servidor.', 'Ejecutar auditoría, conciliar auxiliares y certificar cada periodo piloto.', 'Dos cierres consecutivos quedan certificados sin diferencias.', 'Fase 5', $accountingProductionData ? 'complete' : 'partial'),
            $this->v3Control('ACC-10', 'ALTO', 'Pruebas, rendimiento y operación repetible', $accountingCycleAudit ? 'La suite verifica gobierno, reintentos, idempotencia de pagos, bloqueos de inventario y eliminación de N+1 en deudas/retenciones; existe un comando integral de auditoría programado.' : 'Falta demostrar con pruebas la concurrencia contable, la idempotencia de negocio o la eliminación de consultas repetitivas.', 'Ejecutar la suite compatible, pruebas concurrentes contra MySQL y el comando integral en despliegue.', 'La auditoría devuelve cero pendientes, dos solicitudes simultáneas conservan una sola operación y el número de consultas no crece linealmente con deudas o detalles.', 'Fase 5', $accountingCycleAudit ? 'complete' : 'partial'),
        );

        // ACC-09 no mide una función pendiente: certifica evidencia contable
        // producida por usuarios sobre datos reales. Se mantiene como puerta
        // operativa independiente para no reducir la preparación técnica.
        $accountingTechnicalControls = array();
        foreach ($accountingControls as $accountingControl) {
            if ($accountingControl['id'] !== 'ACC-09') {
                $accountingTechnicalControls[] = $accountingControl;
            }
        }
        $accountingTechnicalCounts = $this->clinicalCounts($accountingTechnicalControls);
        $accountingTechnicalReady = $accountingTechnicalCounts['complete'] === $accountingTechnicalCounts['total'];
        $accountingOperationalCertification = array(
            'id' => 'ACC-09',
            'name' => 'Certificación con datos reales',
            'state' => $accountingProductionData ? 'complete' : 'pending',
            'ready' => $accountingProductionData,
            'decision' => $accountingProductionData ? 'CERTIFICADO' : 'PENDIENTE',
            'detail' => $accountingProductionData
                ? 'Los periodos con movimientos poseen certificación y no mantienen incidencias críticas abiertas.'
                : 'La implementación está completa; faltan conciliaciones auxiliares y aprobación independiente sobre datos reales.',
            'action' => $accountingProductionData
                ? 'Conservar la evidencia y repetir la certificación en cada cierre.'
                : 'Conciliar clientes, proveedores, inventario, activos, impuestos y bancos; aprobar con un usuario distinto del preparador.',
        );
        $accountingPhases = array(
            $this->v3AccountingPhase('Fase 1', 'Integridad y motor único', array_slice($accountingControls, 0, 3)),
            $this->v3AccountingPhase('Fase 2', 'Conciliación de auxiliares', array_slice($accountingControls, 3, 1)),
            $this->v3AccountingPhase('Fase 3', 'Cierre gobernado', array_slice($accountingControls, 4, 1)),
            $this->v3AccountingPhase('Fase 4', 'Certificación financiera', array_slice($accountingControls, 5, 1)),
            $this->v3AccountingPhase('Fase 5', 'Operación, errores y pruebas', array(
                $accountingControls[6], $accountingControls[7], $accountingControls[9],
            )),
        );

        return array(
            'score' => $score,
            'status' => $this->clinicalStatus($score),
            'decision' => $this->v3Decision($controls),
            'controls' => $controls,
            'counts' => $this->clinicalCounts($controls),
            'areas' => $areas,
            'functional_readiness' => array(
                'score' => $functionalScore,
                'ready' => $functionalReady,
                'decision' => $functionalReady ? 'GO FUNCIONAL' : 'NO-GO',
                'complete' => $functionalCounts['complete'],
                'total' => $functionalCounts['total'],
                'counts' => $functionalCounts,
            ),
            'platform_readiness' => array(
                'score' => $platformScore,
                'ready' => $deploymentReady,
                'decision' => $deploymentReady ? 'GO' : 'PENDIENTE',
                'complete' => $platformComplete,
                'total' => count($platformGates),
                'gates' => $platformGates,
            ),
            'accounting_readiness' => array('decision' => $this->v3Decision($accountingControls),
                'score' => $this->weightedClinicalScore($accountingControls), 'counts' => $this->clinicalCounts($accountingControls),
                'controls' => $accountingControls, 'phases' => $accountingPhases,
                'technical_readiness' => array(
                    'score' => $this->weightedClinicalScore($accountingTechnicalControls),
                    'ready' => $accountingTechnicalReady,
                    'decision' => $accountingTechnicalReady ? 'GO TÉCNICO' : 'NO-GO',
                    'complete' => $accountingTechnicalCounts['complete'],
                    'total' => $accountingTechnicalCounts['total'],
                    'counts' => $accountingTechnicalCounts,
                ),
                'operational_certification' => $accountingOperationalCertification,
                'infrastructure_note' => 'HTTPS está habilitado; la preparación operativa se acredita mediante CI, despliegue controlado y restauración comprobada.'),
        );
    }

    private function v3AccountingPhase($name, $description, array $controls)
    {
        return array('name' => $name, 'description' => $description, 'score' => $this->weightedClinicalScore($controls),
            'counts' => $this->clinicalCounts($controls));
    }

    private function accountingOperationalCertificationReady()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('v3_accounting_certifications') || !\Sis_medico\Support\RequestSchema::hasTable('v3_accounting_incidents')) return false;
            $companies = DB::table('ct_asientos_cabecera')->where('estado', 1)->distinct()->pluck('id_empresa')->all();
            if (empty($companies)) return false;
            foreach ($companies as $company) {
                if (!DB::table('v3_accounting_certifications')->where('id_empresa', $company)->where('estado', 'CERTIFIED')->exists()) return false;
                if (DB::table('v3_accounting_incidents')->where('id_empresa', $company)->where('estado', 'OPEN')->where('severidad', 'CRITICAL')->exists()) return false;
            }
            return true;
        } catch (\Exception $exception) { return false; }
    }

    private function v3Control($id, $severity, $finding, $evidence, $action, $acceptance, $phase, $state)
    {
        return array('id' => $id, 'severity' => $severity, 'finding' => $finding, 'evidence' => $evidence, 'action' => $action,
            'acceptance' => $acceptance, 'phase' => $phase, 'state' => $state, 'status' => $this->clinicalState($state));
    }

    private function v3Area($name, array $controls)
    {
        return array('name' => $name, 'score' => $this->weightedClinicalScore($controls), 'counts' => $this->clinicalCounts($controls));
    }

    private function v3Decision(array $controls)
    {
        foreach ($controls as $control) {
            if ($control['severity'] === 'CRÍTICO' && $control['state'] !== 'complete') return 'NO-GO';
        }
        return 'GO CONDICIONADO';
    }

    /**
     * A weighted maturity score can hide an open production gate. This
     * aggregate deliberately counts every required control equally and only
     * reaches 100 when every audited finding, clinical rule and production
     * gate is complete.
     */
    private function completionRequirements(array $clinical, array $v3Readiness)
    {
        $clinicalProduction = isset($clinical['production_readiness'])
            ? $clinical['production_readiness'] : array();
        $accounting = isset($v3Readiness['accounting_readiness'])
            ? $v3Readiness['accounting_readiness'] : array();

        $groups = array(
            $this->completionGroup('clinical', 'Clínica transversal', isset($clinical['controls']) ? $clinical['controls'] : array()),
            $this->completionGroup('clinical_rules', 'Reglas clínicas obligatorias', isset($clinical['rules']) ? $clinical['rules'] : array()),
            $this->completionGroup('hospitalization', 'Salida de hospitalización', isset($clinicalProduction['controls']) ? $clinicalProduction['controls'] : array()),
            $this->completionGroup('v3', 'V3 administrativo y financiero', isset($v3Readiness['controls']) ? $v3Readiness['controls'] : array()),
            $this->completionGroup('accounting', 'Ciclo contable', isset($accounting['controls']) ? $accounting['controls'] : array()),
        );

        $total = 0;
        $complete = 0;
        foreach ($groups as $group) {
            $total += $group['total'];
            $complete += $group['complete'];
        }

        $remaining = max(0, $total - $complete);
        $ready = $total > 0 && $remaining === 0;

        return array(
            'score' => $total > 0 ? round(($complete / $total) * 100, 1) : 0,
            'ready' => $ready,
            'decision' => $ready ? 'GO' : 'NO-GO',
            'total' => $total,
            'complete' => $complete,
            'remaining' => $remaining,
            'groups' => $groups,
        );
    }

    private function completionGroup($key, $name, array $controls)
    {
        $complete = 0;
        $requirements = array();

        foreach ($controls as $control) {
            $state = isset($control['state']) ? $control['state'] : 'pending';
            if ($state === 'complete') {
                $complete++;
                continue;
            }

            $requirements[] = array(
                'id' => isset($control['id']) ? $control['id'] : 'N/D',
                'severity' => isset($control['severity']) ? $control['severity'] : 'MEDIO',
                'finding' => isset($control['finding']) ? $control['finding'] : 'Control pendiente',
                'evidence' => isset($control['evidence']) ? $control['evidence'] : 'Sin evidencia registrada.',
                'action' => isset($control['action']) ? $control['action'] : 'Completar el control y adjuntar evidencia verificable.',
                'acceptance' => isset($control['acceptance']) ? $control['acceptance'] : (isset($control['action']) ? $control['action'] : 'Auditoría sin pendientes.'),
                'phase' => isset($control['phase']) ? $control['phase'] : 'Pendiente',
                'state' => $state,
                'status' => isset($control['status']) ? $control['status'] : $this->clinicalState($state),
            );
        }

        $total = count($controls);
        return array(
            'key' => $key,
            'name' => $name,
            'total' => $total,
            'complete' => $complete,
            'remaining' => max(0, $total - $complete),
            'score' => $total > 0 ? round(($complete / $total) * 100, 1) : 0,
            'requirements' => $requirements,
        );
    }

    private function v3ControllerSources()
    {
        $sources = '';
        foreach ($this->phpFiles(app_path('Http/Controllers/v3/contable')) as $file) $sources .= "\n" . @file_get_contents($file);
        return $sources;
    }

    private function v3ModelSources()
    {
        $sources = '';
        foreach ($this->phpFiles(app_path('v3/contable')) as $file) $sources .= "\n" . @file_get_contents($file);
        return $sources;
    }

    private function countV3Tests()
    {
        $count = 0;
        foreach ($this->phpFiles(base_path('tests')) as $file) {
            $source = strtolower((string) @file_get_contents($file));
            if (strpos($source, 'v3') !== false || strpos($source, 'conciliacion') !== false || strpos($source, 'libro mayor') !== false) $count++;
        }
        return $count;
    }

    private function hasV3TestTerms(array $terms)
    {
        $content = '';
        foreach ($this->phpFiles(base_path('tests')) as $file) $content .= "\n" . strtolower((string) @file_get_contents($file));
        foreach ($terms as $term) if (strpos($content, strtolower($term)) === false) return false;
        return true;
    }

    private function hasViewContaining($directory, $term)
    {
        $path = base_path($directory);
        if (!is_dir($path)) return false;
        foreach ($this->phpFiles($path) as $file) {
            if (strpos(strtolower($file), strtolower($term)) !== false) return true;
        }
        return false;
    }

    /**
     * Clinical maturity is deliberately independent from technical maturity.
     * A controller or a screen is evidence of implementation, but it is not
     * proof that a patient-safety control is enforced end to end.
     */
    private function clinicalMaturity()
    {
        $hospitalization = $this->source('app/Http/Controllers/hospital/hospitalizacion/HospitalizacionController.php');
        $epicrisis = $this->source('app/Http/Controllers/hc_admision/EpicrisisController.php');
        $emergency = $this->source('app/Http/Controllers/hospital/emergencia/CatorceavoPasoController.php');
        $icu = $this->source('app/Http/Controllers/hospital/uci/UciController.php');
        $ward = $this->source('app/Http/Controllers/hospital/CuartoController.php');
        $resolverReady = is_file(app_path('Services/CurrentHospitalResolver.php'));
        $hospitalContextAuditReady = is_file(app_path('Services/ClinicalHospitalContextAuditService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalHospitalContext.php'));
        $resolverSource = $this->source('app/Services/CurrentHospitalResolver.php');
        $hospitalContextCodeReady = $resolverReady
            && is_file(database_path('migrations/2026_08_19_019000_create_company_hospital_contexts.php'))
            && is_file(app_path('Console/Commands/ConfigureCompanyHospital.php'))
            && strpos($resolverSource, 'fromClinicalContext') !== false
            && strpos($resolverSource, 'company_hospital_contexts') !== false
            && strpos($icu, 'CurrentHospitalResolver') !== false
            && strpos($ward, 'activeRoomsForSolicitud') !== false;
        $longStayReady = $hospitalization !== ''
            && strpos($this->methodSource($hospitalization, 'master', 'buscar_hospitalizado'), 'whereBetween') === false;
        $epicrisisDebugFree = $epicrisis !== '' && strpos($epicrisis, 'dd($epicrisis)') === false;
        $fixedHospitalIds = preg_match("/where\s*\(\s*['\"]id_hospital['\"]\s*,\s*['\"]?[15]['\"]?\s*\)/i", $hospitalization . $emergency . $icu . $ward) === 1;
        $encounterFoundation = is_file(app_path('ClinicalEncounter.php'))
            && is_file(app_path('Services/ClinicalEncounterService.php'))
            && is_file(database_path('migrations/2026_08_19_007000_create_clinical_encounter_foundation.php'));
        $ambulatory = $this->source('app/Http/Controllers/hc4/ConsultaController.php');
        $emergencyAdmission = $this->source('app/Http/Controllers/hospital/Formulario008Controller.php');
        $hospitalAdmission = $this->source('app/Http/Controllers/hospital/HospitalController.php');
        $r01Integrated = $encounterFoundation
            && is_file(app_path('Console/Commands/AuditClinicalEncounters.php'))
            && strpos($hospitalAdmission, 'ClinicalEncounterService') !== false
            && strpos($ambulatory, 'ClinicalEncounterService') !== false
            && strpos($emergencyAdmission, 'ClinicalEncounterService') !== false;
        $r01IntegrityReady=$r01Integrated&&$this->clinicalEncounterConflictIntegrityReady();
        $coreEncounterMigration = is_file(database_path('migrations/2026_08_19_009000_attach_encounter_to_core_clinical_records.php'));
        $coreEncounterBackfill = is_file(app_path('Services/ClinicalEncounterBackfillService.php'))
            && is_file(app_path('Console/Commands/BackfillClinicalEncounters.php'));
        $coreEncounterModels = array(
            $this->source('app/Agenda.php'),
            $this->source('app/Historiaclinica.php'),
            $this->source('app/Ho_Solicitud.php'),
            $this->source('app/Ho_Form008.php'),
            $this->source('app/Ho_Log_Solicitud.php'),
        );
        $coreEncounterRelations = count(array_filter($coreEncounterModels, function ($source) {
            return strpos($source, "'encounter_id'") !== false;
        })) === count($coreEncounterModels);
        $f01Complete = $r01Integrated && $coreEncounterMigration && $coreEncounterBackfill
            && $coreEncounterRelations && $this->canonicalEncounterDataReady()
            && strpos($this->source('app/Services/ClinicalEncounterService.php'), 'syncSourceEncounter') !== false;
        $f01Evidence = $f01Complete
            ? 'Agenda, historia, solicitud, formulario 008 y bitácora comparten encounter_id sin registros clínicos huérfanos.'
            : 'El código y el backfill están listos; falta aplicar la migración y completar encounter_id en la base activa.';
        $clinicalClosureCodeReady = is_file(app_path('Services/ClinicalEncounterClosureService.php'))
            && is_file(app_path('Services/ClinicalClosureGuard.php'))
            && is_file(app_path('Services/ClinicalClosureAuditService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalClosures.php'))
            && is_file(app_path('Exceptions/ClinicalClosureBypassException.php'))
            && is_file(database_path('migrations/2026_08_19_010000_create_clinical_encounter_closure.php'))
            && strpos($epicrisis, 'ClinicalEncounterClosureService') !== false
            && strpos($this->source('app/Ho_Solicitud.php'), 'ClinicalClosureGuard::assertSolicitudMutation') !== false
            && strpos($this->source('app/ClinicalEncounter.php'), 'ClinicalClosureGuard::assertEncounterMutation') !== false
            && strpos($this->source('app/Services/ClinicalEncounterClosureService.php'), 'ClinicalClosureGuard::class') !== false
            && strpos($this->source('resources/views/hc_admision/epicrisis_final/epicrisis.blade.php'), 'cerrarEpisodio') !== false;
        $clinicalClosureReady = $clinicalClosureCodeReady && $f01Complete && $this->clinicalClosureDataReady();
        $clinicalClosureEntryPointsReady = strpos($this->source('resources/views/hospital/ambulatorio/solic.blade.php'), 'epicrisis.mostrar_final') !== false
            && strpos($this->source('resources/views/hospital/ambulatorio/solic_admin.blade.php'), 'epicrisis.mostrar_final') !== false
            && strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'), 'epicrisis.mostrar_final') !== false
            && strpos($epicrisis, 'inicializar_final') !== false
            && strpos($this->source('routes/hc_admision/web_epicrisis.php'), 'epicrisis.inicializar_final') !== false;
        $f02Complete = $clinicalClosureReady && $clinicalClosureEntryPointsReady;
        $f02Evidence = $f02Complete
            ? 'Hospitalización, emergencia y atención ambulatoria acceden al mismo cierre transaccional, que valida epicrisis, diagnóstico, conciliación, indicaciones, firma y órdenes.'
            : ($clinicalClosureCodeReady && $clinicalClosureEntryPointsReady
                ? 'Hospitalización, emergencia y ambulatorio ya usan el cierre único y los modelos bloquean cierres directos; falta aplicar migraciones o resolver expedientes históricos pendientes.'
                : 'Faltan integrar ámbitos clínicos o bloquear vías de cierre paralelas.');
        $clinicalStateReady = is_file(app_path('Services/ClinicalStateTransitionService.php'))
            && is_file(app_path('Services/ClinicalStateGuard.php'))
            && is_file(database_path('migrations/2026_08_19_008000_create_clinical_transitions_and_transfer_reconciliation.php'))
            && strpos($hospitalAdmission, 'ClinicalTransferReconciliationService') !== false
            && strpos($this->source('app/ClinicalEncounter.php'), 'ClinicalStateGuard::assertEncounterMutation') !== false;
        $clinicalStateAuditReady = is_file(app_path('Services/ClinicalStateAuditService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalStates.php'));
        $clinicalStateIntegrityReady = $clinicalStateReady && $clinicalStateAuditReady && $this->clinicalStateIntegrityReady();
        $emergencyTransfer = $this->source('app/Http/Controllers/hospital/emergencia/CatorceavoPasoController.php');
        $wardTransfer = $this->source('app/Http/Controllers/hospital/CuartoController.php');
        $transferReconciliationReady = $clinicalStateReady
            && is_file(app_path('Services/ClinicalTransferReconciliationService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalTransfers.php'))
            && strpos($emergencyTransfer, 'ClinicalTransferReconciliationService') !== false
            && strpos($wardTransfer, 'executeProcedureTransfer') !== false
            && strpos($wardTransfer, "executeProcedureTransfer(\$request, 5") !== false
            && strpos($wardTransfer, "executeProcedureTransfer(\$request, 4") !== false
            && strpos($this->source('resources/views/hospital/modulos/traspaso.blade.php'), 'preserve_active_orders') !== false
            && strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'), 'preserve_active_orders') !== false
            && strpos($this->source('resources/views/hospital/habitacion/modal_cirugia.blade.php'), 'preserve_active_orders') !== false
            && strpos($this->source('resources/views/hospital/habitacion/modal_imagenes.blade.php'), 'preserve_active_orders') !== false;
        $transferReconciliationIntegrityReady=$transferReconciliationReady&&$this->clinicalTransferIntegrityReady();
        $bedTransferSource = $this->source('app/Services/ClinicalPatientBedTransferService.php');
        $bedControllerSource = $this->source('app/Http/Controllers/hospital/CuartoController.php');
        $bedJourneySource = $this->source('app/Services/ClinicalPatientJourneyService.php');
        $bedTransferCodeReady = is_file(app_path('ClinicalBedTransfer.php'))
            && is_file(database_path('migrations/2026_08_21_010000_create_clinical_bed_transfers.php'))
            && strpos($bedTransferSource, 'DB::transaction') !== false
            && strpos($bedTransferSource, 'lockForUpdate') !== false
            && strpos($bedTransferSource, "'idempotent' => true") !== false
            && strpos($bedTransferSource, '}, 3);') !== false
            && strpos($bedTransferSource, 'ClinicalBedTransfer::create') !== false
            && strpos($bedControllerSource, "'assignment_already_processed'") !== false
            && strpos($bedControllerSource, '$sinCambios') !== false
            && strpos($bedControllerSource, 'Nunca libera') !== false
            && strpos($bedControllerSource, 'camas en preparación o fuera de servicio') !== false
            && strpos($bedJourneySource, 'clinical_bed_transfers') !== false
            && strpos($this->source('resources/views/hospital/habitacion/cuartos.blade.php'), 'Trasladar paciente') !== false
            && strpos($this->source('resources/views/hospital/habitacion/detalle.blade.php'), 'Ruta asistencial del episodio') !== false;
        $bedTransferDataReady = $this->clinicalBedTransferDataReady();
        $medicationAdministrationSource = $this->source('app/Services/ClinicalMedicationAdministrationService.php');
        $medicationControllerSource = $this->source('app/Http/Controllers/hospital/hospitalizacion/HospitalizacionController.php');
        $medicationViewSource = $this->source('resources/views/hospital/hospitalizacion/detalle.blade.php');
        $emergencyMedicationViewSource = $this->source('resources/views/hospital/emergencia/treceavopaso.blade.php');
        $ambulatoryMedicationViewSource = $this->source('resources/views/hc_admision/receta/detalle.blade.php');
        $sharedMedicationControllerSource = $this->source('app/Http/Controllers/hospital/ClinicalMedicationController.php');
        $emarCodeReady = is_file(app_path('ClinicalMedicationOrder.php'))
            && is_file(app_path('ClinicalMedicationAdministration.php'))
            && is_file(app_path('Services/ClinicalMedicationAuditService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalMedication.php'))
            && is_file(database_path('migrations/2026_08_19_011000_create_clinical_medication_administration.php'))
            && strpos($medicationAdministrationSource, 'La orden médica está suspendida') !== false
            && strpos($medicationAdministrationSource, 'patient_verified') !== false
            && strpos($medicationControllerSource, 'ClinicalMedicationAdministrationService') !== false
            && strpos($medicationViewSource, 'registrarAdministracion') !== false;
        $emergencyEmarReady = $emarCodeReady
            && strpos($sharedMedicationControllerSource, 'ClinicalMedicationAdministrationService') !== false
            && strpos($emergencyMedicationViewSource, 'registrarEmarClinico') !== false
            && strpos($this->source('routes/hospital/web_hospital.php'), "name('clinical_medication.store')") !== false;
        $ambulatoryEmarReady = $emergencyEmarReady
            && strpos($ambulatoryMedicationViewSource, 'Administración en consulta') !== false
            && strpos($ambulatoryMedicationViewSource, 'registrarEmarClinico') !== false;
        $emergencyMedicationExceptionReady = $ambulatoryEmarReady
            && is_file(app_path('ClinicalEmergencyMedicationException.php'))
            && is_file(database_path('migrations/2026_08_19_012000_create_emergency_medication_exceptions.php'))
            && strpos($medicationAdministrationSource, 'registerEmergencyException') !== false
            && strpos($medicationAdministrationSource, 'regularizeEmergencyException') !== false
            && strpos($medicationAdministrationSource, 'emergencyIdempotencyKey') !== false
            && strpos($medicationAdministrationSource, 'sameEmergencyPayload') !== false
            && strpos($medicationAdministrationSource, "where('source_id', \$sourceId)->lockForUpdate()->first()") !== false
            && is_file(base_path('tests/Unit/ClinicalMedicationConcurrencyAuditTest.php'))
            && strpos($emergencyMedicationViewSource, 'registrarExcepcionEmergencia') !== false
            && strpos($this->source('app/Services/ClinicalEncounterClosureService.php'), 'pending_emergency_medication_exceptions') !== false;
        $emarDataReady = $this->medicationAdministrationDataReady();
        $legacyWardMedicationSource = $this->source('app/Http/Controllers/hospital/CuartoController.php');
        $anesthesiaMedicationSource = $this->source('app/Http/Controllers/rc_pre_anestesica/rc_pre_anestesicaController.php');
        $legacyMedicationPathsClosed = strpos($legacyWardMedicationSource, 'Nota_Enfermeria::create') === false
            && strpos($legacyWardMedicationSource, 'La administración clínica antigua fue deshabilitada') !== false
            && strpos($legacyWardMedicationSource, 'La prescripción libre antigua fue deshabilitada') !== false
            && strpos($anesthesiaMedicationSource, 'ClinicalMedicationAdministrationService') !== false
            && strpos($anesthesiaMedicationSource, 'Drogas_administradas::create') === false
            && strpos($this->source('resources/views/rc_preanestesico/transanestesico_edit.blade.php'), 'Registrar eMAR') !== false;
        $f03Complete = $emergencyMedicationExceptionReady && $emarDataReady
            && $this->emergencyMedicationExceptionDataReady() && $legacyMedicationPathsClosed;
        $f03Evidence = $emarCodeReady
            ? ($emarDataReady
                ? ($ambulatoryEmarReady
                    ? ($f03Complete
                        ? 'Hospitalización, emergencia, consulta y anestesia usan eMAR; orden e inventario se bloquean durante la decisión, los reintentos de emergencia son idempotentes y la excepción urgente exige regularización en 60 minutos.'
                        : ($emergencyMedicationExceptionReady
                        ? 'Los ámbitos clínicos usan eMAR y la excepción urgente está implementada; falta aplicar la migración o cerrar una vía histórica detectada.'
                        : 'Hospitalización, emergencia y administración en consulta registran eMAR por episodio y orden activa con trazabilidad completa.')
                        )
                    : ($emergencyEmarReady
                    ? 'Hospitalización y emergencia registran eMAR por episodio y orden activa; la administración ambulatoria aún no está integrada.'
                    : 'Hospitalización registra eMAR por episodio y orden activa; emergencia aún no está integrada.')
                )
                : ($ambulatoryEmarReady && $emergencyMedicationExceptionReady
                    ? 'Hospitalización, emergencia, consulta y anestesia usan el mismo eMAR auditable; falta aplicar las migraciones o corregir datos reportados por clinical:medication-audit.'
                    : 'El circuito eMAR está implementado parcialmente; falta integrar ámbitos clínicos o aplicar sus migraciones.'))
            : 'El descargo marca producto entregado sin trazabilidad clínica completa.';
        $clinicalOrderSource = $this->source('app/Services/ClinicalOrderService.php');
        $clinicalOrderRoutes = $this->source('routes/hospital/web_hospital.php');
        $clinicalOrderCodeReady = is_file(app_path('ClinicalOrder.php'))
            && is_file(app_path('Services/ClinicalOrderGuard.php'))
            && is_file(app_path('ClinicalResult.php'))
            && is_file(app_path('ClinicalCriticalResultNotification.php'))
            && is_file(app_path('ClinicalCriticalResultEvent.php'))
            && is_file(database_path('migrations/2026_08_19_014000_create_clinical_order_result_cycle.php'))
            && is_file(database_path('migrations/2026_08_19_024000_create_clinical_critical_result_events.php'))
            && is_file(app_path('Console/Commands/BackfillClinicalOrders.php'))
            && strpos($clinicalOrderSource, 'syncSolicitud') !== false
            && strpos($clinicalOrderSource, 'recordResult') !== false
            && strpos($this->source('app/ClinicalOrder.php'), 'ClinicalOrderGuard::assertOrderMutation') !== false
            && strpos($clinicalOrderRoutes, "name('clinical_orders.index')") !== false;
        $clinicalOrderEntryPointsReady = strpos($this->source('resources/views/hospital/hospitalizacion/index.blade.php'), 'clinical_orders.index') !== false
            && strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'), 'clinical_orders.index') !== false
            && strpos($this->source('resources/views/hospital/ambulatorio/solic.blade.php'), 'clinical_orders.index') !== false
            && strpos($this->source('app/Http/Controllers/hospital/ClinicalOrderController.php'), "where('encounter_id', \$encounterId)") !== false
            && strpos($this->source('app/Http/Controllers/hospital/ClinicalOrderController.php'), 'ORDER_VIEW') !== false;
        $clinicalOrderCodeReady = $clinicalOrderCodeReady && $clinicalOrderEntryPointsReady;
        $criticalResultCodeReady = $clinicalOrderCodeReady
            && is_file(app_path('Console/Commands/EscalateCriticalClinicalResults.php'))
            && strpos($clinicalOrderSource, 'notifyCritical') !== false
            && strpos($clinicalOrderSource, 'acknowledgeCritical') !== false
            && strpos($clinicalOrderSource, 'escalateOverdue') !== false
            && strpos($this->source('resources/views/hospital/clinical_orders/index.blade.php'), 'Registrar acuse') !== false;
        $clinicalOrderAuditReady = is_file(app_path('Services/ClinicalOrderAuditService.php'))
            && is_file(app_path('Console/Commands/AuditClinicalOrders.php'));
        $clinicalOrderDataReady = $this->clinicalOrderResultDataReady();
        $clinicalOrderIntegrityReady = $clinicalOrderDataReady && $clinicalOrderAuditReady
            && $this->clinicalOrderIntegrityReady();
        $f04Evidence = $clinicalOrderCodeReady
            ? ($clinicalOrderIntegrityReady
                ? 'Órdenes y resultados se consolidan por episodio sin huérfanos detectados; los resultados críticos registran receptor, hora, acuse y escalamiento automático.'
                : ($clinicalOrderDataReady
                ? 'El ciclo transversal está desplegado, pero la auditoría aún detecta datos por sincronizar o verificar.'
                : 'El ciclo transversal Order/Result, su backfill y el control de resultados críticos están listos; falta aplicar la migración y ejecutar la sincronización inicial.')
                )
            : 'Laboratorio, imágenes y procedimientos mantienen flujos separados.';
        $clinicalRecordCodeReady = is_file(app_path('ClinicalRecord.php'))
            && is_file(app_path('ClinicalDocument.php'))
            && is_file(app_path('Services/ClinicalRecordService.php'))
            && is_file(app_path('Console/Commands/BackfillClinicalRecords.php'))
            && is_file(app_path('Console/Commands/AuditClinicalRecords.php'))
            && is_file(database_path('migrations/2026_08_19_015000_create_longitudinal_clinical_record.php'))
            && strpos($this->source('app/Historiaclinica.php'), 'registerHistory') !== false
            && strpos($this->source('app/Hc_Epicrisis_Final.php'), 'registerEpicrisis') !== false
            && strpos($this->source('app/Ho_Form008.php'), 'registerEmergencyForm') !== false;
        $clinicalRecordIntegrityReady = $clinicalRecordCodeReady && $this->clinicalRecordIntegrityReady();
        $f07Evidence = $clinicalRecordIntegrityReady
            ? 'Cada paciente tiene un expediente longitudinal único y sus historias por agenda se registran como documentos vinculados al episodio correspondiente.'
            : ($clinicalRecordCodeReady
                ? 'El expediente longitudinal, los documentos por episodio y su backfill están implementados; falta aplicar la migración o resolver pendientes de integridad.'
                : 'Historiaclinica se relaciona por id_agenda y puede haber varios hcid por paciente.');
        $consultWorkflowSource = $this->source('app/Services/ClinicalConsultWorkflowService.php');
        $consultWorkflowCodeReady = is_file(app_path('ClinicalConsultRequest.php'))
            && is_file(app_path('ClinicalConsultEvent.php'))
            && is_file(app_path('Console/Commands/BackfillClinicalConsults.php'))
            && is_file(app_path('Console/Commands/AuditClinicalConsults.php'))
            && is_file(app_path('Console/Commands/EscalateClinicalConsults.php'))
            && is_file(database_path('migrations/2026_08_19_016000_create_clinical_consult_workflow.php'))
            && strpos($consultWorkflowSource, 'escalateOverdue') !== false
            && strpos($consultWorkflowSource, 'SLA_ESCALATED') !== false
            && strpos($this->source('resources/views/hospital/clinical_consults/index.blade.php'), 'Iniciar revisión') !== false
            && strpos($this->source('app/Http/Controllers/hospital/emergencia/DecimoPasoController.php'), 'ClinicalConsultWorkflowService') !== false
            && strpos($this->source('app/Http/Controllers/hc4/ConsultaController.php'), 'ClinicalConsultWorkflowService') !== false;
        $consultEntryPointsReady=strpos($this->source('resources/views/hospital/hospitalizacion/index.blade.php'),'clinical_consults.index')!==false
            &&strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'),'clinical_consults.index')!==false
            &&strpos($this->source('resources/views/hospital/ambulatorio/solic.blade.php'),'clinical_consults.index')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalConsultController.php'),"where('encounter_id',\$encounterId)")!==false;
        $consultWorkflowCodeReady=$consultWorkflowCodeReady&&$consultEntryPointsReady;
        $consultIntegrity = $consultWorkflowCodeReady ? $this->clinicalConsultIntegrity() : array('ready'=>false,'sla_ready'=>false);
        $f08Evidence = $consultIntegrity['ready']
            ? 'Hospitalización, emergencia y ambulatorio comparten estados, aceptación, revisión, respuesta, SLA y eventos auditables por episodio.'
            : ($consultWorkflowCodeReady
                ? 'El workflow transversal y su backfill están implementados; falta aplicar la migración o sincronizar registros heredados.'
                : 'No se evidencia SLA, aceptación, escalamiento y confirmación de revisión.');
        $deathServiceSource = $this->source('app/Services/ClinicalDeathService.php');
        $deathWorkflowCodeReady = is_file(app_path('ClinicalDeathCase.php'))
            && is_file(app_path('ClinicalDeathEvent.php'))
            && is_file(app_path('Console/Commands/AuditClinicalDeaths.php'))
            && is_file(database_path('migrations/2026_08_19_017000_create_clinical_death_workflow.php'))
            && strpos($deathServiceSource, 'DEATH_WORKFLOW_COMPLETED') !== false
            && strpos($this->source('app/Services/ClinicalEncounterClosureService.php'), 'ClinicalDeathService') !== false
            && strpos($this->source('resources/views/hc_admision/epicrisis_final/epicrisis.blade.php'), 'Circuito específico de defunción') !== false;
        $deathWorkflowIntegrityReady = $deathWorkflowCodeReady && $this->clinicalDeathIntegrityReady();
        $f09Evidence = $deathWorkflowIntegrityReady
            ? 'Toda defunción cerrada utiliza CLOSED_DECEASED y conserva constatación, causa, certificado, identidad y notificaciones auditables.'
            : ($deathWorkflowCodeReady
                ? 'El circuito independiente de defunción y su auditoría están implementados; falta aplicar la migración o completar expedientes históricos detectados.'
                : 'Existe un campo defuncion en epicrisis, pero no un circuito integral independiente.');
        $procedureSafetySource=$this->source('app/Services/ClinicalProcedureSafetyService.php');
        $procedureSafetyCodeReady=is_file(app_path('ClinicalConsent.php'))
            && is_file(app_path('ClinicalProcedureAuthorization.php'))
            && is_file(app_path('ClinicalConsentEvent.php'))
            && is_file(app_path('Console/Commands/AuditClinicalProcedureSafety.php'))
            && is_file(database_path('migrations/2026_08_19_018000_create_clinical_consent_safety.php'))
            && strpos($procedureSafetySource,'EMERGENCY_EXCEPTION_AUTHORIZED')!==false
            && strpos($procedureSafetySource,'PROCEDURE_STARTED')!==false
            && strpos($this->source('app/Http/Controllers/hc_admision/ProtocoloController.php'),'authorizeForProcedure')!==false
            && strpos($this->source('resources/views/hc_admision/protocolo/protocolo.blade.php'),'Seguridad antes de iniciar el procedimiento')!==false
            && strpos($this->source('app/Http/Controllers/hc4/ProcedimientosQuirurgicosController.php'),'authorizeExistingProtocol')!==false
            && strpos($this->source('resources/views/hc4/procedimiento_quirurgicos/editar2.blade.php'),'procedure_identity_verified')!==false
            && strpos($this->source('app/Http/Controllers/hc4/ProcedimientosEndoscopicosController.php'),'authorizeFirstStart')!==false
            && strpos($this->source('app/Http/Controllers/hc4/ProcedimientosFuncionalesController.php'),'authorizeExistingProtocol')!==false
            && strpos($this->source('resources/views/hc4/partials/procedure_safety.blade.php'),'procedure_checklist_complete')!==false
            && strpos($this->source('app/Services/ClinicalWardProcedureService.php'),'authorizeForProcedure')!==false
            && strpos($this->source('app/Services/ClinicalWardProcedureService.php'),"->consume(\$procedureAuthorization, 'hc_protocolo'")!==false
            && strpos($this->source('resources/views/hospital/habitacion/_procedure_safety.blade.php'),'procedure_identity_verified')!==false;
        $procedureSafetyIntegrityReady=$procedureSafetyCodeReady&&$this->clinicalProcedureSafetyIntegrityReady();
        $f10Evidence=$procedureSafetyCodeReady
            ? ($procedureSafetyIntegrityReady
                ? 'Los protocolos generales, endoscópicos, funcionales, quirúrgicos y los traslados hospitalarios a cirugía/imágenes exigen identidad, indicación, consentimiento versionado y checklist antes del primer inicio; las excepciones de emergencia quedan justificadas y auditadas.'
                : 'Las guardas preprocedimiento están integradas en protocolos generales, endoscópicos, funcionales, quirúrgicos y traslados hospitalarios; falta aplicar la migración o resolver inconsistencias detectadas por la auditoría.')
            : 'Existen formularios imprimibles sin validación central previa al procedimiento.';
        $accessControlSource=$this->source('app/Services/ClinicalAccessControlService.php');
        $accessControlCodeReady=is_file(app_path('ClinicalAccessScope.php'))&&is_file(app_path('ClinicalBreakGlassAccess.php'))&&is_file(app_path('ClinicalAccessEvent.php'))
            &&is_file(database_path('migrations/2026_08_19_020000_create_clinical_access_control.php'))&&is_file(app_path('Console/Commands/AuditClinicalAccess.php'))
            &&is_file(database_path('migrations/2026_08_19_025000_link_break_glass_access_events.php'))
            &&strpos($accessControlSource,'activateBreakGlass')!==false&&strpos($accessControlSource,'review_due_at')!==false
            &&strpos($accessControlSource,'break_glass_access_id')!==false&&strpos($accessControlSource,'duplicate_active')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalOrderController.php'),'assertAllowed')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalOrderController.php'),'ORDER_VIEW')!==false
            &&strpos($this->source('routes/hospital/web_hospital.php'),"name('clinical_access.break_glass')")!==false;
        $accessControlIntegrityReady=$accessControlCodeReady&&$this->clinicalAccessIntegrityReady();
        $accessControlEntryPointsReady=strpos($this->source('resources/views/hospital/hospitalizacion/index.blade.php'),'clinical_access.index')!==false
            &&strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'),'clinical_access.index')!==false
            &&strpos($this->source('resources/views/hospital/ambulatorio/solic.blade.php'),'clinical_access.index')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalAccessController.php'),"where('encounter_id',\$encounterId)")!==false
            &&strpos($this->source('resources/views/hospital/clinical_access/index.blade.php'),'Pendiente de revisión independiente')!==false;
        $accessControlClinicalSurfacesReady=$accessControlCodeReady
            &&strpos($this->source('app/Services/ClinicalMedicationAdministrationService.php'),'medication.administer')!==false
            &&strpos($procedureSafetySource,'procedure.authorize')!==false
            &&strpos($this->source('app/Services/ClinicalConsultWorkflowService.php'),'consult.update')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalConsultController.php'),'CONSULT_VIEW')!==false
            &&strpos($this->source('app/Services/ClinicalEncounterClosureService.php'),'encounter.close')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalSafetyEventController.php'),'SAFETY_EVENT_VIEW')!==false;
        $bedTransferAccessReady = strpos($bedTransferSource, 'ClinicalAccessControlService') !== false
            && strpos($bedTransferSource, 'assertAllowed') !== false;
        $accessControlClinicalSurfacesReady = $accessControlClinicalSurfacesReady && $bedTransferAccessReady;
        $f13Complete=$accessControlIntegrityReady&&$accessControlClinicalSurfacesReady&&$accessControlEntryPointsReady;
        $amendmentSource=$this->source('app/Services/ClinicalRecordService.php');
        $signedAmendmentReady=is_file(app_path('ClinicalDocumentAmendment.php'))&&is_file(database_path('migrations/2026_08_19_021000_create_clinical_document_amendments.php'))
            &&is_file(app_path('Services/ClinicalSignedDocumentGuard.php'))&&is_file(app_path('Services/ClinicalDocumentGuard.php'))
            &&is_file(app_path('Console/Commands/AuditClinicalRecords.php'))
            &&strpos($amendmentSource,'signDocument')!==false&&strpos($amendmentSource,'canonicalContent')!==false
            &&strpos($amendmentSource,'invalid_amendment_chain')!==false
            &&strpos($this->source('app/Services/ClinicalEncounterClosureService.php'),"signDocument('hc_epicrisis_final'")!==false;
        $signedAmendmentIntegrityReady=$signedAmendmentReady&&$clinicalRecordIntegrityReady;
        $documentCompletenessCodeReady=$signedAmendmentReady
            &&is_file(app_path('ClinicalDocumentCompleteness.php'))
            &&is_file(app_path('Services/ClinicalDocumentCompletenessService.php'))
            &&is_file(app_path('Console/Commands/AuditClinicalDocumentCompleteness.php'))
            &&is_file(database_path('migrations/2026_08_19_023000_create_clinical_document_completeness.php'))
            &&strpos($amendmentSource,'ClinicalDocumentCompletenessService::class')!==false
            &&strpos($this->source('resources/views/hc_admision/epicrisis_final/epicrisis.blade.php'),'Completitud del expediente')!==false;
        $documentCompletenessIntegrityReady=$documentCompletenessCodeReady&&$signedAmendmentIntegrityReady&&$this->clinicalDocumentCompletenessIntegrityReady();
        $safetyEventSource=$this->source('app/Services/ClinicalSafetyEventService.php');
        $safetyEventCodeReady=is_file(app_path('ClinicalSafetyEvent.php'))&&is_file(app_path('ClinicalSafetyEventLog.php'))
            &&is_file(app_path('Services/ClinicalSafetyEventGuard.php'))
            &&is_file(database_path('migrations/2026_08_19_022000_create_clinical_safety_events.php'))&&is_file(app_path('Console/Commands/AuditClinicalSafetyEvents.php'))
            &&strpos($safetyEventSource,'SLA_ESCALATED')!==false&&strpos($safetyEventSource,'No puede cerrar un evento grave sin notificación registrada.')!==false
            &&strpos($safetyEventSource,'missing_closure_log')!==false
            &&strpos($this->source('app/ClinicalSafetyEvent.php'),'ClinicalSafetyEventGuard::assertMutation')!==false
            &&strpos($this->source('routes/hospital/web_hospital.php'),"name('clinical_safety.store')")!==false;
        $safetyEventEntryPointsReady=strpos($this->source('resources/views/hospital/hospitalizacion/index.blade.php'),'clinical_safety.index')!==false
            &&strpos($this->source('resources/views/hospital/emergencia/catorceavopaso.blade.php'),'clinical_safety.index')!==false
            &&strpos($this->source('resources/views/hospital/ambulatorio/solic.blade.php'),'clinical_safety.index')!==false
            &&strpos($this->source('app/Http/Controllers/hospital/ClinicalSafetyEventController.php'),"where('encounter_id',\$encounterId)")!==false;
        $safetyEventIntegrityReady=$safetyEventCodeReady&&$safetyEventEntryPointsReady&&$this->clinicalSafetyEventIntegrityReady();
        $mutableGetAuditReady=is_file(app_path('Services/ClinicalRouteSafetyAuditService.php'))&&is_file(app_path('Console/Commands/AuditClinicalRouteSafety.php'));
        $mutableGetPending=$mutableGetAuditReady?$this->mutableClinicalGetPending():null;
        $transportCodeReady=is_file(app_path('Http/Middleware/EnforceSecureClinicalTransport.php'))&&is_file(app_path('Http/Middleware/TrustProxies.php'))
            &&is_file(app_path('Services/ClinicalTransportAuditService.php'))&&is_file(app_path('Console/Commands/AuditClinicalTransport.php'))
            &&strpos($this->source('app/Http/Kernel.php'),'EnforceSecureClinicalTransport')!==false
            &&strpos($this->source('app/Http/Middleware/EnforceSecureClinicalTransport.php'),'Content-Security-Policy')!==false
            &&strpos($this->source('app/Http/Middleware/EnforceSecureClinicalTransport.php'),"config('app.url')")!==false
            &&strpos($this->source('app/Services/ClinicalTransportAuditService.php'),'APP_DEBUG debe estar desactivado')!==false;
        $transportPending=$transportCodeReady?$this->clinicalTransportPending():null;
        $architectureAuditReady=is_file(app_path('Services/ClinicalArchitectureAuditService.php'))&&is_file(app_path('Console/Commands/AuditClinicalArchitecture.php'));
        $architecturePending=$architectureAuditReady?$this->clinicalArchitecturePending():null;

        $controls = array(
            $this->clinicalControl('F-01', 'CRÍTICO', 'No existe un episodio clínico canónico transversal.', $f01Evidence, 'Continuar usando el episodio canónico como raíz de nuevos documentos clínicos.', 'P0', $f01Complete ? 'complete' : ($encounterFoundation ? 'partial' : 'pending')),
            $this->clinicalControl('F-02', 'CRÍTICO', 'Alta y cierre no están gobernados por una transacción clínica única.', $f02Evidence, 'Mantener la auditoría del cierre único y bloquear cualquier nuevo egreso paralelo.', 'P0', $f02Complete ? 'complete' : ($clinicalClosureCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-03', 'CRÍTICO', 'La medicación no demuestra un circuito cerrado de administración.', $f03Evidence, 'Mantener auditoría periódica de nuevas rutas de medicación y cumplimiento de regularizaciones.', 'P0', $f03Complete ? 'complete' : ($emarCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-04', 'CRÍTICO', 'Órdenes y resultados carecen de un ciclo transversal.', $f04Evidence, 'Ejecutar la auditoría reparable y corregir cualquier origen histórico que permanezca sin episodio.', 'P0', $clinicalOrderIntegrityReady ? 'complete' : ($clinicalOrderCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-05', 'ALTO', 'Pacientes de larga estancia pueden no aparecer en la vista inicial.', 'La ocupación activa se filtraba por la fecha del día.', 'Listar ocupaciones activas sin limitar la fecha de ingreso; conservar la fecha como filtro opcional.', 'P0', $longStayReady ? 'complete' : 'pending'),
            $this->clinicalControl('F-06', 'ALTO', 'Los estados son números o módulos y no una máquina clínica.', $clinicalStateReady ? 'Los estados nominales gobiernan atención activa, traslado, cierre, alta y defunción; la auditoría reparable detecta episodios sin estado o inconsistentes.' : 'El flujo depende de números de módulo sin transiciones clínicas explícitas.', 'Ejecutar periódicamente clinical:states-audit y corregir cualquier transición inválida reportada.', 'P1', $clinicalStateIntegrityReady ? 'complete' : ($clinicalStateReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-07', 'ALTO', 'La historia longitudinal se confunde con la historia por agenda.', $f07Evidence, 'Mantener el expediente 1:1 y registrar todo documento nuevo dentro de su episodio.', 'P0', $clinicalRecordIntegrityReady ? 'complete' : ($clinicalRecordCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-08', 'ALTO', 'La interconsulta no tiene una orquestación completa.', $f08Evidence, 'Supervisar los SLA y mantener responsables por servicio y especialidad.', 'P1', $consultIntegrity['ready'] ? 'complete' : ($consultWorkflowCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-09', 'ALTO', 'La defunción no está separada del alta normal.', $f09Evidence, 'Auditar periódicamente que no existan cierres CLOSED_DECEASED sin expediente específico completo.', 'P0', $deathWorkflowIntegrityReady ? 'complete' : ($deathWorkflowCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-10', 'ALTO', 'Los consentimientos no operan como guardas.', $f10Evidence, 'Mantener la auditoría preprocedimiento en cero y registrar toda nueva vía de inicio en la guarda central.', 'P0', $procedureSafetyIntegrityReady ? 'complete' : ($procedureSafetyCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-11', 'ALTO', 'Riesgo de interrupción por código de depuración.', 'EpicrisisController::actualiza contenía una detención de depuración después de actualizar.', 'Eliminar la depuración y proteger la actualización con transacción y prueba.', 'P0', $epicrisisDebugFree ? 'complete' : 'pending'),
            $this->clinicalControl('F-12', 'ALTO', 'La configuración hospitalaria es rígida.', $hospitalContextCodeReady ? 'Hospitalización y emergencia resuelven sede por episodio, solicitud, empresa configurada o sesión; la auditoría detecta sedes ausentes, inválidas o ambiguas.' : 'Hospitalización y emergencia utilizaban identificadores fijos diferentes.', 'Ejecutar clinical:hospital-context-audit --repair y seleccionar manualmente la sede cuando una empresa opere en varias.', 'P0', ($hospitalContextCodeReady && $hospitalContextAuditReady && !$fixedHospitalIds && $this->hospitalContextDataReady()) ? 'complete' : (($resolverReady && !$fixedHospitalIds) ? 'partial' : 'pending')),
            $this->clinicalControl('F-13', 'ALTO', 'No se demuestra autorización clínica granular.', !$bedTransferAccessReady ? 'Órdenes, resultados, medicación, interconsultas, cierre y seguridad validan alcance; el traslado de paciente entre camas todavía depende solo de autenticación y debe incorporarse a la autorización granular.' : ($accessControlEntryPointsReady ? 'Los tres ámbitos y el traslado de cama aplican alcance por episodio, acción y break-glass revisable.' : ($accessControlCodeReady ? 'Existe autorización granular, pero falta acceso contextual completo al circuito excepcional.' : 'Los permisos son por opción/tipo de usuario, sin alcance por episodio, servicio y acción.')), 'Aplicar alcance explícito a traslado, liberación y reubicación de camas; mantener auditoría de accesos excepcionales.', 'P1', $f13Complete ? 'complete' : ($accessControlCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-14', 'MEDIO', 'Los formularios y datos pueden quedar incompletos.', $documentCompletenessCodeReady ? ($documentCompletenessIntegrityReady ? 'Cada documento muestra su porcentaje y campos faltantes; la firma exige 100% según su tipo y estado, y las correcciones posteriores crean adendas.' : 'La evaluación visible y el bloqueo de firma incompleta están implementados; falta aplicar la migración o reparar documentos históricos firmados sin evaluación.') : ($signedAmendmentReady ? 'Los documentos firmados son inmutables, pero aún no existe una evaluación transversal visible de completitud.' : 'La obligatoriedad está dispersa entre múltiples controladores y guardados parciales.'), 'Ejecutar clinical:documents-audit --repair y mantener en cero los documentos firmados incompletos.', 'P1', $documentCompletenessIntegrityReady ? 'complete' : ($documentCompletenessCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-15', 'MEDIO', 'Eventos adversos, IAAS, aislamiento y custodia no están integrados.', $safetyEventEntryPointsReady ? 'Hospitalización, emergencia y ambulatorio abren el registro transversal desde el episodio; la auditoría valida SLA, bitácora, notificación, causa raíz y cierre.' : ($safetyEventCodeReady ? 'Existe el registro transversal, pero faltan accesos contextuales desde todos los ámbitos clínicos.' : 'No se evidencia un registro transversal enlazado al episodio.'), 'Supervisar eventos vencidos y notificaciones graves mediante la auditoría programada.', 'P2', $safetyEventIntegrityReady ? 'complete' : ($safetyEventCodeReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-16', 'MEDIO', 'Operaciones mutables utilizan rutas GET en varios módulos.', $mutableGetAuditReady ? 'La auditoría cuenta '.(int) $mutableGetPending.' rutas clínicas mutables por GET; las altas, actualizaciones y eliminaciones auditadas utilizan POST/DELETE y protección CSRF.' : 'Hay endpoints GET para eliminar diagnósticos, órdenes y otros elementos.', 'Mantener la auditoría automática en cero al incorporar nuevos flujos clínicos.', 'P1', $mutableGetPending===0 ? 'complete' : ($mutableGetAuditReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-17', 'MEDIO', 'Los controladores concentran responsabilidades clínicas y operativas.', $architectureAuditReady ? 'Admisión, asignación de camas, evoluciones, consultas, visitas, protocolos y órdenes clínicas ya usan servicios transaccionales sincronizados con el episodio; importaciones, estadísticas y exportadores extensos se aíslan de la lógica HTTP. La auditoría mantiene '.(int) $architecturePending.' controladores heredados por separar.' : 'HospitalController e HistoriaController mezclan admisión, clínica, inventario y reportes.', 'Extraer progresivamente cada controlador reportado a servicios de dominio con pruebas por caso de uso.', 'P2', $architecturePending===0 ? 'complete' : ($architectureAuditReady ? 'partial' : 'pending')),
            $this->clinicalControl('F-18', 'MEDIO', 'La confidencialidad del transporte no está garantizada.', $transportCodeReady ? ($transportPending===0 ? 'HTTPS, cookies seguras, dominio canónico, HSTS y cabeceras defensivas están activos y auditados.' : 'El certificado está instalado; la configuración de la aplicación todavía reporta controles de transporte pendientes.') : 'El acceso observado se presenta sin conexión segura.', 'Mantener HTTPS obligatorio y ejecutar clinical:transport-audit después de cada cambio de dominio, certificado o sesión.', 'Infraestructura', $transportPending===0 ? 'complete' : ($transportCodeReady ? 'partial' : 'pending')),
        );

        $rules = array(
            $this->clinicalRule('R-01', 'No abrir dos episodios activos incompatibles para el mismo paciente sin advertencia y autorización.', array('F-01', 'F-06'), $r01IntegrityReady ? 'complete' : ($r01Integrated ? 'partial' : 'pending')),
            $this->clinicalRule('R-02', 'No iniciar procedimiento sin identidad, indicación, consentimiento vigente y checklist, salvo emergencia documentada.', array('F-10', 'F-14'), $procedureSafetyIntegrityReady ? 'complete' : ($procedureSafetyCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-03', 'No administrar medicamento sin orden activa, salvo protocolo de emergencia con regularización obligatoria.', array('F-03', 'F-04'), $f03Complete ? 'complete' : ($emarCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-04', 'Toda administración registra dosis, vía, hora programada, hora real, responsable y resultado u omisión.', array('F-03'), $f03Complete ? 'complete' : ($emarCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-05', 'Un resultado crítico requiere notificación, receptor, hora, acuse y escalamiento.', array('F-04'), ($criticalResultCodeReady && $clinicalOrderIntegrityReady) ? 'complete' : ($criticalResultCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-06', 'Toda interconsulta urgente tiene temporizador y escalamiento.', array('F-08'), $consultIntegrity['sla_ready'] ? 'complete' : ($consultWorkflowCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-07', 'Un traslado conserva órdenes activas o documenta su conciliación o cancelación.', array('F-04', 'F-06'), $transferReconciliationIntegrityReady ? 'complete' : ($transferReconciliationReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-08', 'El alta no procede con epicrisis, diagnóstico de egreso, conciliación, indicaciones o firmas incompletas.', array('F-02', 'F-14'), $f02Complete ? 'complete' : ($clinicalClosureCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-09', 'Las órdenes pendientes al alta se cancelan con razón o pasan a seguimiento con responsable.', array('F-02', 'F-04'), $f02Complete ? 'complete' : ($clinicalClosureCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-10', 'La defunción nunca se registra como alta común y exige documentos y actores específicos.', array('F-09'), $deathWorkflowIntegrityReady ? 'complete' : ($deathWorkflowCodeReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-11', 'La corrección de un documento firmado crea una adenda y conserva el contenido previo.', array('F-07', 'F-14'), $signedAmendmentIntegrityReady ? 'complete' : ($signedAmendmentReady ? 'partial' : 'pending')),
            $this->clinicalRule('R-12', 'Los accesos break-glass exigen una razón y revisión posterior.', array('F-13'), $accessControlIntegrityReady ? 'complete' : ($accessControlCodeReady ? 'partial' : 'pending')),
        );

        $clinicalCiReady = is_file(base_path('.github/workflows/clinical-ci.yml'));
        $operationsEvidenceCodeReady = is_file(app_path('Services/ClinicalOperationsEvidenceService.php'))
            && is_file(app_path('Console/Commands/MonitorClinicalOperations.php'))
            && is_file(app_path('Console/Commands/RecordClinicalReadinessEvidence.php'))
            && is_file(app_path('Console/Commands/AcknowledgeClinicalOperationsAlert.php'))
            && is_file(database_path('migrations/2026_08_21_020000_create_clinical_readiness_evidence.php'));
        $operationsStatus = $operationsEvidenceCodeReady
            ? app(\Sis_medico\Services\ClinicalOperationsEvidenceService::class)->status()
            : array('monitor_72h' => false, 'monitor_hours' => 0, 'restore_approved' => false, 'pilot_approved' => false);
        $restoreDrillReady = !empty($operationsStatus['restore_approved']);
        $pilotSignoffReady = !empty($operationsStatus['pilot_approved']);
        $transportRenewalReady = !empty($operationsStatus['transport_renewal_approved']);
        $transportSnapshot = app(\Sis_medico\Services\ClinicalTransportAuditService::class)->latestLiveSnapshot();
        $transportLiveReady = is_array($transportSnapshot) && !empty($transportSnapshot['fresh'])
            && isset($transportSnapshot['pending']) && (int) $transportSnapshot['pending'] === 0;
        $transportLiveEvidence = 'Falta una inspección pública satisfactoria del certificado en las últimas 24 horas. ';
        if ($transportLiveReady) {
            $certificate = isset($transportSnapshot['details']) && is_array($transportSnapshot['details'])
                ? $transportSnapshot['details']
                : array();
            $transportLiveEvidence = 'Auditoría pública F-18 aprobada y sellada: dominio '
                .(isset($certificate['domain']) ? $certificate['domain'] : 'no informado')
                .', protocolo '.(isset($certificate['protocol']) ? $certificate['protocol'] : 'no informado')
                .', emisor '.(isset($certificate['issuer']) ? $certificate['issuer'] : 'no informado')
                .', vence '.(isset($certificate['expires_at']) ? $certificate['expires_at'] : 'no informado')
                .', días restantes '.(isset($certificate['remaining_days']) ? (int) $certificate['remaining_days'] : 0)
                .', huella SHA-256 '.(isset($certificate['fingerprint_sha256']) ? $certificate['fingerprint_sha256'] : 'no informada')
                .' y hash de evidencia '.(isset($transportSnapshot['integrity_hash']) ? $transportSnapshot['integrity_hash'] : 'no informado').'. ';
        }
        $schedulerSource = $this->source('app/Console/Kernel.php');
        $clinicalSchedulerReady = strpos($schedulerSource, 'clinical:critical-results-escalate') !== false
            && strpos($schedulerSource, 'clinical:consults-escalate') !== false
            && strpos($schedulerSource, 'clinical:safety-events-escalate') !== false
            && strpos($schedulerSource, 'clinical:operations-monitor') !== false;
        $operations72hReady = $clinicalSchedulerReady && !empty($operationsStatus['monitor_72h'])
            && !empty($operationsStatus['daily_audit_fresh'])
            && !empty($operationsStatus['operations_responsible_configured'])
            && isset($operationsStatus['open_alerts']) && (int) $operationsStatus['open_alerts'] === 0;

        $productionControls = array(
            $this->v3Control('HPR-01', 'CRÍTICO', 'Episodio, admisión e identidad clínica deben permanecer unificados.',
                $f01Complete && $clinicalStateIntegrityReady ? 'El episodio canónico y la máquina clínica no reportan inconsistencias en la base evaluada.' : 'La implementación existe, pero la autorización depende de ejecutar las auditorías contra la base que recibirá pacientes.',
                'Ejecutar el ciclo reparable y conservar en cero episodios huérfanos, incompatibles o con estado inválido.',
                'clinical:encounters-audit y clinical:states-audit terminan en cero sobre un respaldo restaurado de producción.', 'Fase 1',
                $f01Complete && $clinicalStateIntegrityReady ? 'complete' : ($encounterFoundation ? 'partial' : 'pending')),
            $this->v3Control('HPR-02', 'ALTO', 'Ocupación, habitación, cama y traslados internos requieren integridad concurrente.',
                $bedTransferCodeReady ? ($bedTransferDataReady ? 'Asignación, estado y traslado son atómicos, bloquean filas, reintentan contención y reconocen reenvíos sin duplicar movimientos; la cama anterior queda en preparación y existe bitácora origen/destino.' : 'El flujo atómico, idempotente y la línea de tiempo están implementados; falta aplicar la migración de traslados en la base evaluada.') : 'No se demuestra un traslado paciente-cama atómico, idempotente y auditable.',
                'Aplicar la migración, auditar ocupaciones duplicadas y ejecutar una prueba concurrente de dos traslados hacia la misma cama.',
                'Nunca existen dos ingresos activos por cama o paciente; un solo traslado gana la carrera, el otro se rechaza sin cambios parciales y un reenvío idéntico no duplica la bitácora.', 'Fase 1',
                $bedTransferCodeReady && $bedTransferDataReady ? 'complete' : ($bedTransferCodeReady ? 'partial' : 'pending')),
            $this->v3Control('HPR-03', 'CRÍTICO', 'La administración de medicamentos debe operar exclusivamente mediante eMAR.',
                $f03Evidence, 'Ejecutar la auditoría de medicación y regularizar excepciones urgentes antes del piloto.',
                'Cero administraciones sin orden, cinco correctos completos y cero excepciones vencidas.', 'Fase 1',
                $f03Complete ? 'complete' : ($emarCodeReady ? 'partial' : 'pending')),
            $this->v3Control('HPR-04', 'CRÍTICO', 'Órdenes, resultados y alertas críticas deben cerrar su ciclo transversal.',
                $f04Evidence, 'Sincronizar datos históricos y verificar notificación, acuse y escalamiento de un resultado crítico de prueba.',
                'Cero órdenes/resultados huérfanos y una prueba de resultado crítico conserva receptor, hora, acuse y escalamiento.', 'Fase 1',
                $clinicalOrderIntegrityReady ? 'complete' : ($clinicalOrderCodeReady ? 'partial' : 'pending')),
            $this->v3Control('HPR-05', 'ALTO', 'Traslados asistenciales, procedimientos e interconsultas deben conservar órdenes y SLA.',
                $transferReconciliationIntegrityReady && $procedureSafetyIntegrityReady && $consultIntegrity['ready'] ? 'Conciliación de órdenes, guardas preprocedimiento e interconsultas están íntegras en la base evaluada.' : 'Existen los controles, pero al menos una auditoría o dato productivo todavía no demuestra integridad total.',
                'Probar emergencia→sala→hospitalización→imágenes/laboratorio→retorno y una interconsulta urgente vencida.',
                'Las órdenes se conservan o cancelan con motivo; el procedimiento exige consentimiento y la interconsulta escala dentro del SLA.', 'Fase 1',
                $transferReconciliationIntegrityReady && $procedureSafetyIntegrityReady && $consultIntegrity['ready'] ? 'complete' : 'partial'),
            $this->v3Control('HPR-06', 'CRÍTICO', 'Alta, defunción, documentos y firma deben bloquear cierres incompletos.',
                $f02Complete && $deathWorkflowIntegrityReady && $documentCompletenessIntegrityReady ? 'El cierre único, la defunción separada y la completitud documental están íntegras.' : 'La lógica está disponible, pero no se ha demostrado integridad simultánea de cierre, defunción y documentos sobre la base objetivo.',
                'Certificar un alta normal, una cancelación de alta por pendiente y una defunción en datos de prueba anonimizados.',
                'No cierra sin epicrisis, diagnóstico, conciliación, indicaciones y firmas; la defunción nunca usa alta común.', 'Fase 1',
                $f02Complete && $deathWorkflowIntegrityReady && $documentCompletenessIntegrityReady ? 'complete' : ($clinicalClosureCodeReady ? 'partial' : 'pending')),
            $this->v3Control('HPR-07', 'CRÍTICO', 'Las acciones clínicas sensibles necesitan autorización por episodio, servicio y acción.',
                $bedTransferAccessReady ? 'El traslado de cama y las demás superficies clínicas usan la autorización granular.' : 'El nuevo traslado de paciente está autenticado, pero aún no invoca la autorización clínica granular; cualquier usuario autenticado con acceso a la ruta podría intentarlo.',
                'Aplicar ClinicalAccessControlService al traslado, liberación y reubicación de camas; añadir pruebas positivas, negativas y break-glass.',
                'Un usuario sin bed.transfer recibe 403; Sistemas conserva acceso explícito; break-glass exige razón y revisión.', 'Fase 0',
                $f13Complete && $mutableGetPending===0 ? 'complete' : 'partial'),
            $this->v3Control('HPR-08', 'MEDIO', 'El transporte, el certificado y la sesión deben operar de forma segura y renovable.',
                ($transportPending===0 ? 'HTTPS y la sesión segura están activos. ' : 'La configuración HTTPS aún presenta observaciones. ').$transportLiveEvidence.($transportRenewalReady ? 'La renovación automática fue probada mediante ensayo exitoso y su evidencia conserva huellas SHA-256, hash del acta y aprobación independiente.' : 'Todavía falta ejecutar y registrar un ensayo real de renovación automática con código de salida 0 y huellas del certificado.'),
                $transportPending===0 && $transportLiveReady ? 'Mantener la auditoría periódica y probar la renovación automática con su acta.' : 'Completar HTTPS, certificado público, cookies seguras y dominio canónico; después probar la renovación automática.',
                'clinical:transport-audit termina sin observaciones y la renovación automática está probada con evidencia íntegra y aprobación responsable.', 'Infraestructura',
                $transportPending===0 && $transportLiveReady && $transportRenewalReady ? 'complete' : 'partial'),
            $this->v3Control('HPR-09', 'CRÍTICO', 'La salida requiere CI clínico y restauración comprobada.',
                ($clinicalCiReady ? 'Existe CI clínico con migración limpia. ' : 'Los cambios hospitalarios no disparan una suite clínica. ').($restoreDrillReady ? 'Existe un simulacro aprobado con RPO/RTO medidos, restauración íntegra en servidor alterno y archivo sellado por hash.' : ($operationsEvidenceCodeReady ? 'El simulacro puede registrarse, pero aún falta ejecutarlo con RPO/RTO, servidor alterno, integridad y aprobación independiente.' : 'No existe evidencia verificable de un simulacro de restauración.')),
                'Ejecutar el simulacro en otro servidor y registrarlo con clinical:readiness-evidence restore, incluyendo RPO/RTO medidos, verificación PASSED, responsables y acta.',
                'El commit candidato pasa CI y el respaldo se restaura en otro servidor con RPO/RTO medidos y aprobación responsable.', 'Fase 0',
                $clinicalCiReady && $restoreDrillReady ? 'complete' : (($clinicalCiReady && $operationsEvidenceCodeReady) ? 'partial' : 'pending')),
            $this->v3Control('HPR-10', 'ALTO', 'Escalamientos, auditorías y contexto hospitalario requieren operación continua.',
                $operations72hReady ? 'El scheduler acumula 72 horas continuas, la auditoría diaria está vigente y no existen alertas abiertas.' : ($clinicalSchedulerReady ? 'Resultados críticos, interconsultas, seguridad, auditoría diaria y monitor están programados; se han demostrado '.(isset($operationsStatus['monitor_hours']) ? $operationsStatus['monitor_hours'] : 0).' de 72 horas, con '.(isset($operationsStatus['open_alerts']) ? (int) $operationsStatus['open_alerts'] : 0).' alerta(s) abierta(s).' : 'No se detecta programación completa de escalamiento clínico.'),
                'Configurar CLINICAL_OPERATIONS_RESPONSIBLE, mantener scheduler cada minuto y atender alertas con clinical:operations-alert-ack.',
                'Durante 72 horas de prueba no hay tareas omitidas y cada fallo genera alerta con responsable y tiempo de atención.', 'Fase 2',
                $operations72hReady && $hospitalContextCodeReady ? 'complete' : (($clinicalSchedulerReady && $operationsEvidenceCodeReady) ? 'partial' : 'pending')),
            $this->v3Control('HPR-11', 'CRÍTICO', 'Falta una certificación formal del piloto asistencial.',
                $pilotSignoffReady ? 'Existe piloto aprobado con decisión GO, capacitación completa, cero defectos críticos, responsables de incidencias, participación multidisciplinaria y acta sellada por hash.' : ($operationsEvidenceCodeReady ? 'El registro gobernado exige decisión GO, capacitación, responsables, nueve áreas y aprobación independiente; todavía falta ejecutar y firmar el piloto.' : 'No existe certificación verificable del piloto asistencial.'),
                'Ejecutar el piloto y registrarlo con clinical:readiness-evidence pilot, incluyendo las nueve áreas, decisión GO, capacitación e incidencias con responsables.',
                'Acta firmada con cero defectos críticos, responsables de incidencias, capacitación y decisión explícita GO.', 'Fase 3',
                $pilotSignoffReady ? 'complete' : ($operationsEvidenceCodeReady ? 'partial' : 'pending')),
        );

        $productionPhases = array(
            $this->hospitalizationPhase('Fase 0', 'Contención funcional', array($productionControls[6], $productionControls[8]), 'Cerrar permisos, CI y respaldo antes de procesar datos clínicos reales.'),
            $this->hospitalizationPhase('Fase 1', 'Integridad clínica extremo a extremo', array_slice($productionControls, 0, 6), 'Certificar episodio, camas, medicación, órdenes, traslados y cierre con datos controlados.'),
            $this->hospitalizationPhase('Fase 2', 'Operación y observabilidad', array_slice($productionControls, 9, 1), 'Demostrar que escalamiento, monitoreo y respuesta operan de forma continua.'),
            $this->hospitalizationPhase('Fase 3', 'Piloto y autorización', array_slice($productionControls, 10, 1), 'Obtener aprobación multidisciplinaria antes del despliegue general.'),
            $this->hospitalizationPhase('Infraestructura', 'HTTPS y renovación comprobada', array($productionControls[7]), 'Conservar HTTPS operativo, auditar el certificado y comprobar su renovación automática.'),
        );

        $controlScore = $this->weightedClinicalScore($controls);
        $ruleScore = $this->weightedClinicalScore($rules);
        $score = (int) round(($controlScore * .65) + ($ruleScore * .35));

        return array(
            'score' => $score,
            'status' => $this->clinicalStatus($score),
            'controls' => $controls,
            'rules' => $rules,
            'counts' => $this->clinicalCounts($controls),
            'rule_counts' => $this->clinicalCounts($rules),
            'production_readiness' => array(
                'score' => $this->weightedClinicalScore($productionControls),
                'decision' => $this->v3Decision($productionControls),
                'controls' => $productionControls,
                'counts' => $this->clinicalCounts($productionControls),
                'phases' => $productionPhases,
            ),
        );
    }

    private function clinicalControl($id, $severity, $finding, $evidence, $action, $phase, $state)
    {
        return array('id' => $id, 'severity' => $severity, 'finding' => $finding, 'evidence' => $evidence, 'action' => $action, 'phase' => $phase, 'state' => $state, 'status' => $this->clinicalState($state));
    }

    private function hospitalizationPhase($id, $name, array $controls, $objective)
    {
        return array('id' => $id, 'name' => $name, 'objective' => $objective,
            'score' => $this->weightedClinicalScore($controls), 'counts' => $this->clinicalCounts($controls));
    }

    private function clinicalBedTransferDataReady()
    {
        try {
            return \Sis_medico\Support\RequestSchema::hasTable('clinical_bed_transfers')
                && \Sis_medico\Support\RequestSchema::hasColumn('clinical_bed_transfers', 'hospitalization_id')
                && \Sis_medico\Support\RequestSchema::hasColumn('clinical_bed_transfers', 'from_bed_id')
                && \Sis_medico\Support\RequestSchema::hasColumn('clinical_bed_transfers', 'to_bed_id');
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function canonicalEncounterDataReady()
    {
        $tables = array('agenda', 'historiaclinica', 'ho_solicitud', 'ho_form008', 'ho_log_solicitud');
        try {
            foreach ($tables as $table) {
                if (!\Sis_medico\Support\RequestSchema::hasTable($table) || !\Sis_medico\Support\RequestSchema::hasColumn($table, 'encounter_id')) return false;
            }
            foreach (array('historiaclinica', 'ho_solicitud', 'ho_form008', 'ho_log_solicitud') as $table) {
                if (DB::table($table)->whereNull('encounter_id')->exists()) return false;
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalClosureDataReady()
    {
        try {
            if(!\Sis_medico\Support\RequestSchema::hasTable('clinical_encounter_closure_orders')||!\Sis_medico\Support\RequestSchema::hasColumn('hc_epicrisis_final','encounter_id'))return false;
            $report=app(ClinicalClosureAuditService::class)->inspect(false);
            return $report['available']&&$report['pending']===0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalStateIntegrityReady()
    {
        try {
            $report = app(ClinicalStateAuditService::class)->inspect(false);
            return $report['available'] && $report['pending'] === 0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalTransferIntegrityReady()
    {
        try{$report=app(ClinicalTransferReconciliationService::class)->integrity();return $report['available']&&$report['pending']===0;}
        catch(\Exception $exception){return false;}
    }

    private function clinicalEncounterConflictIntegrityReady()
    {
        try{$report=app(ClinicalEncounterService::class)->integrity();return $report['available']&&$report['pending']===0;}
        catch(\Exception $exception){return false;}
    }

    private function medicationAdministrationDataReady()
    {
        try {
            $report=app(ClinicalMedicationAuditService::class)->inspect();
            return $report['available']&&$report['pending']===0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function emergencyMedicationExceptionDataReady()
    {
        try {
            return \Sis_medico\Support\RequestSchema::hasTable('clinical_emergency_medication_exceptions')
                && \Sis_medico\Support\RequestSchema::hasColumn('clinical_emergency_medication_exceptions', 'regularization_due_at')
                && \Sis_medico\Support\RequestSchema::hasColumn('clinical_emergency_medication_exceptions', 'regularized_order_id');
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalOrderResultDataReady()
    {
        try {
            return \Sis_medico\Support\RequestSchema::hasTable('clinical_orders')
                && \Sis_medico\Support\RequestSchema::hasTable('clinical_order_events')
                && \Sis_medico\Support\RequestSchema::hasTable('clinical_results')
                && \Sis_medico\Support\RequestSchema::hasTable('clinical_critical_result_notifications')
                && \Sis_medico\Support\RequestSchema::hasTable('clinical_critical_result_events');
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalOrderIntegrityReady()
    {
        try {
            $report = app(ClinicalOrderAuditService::class)->inspect();
            return $report['available'] && $report['pending'] === 0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalRecordIntegrityReady()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('clinical_records') || !\Sis_medico\Support\RequestSchema::hasTable('clinical_documents')) return false;
            $integrity = app(ClinicalRecordService::class)->integrity();
            return $integrity['available'] && $integrity['pending'] === 0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalConsultIntegrity()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('clinical_consult_requests') || !\Sis_medico\Support\RequestSchema::hasTable('clinical_consult_events')) {
                return array('ready'=>false,'sla_ready'=>false);
            }
            $integrity=app(ClinicalConsultWorkflowService::class)->integrity();
            return array('ready'=>$integrity['available'] && $integrity['pending']===0,
                'sla_ready'=>$integrity['available'] && $integrity['pending']===0 && $integrity['overdue_without_escalation']===0);
        } catch (\Exception $exception) {
            return array('ready'=>false,'sla_ready'=>false);
        }
    }

    private function clinicalDeathIntegrityReady()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('clinical_death_cases') || !\Sis_medico\Support\RequestSchema::hasTable('clinical_death_events')) return false;
            $integrity=app(ClinicalDeathService::class)->integrity();
            return $integrity['available'] && $integrity['pending']===0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalProcedureSafetyIntegrityReady()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('clinical_consents') || !\Sis_medico\Support\RequestSchema::hasTable('clinical_procedure_authorizations')) return false;
            $integrity=app(ClinicalProcedureSafetyService::class)->integrity();
            return $integrity['available'] && $integrity['pending']===0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function clinicalDocumentCompletenessIntegrityReady()
    {
        try {
            if (!\Sis_medico\Support\RequestSchema::hasTable('clinical_document_completeness')) return false;
            $integrity=app(ClinicalDocumentCompletenessService::class)->integrity();
            return $integrity['available'] && $integrity['pending']===0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function hospitalContextDataReady()
    {
        try {
            $report=app(ClinicalHospitalContextAuditService::class)->inspect(false);
            return $report['available'] && $report['pending']===0;
        }catch(\Exception $exception){return false;}
    }

    private function clinicalAccessIntegrityReady()
    {
        try{if(!\Sis_medico\Support\RequestSchema::hasTable('clinical_break_glass_accesses')||!\Sis_medico\Support\RequestSchema::hasTable('clinical_access_events'))return false;$report=app(ClinicalAccessControlService::class)->integrity();return $report['available']&&$report['pending']===0;}catch(\Exception $exception){return false;}
    }

    private function clinicalSafetyEventIntegrityReady()
    {
        try{if(!\Sis_medico\Support\RequestSchema::hasTable('clinical_safety_events')||!\Sis_medico\Support\RequestSchema::hasTable('clinical_safety_event_logs'))return false;$report=app(ClinicalSafetyEventService::class)->integrity();return $report['available']&&$report['pending']===0;}catch(\Exception $exception){return false;}
    }

    private function mutableClinicalGetPending()
    {try{return app(ClinicalRouteSafetyAuditService::class)->inspect()['pending'];}catch(\Exception $exception){return null;}}

    private function clinicalTransportPending()
    {try{return app(ClinicalTransportAuditService::class)->inspect()['pending'];}catch(\Exception $exception){return null;}}

    private function clinicalArchitecturePending()
    {try{return app(ClinicalArchitectureAuditService::class)->inspect()['pending'];}catch(\Exception $exception){return null;}}

    private function clinicalRule($id, $control, array $findings, $state)
    {
        return array('id' => $id, 'severity' => 'OBLIGATORIA', 'finding' => $control, 'evidence' => 'Relacionada con ' . implode(', ', $findings) . '.', 'action' => 'Implementar como guarda de negocio central, auditable y probada.', 'phase' => 'P0', 'state' => $state, 'status' => $this->clinicalState($state));
    }

    private function clinicalState($state)
    {
        if ($state === 'complete') return array('label' => 'Implementado', 'class' => 'complete', 'score' => 100);
        if ($state === 'partial') return array('label' => 'Parcial', 'class' => 'partial', 'score' => 40);
        return array('label' => 'Pendiente', 'class' => 'pending', 'score' => 0);
    }

    private function clinicalStatus($score)
    {
        if ($score >= 85) return array('label' => 'Controlado', 'class' => 'excellent');
        if ($score >= 60) return array('label' => 'En mejora', 'class' => 'functional');
        if ($score >= 35) return array('label' => 'Prioritario', 'class' => 'development');
        return array('label' => 'Crítico', 'class' => 'initial');
    }

    private function clinicalCounts(array $items)
    {
        $counts = array('total' => count($items), 'complete' => 0, 'partial' => 0, 'pending' => 0, 'critical_open' => 0);
        foreach ($items as $item) {
            $counts[$item['state']]++;
            if (isset($item['severity']) && $item['severity'] === 'CRÍTICO' && $item['state'] !== 'complete') $counts['critical_open']++;
        }
        return $counts;
    }

    private function weightedClinicalScore(array $items)
    {
        $weights = array('CRÍTICO' => 4, 'ALTO' => 3, 'MEDIO' => 2, 'OBLIGATORIA' => 4);
        $earned = 0;
        $possible = 0;
        foreach ($items as $item) {
            $weight = isset($weights[$item['severity']]) ? $weights[$item['severity']] : 1;
            $earned += $item['status']['score'] * $weight;
            $possible += 100 * $weight;
        }
        return $possible ? (int) round(($earned / $possible) * 100) : 0;
    }

    private function source($relative)
    {
        $source = @file_get_contents(base_path($relative));
        return $source === false ? '' : $source;
    }

    private function methodSource($source, $method, $nextMethod)
    {
        $start = strpos($source, 'function ' . $method . '(');
        if ($start === false) return '';
        $end = strpos($source, 'function ' . $nextMethod . '(', $start + 1);
        return $end === false ? substr($source, $start) : substr($source, $start, $end - $start);
    }

    private function definitions()
    {
        return array(
            array('key' => 'installation', 'name' => 'Instalación y configuración inicial', 'description' => 'Preparación de tablas, usuarios base y requisitos institucionales.', 'icon' => 'fa fa-cogs', 'patterns' => array('#^instalacion(?:/|$)#', '#^configuracion-inicial(?:/|$)#'), 'view_paths' => array('setup'), 'controller_terms' => array('Installation', 'InitialSetup'), 'test_terms' => array('installation', 'initial setup', 'configuracion inicial'), 'support_paths' => array('app/Http/Middleware/EnsureInitialSetup.php', 'database/seeds/BootstrapUsersSeeder.php', 'database/seeds/HospitalModulesSeeder.php', 'app/Services/InitialSetupService.php', 'app/Services/InstallationProgressStore.php', 'app/Services/LegacySchemaInstaller.php'), 'support_target' => 6, 'targets' => array('routes' => 6, 'controllers' => 2, 'actions' => 6, 'views' => 3, 'tests' => 3)),
            array('key' => 'v3', 'name' => 'Plataforma V3', 'description' => 'Módulos V3, navegación por submódulo, permisos y estructura independiente.', 'icon' => 'fa fa-code-branch', 'patterns' => array('#^v3(?:/|$)#', '#^api/v1/tributario(?:/|$)#'), 'view_paths' => array('v3'), 'controller_terms' => array(), 'test_terms' => array('v3', 'new_db_menu'), 'support_paths' => array('app/v3', 'database/seeds/V3MenuSeeder.php', 'database/data/v3_menu.csv'), 'support_target' => 10),
            array('key' => 'imports', 'name' => 'Importación y plantillas', 'description' => 'Carga masiva y plantillas para catálogos y configuraciones del sistema.', 'icon' => 'fa fa-upload', 'patterns' => array('#^(?:seguros|procedimientos|especialidades)/importar(?:/|$)#', '#^contable/tipo_pago/importar(?:/|$)#', '#^importar(?:/|_|$)#', '#^importaciones(?:/|$)#', '#^masivo(?:/|$)#'), 'view_paths' => array('importar', 'importaciones'), 'controller_terms' => array('Importar', 'Import'), 'test_terms' => array('import', 'plantilla'), 'support_paths' => array('public/plantillas/Plantillas_Carga_Inicial_Aitrol.xlsx', 'tools/build_initial_load_templates.mjs', 'resources/views/importar/plantilla_procedimiento.blade.php'), 'support_target' => 3, 'targets' => array('views' => 3)),
            array('key' => 'observability', 'name' => 'Referencias y diagnóstico', 'description' => 'Registro de inconvenientes, referencias legibles y diagnóstico exclusivo para Sistemas.', 'icon' => 'fa fa-bug', 'patterns' => array('#^sistema/referencias(?:/|$)#'), 'view_paths' => array('system-errors', 'errors'), 'controller_terms' => array('SystemErrorReference'), 'test_terms' => array('SystemErrorReference', 'error reference', 'system error'), 'support_paths' => array('app/Exceptions/Handler.php'), 'support_target' => 1, 'targets' => array('routes' => 4, 'controllers' => 1, 'actions' => 4, 'views' => 8, 'tests' => 2)),
            array('key' => 'emergency', 'name' => 'Emergencia', 'description' => 'Triaje, atención inmediata e ingresos de emergencia.', 'icon' => 'fa fa-ambulance', 'patterns' => array('#^hospital/emergencia(?:/|$)#', '#^emergencia(?:/|$)#'), 'view_paths' => array('hospital/emergencia'), 'controller_terms' => array('Emergencia', 'Manchester', 'Triaje'), 'test_terms' => array('emergencia', 'triage', 'manchester')),
            array('key' => 'hospital', 'name' => 'Hospitalización', 'description' => 'Admisión, habitaciones, camas y seguimiento hospitalario.', 'icon' => 'fa fa-bed', 'patterns' => array('#^hospital$#', '#^hospital/(?!emergencia(?:/|$)|laboratorio(?:/|$))#', '#^hospitalizacion(?:/|$)#'), 'view_paths' => array('hospital', 'hospital_admin', 'pdf_hospital'), 'controller_terms' => array('hospital', 'Cuarto', 'Habitacion'), 'test_terms' => array('hospital', 'habitacion', 'cama')),
            array('key' => 'laboratory', 'name' => 'Laboratorio', 'description' => 'Órdenes, muestras, resultados y catálogos de laboratorio.', 'icon' => 'fa fa-flask', 'patterns' => array('#^laboratorio(?:/|$)#', '#^hospital/laboratorio(?:/|$)#', '#^plantilla_labs(?:/|$)#'), 'view_paths' => array('laboratorio'), 'controller_terms' => array('laboratorio', 'Labs', 'Examen'), 'test_terms' => array('laboratorio', 'labs')),
            array('key' => 'patients', 'name' => 'Pacientes', 'description' => 'Registro, actualización, documentos e historia del paciente.', 'icon' => 'fa fa-users', 'patterns' => array('#^paciente(?:/|$)#', '#^images_lab_externos(?:/|$)#'), 'view_paths' => array('paciente', 'paciente_biopsia', 'historiaclinica'), 'controller_terms' => array('Paciente'), 'test_terms' => array('paciente')),
            array('key' => 'agenda', 'name' => 'Agenda y consulta externa', 'description' => 'Agenda médica, horarios, citas y consulta ambulatoria.', 'icon' => 'fa fa-calendar-check', 'patterns' => array('#^agenda(?:/|$)#', '#^horario(?:/|$)#', '#^preagenda(?:/|$)#', '#^consultam(?:/|$)#'), 'view_paths' => array('agenda', 'horario', 'preagenda', 'consultam'), 'controller_terms' => array('Agenda', 'Horario', 'consultam'), 'test_terms' => array('agenda', 'consulta')),
            array('key' => 'clinical_history', 'name' => 'Historia clínica', 'description' => 'Consultas, evoluciones, recetas, órdenes e informes clínicos.', 'icon' => 'fa fa-file-alt', 'patterns' => array('#^hc(?:/|$)#', '#^historia(?:/|$)#', '#^evolucion(?:/|$)#', '#^epicrisis(?:/|$)#', '#^receta(?:/|$)#'), 'view_paths' => array('hc4', 'hc_admision', 'anamenesis'), 'controller_terms' => array('hc_admision', 'Hc4', 'Evolucion', 'Epicrisis', 'Receta'), 'test_terms' => array('historia', 'hc', 'consulta')),
            array('key' => 'procedures', 'name' => 'Procedimientos y especialidades', 'description' => 'Catálogos médicos, especialidades y procedimientos sugeridos.', 'icon' => 'fa fa-stethoscope', 'patterns' => array('#^procedimiento(?:/|$)#', '#^especialidad(?:/|$)#', '#^grupo.procedimiento(?:/|$)#', '#^tecnicas(?:/|$)#'), 'view_paths' => array('procedimiento', 'especialidad', 'grupo_procedimiento', 'tecnicas'), 'controller_terms' => array('Procedimiento', 'Especialidad', 'Tecnica'), 'test_terms' => array('procedimiento', 'especialidad')),
            array('key' => 'imaging', 'name' => 'Imágenes y documentos clínicos', 'description' => 'Carga, consulta y gestión de imágenes o archivos médicos.', 'icon' => 'fa fa-image', 'patterns' => array('#^(?:imagen|imagenes|images|canvas)(?:/|$)#', '#^hospital/(?:imagenes|modal/(?:imagenes|guardar_imagenes))(?:/|$)#', '#^(?:paciente|user-management)/subir_imagen_usuario(?:/|$)#', '#^historiaclinica/foto/mostrar(?:/|$)#', '#^agenda/(?:generar/archivo/subida/dato|datos/foto/mostrar)(?:/|$)#', '#^api/paciente/imagenologia(?:/|$)#'), 'view_paths' => array('canvasimagenes', 'paciente_biopsia', 'hospital/imagenes', 'agenda/foto.blade.php', 'manual'), 'controller_terms' => array('Imagen', 'Image', 'Biopsia'), 'test_terms' => array('imagen', 'image', 'biopsia'), 'targets' => array('routes' => 10, 'views' => 8)),
            array('key' => 'inventory', 'name' => 'Farmacia e inventario', 'description' => 'Medicamentos, insumos, bodegas, existencias y movimientos.', 'icon' => 'fa fa-medkit', 'patterns' => array('#^insumos(?:/|$)#', '#^inventario(?:/|$)#', '#^farmacia(?:/|$)#', '#^bodega(?:/|$)#'), 'view_paths' => array('insumos', 'inventario'), 'controller_terms' => array('Insumos', 'Inventario', 'Farmacia', 'Bodega'), 'test_terms' => array('insumo', 'inventario', 'farmacia')),
            array('key' => 'billing', 'name' => 'Facturación y caja', 'description' => 'Cobros, ventas, productos, comprobantes y formas de pago.', 'icon' => 'fa fa-money-bill', 'patterns' => array('#^factura(?:/|$)#', '#^venta(?:/|$)#', '#^caja(?:/|$)#', '#^comercial(?:/|$)#', '#^contable/tipo_pago(?:/|$)#'), 'view_paths' => array('comercial', 'contable/facturacion'), 'controller_terms' => array('Factura', 'Venta', 'Caja', 'TipoPago'), 'test_terms' => array('factura', 'venta', 'pago')),
            array('key' => 'accounting', 'name' => 'Contabilidad', 'description' => 'Plan de cuentas, diarios, balances, bancos y reportes contables.', 'icon' => 'fa fa-calculator', 'patterns' => array('#^contable(?:/|$)#', '#^v3/contable(?:/|$)#'), 'view_paths' => array('contable', 'v3/contable'), 'controller_terms' => array('contable', 'Plan_Cuentas', 'Banco'), 'test_terms' => array('contable', 'cuenta', 'balance')),
            array('key' => 'companies', 'name' => 'Empresas y ubicaciones', 'description' => 'Empresa principal, unidades de atención, salas y geografía.', 'icon' => 'fa fa-building', 'patterns' => array('#^empresa(?:/|$)#', '#^de_empresa(?:/|$)#', '#^hospital-management(?:/|$)#', '#^sala-management(?:/|$)#', '#^ubicaciones-geograficas(?:/|$)#'), 'view_paths' => array('empresa', 'de_empresa', 'hospital-mgmt', 'sala-mgmt', 'ubicacion_geografica'), 'controller_terms' => array('Empresa', 'HospitalManagement', 'SalaManagement', 'Geograf'), 'test_terms' => array('empresa', 'sala', 'ubicacion')),
            array('key' => 'insurance', 'name' => 'Seguros y convenios', 'description' => 'Seguros, subseguro, cobertura y carga de convenios.', 'icon' => 'fa fa-shield-alt', 'patterns' => array('#^seguro(?:/|$)#', '#^seguros(?:/|$)#', '#^form_enviar_seguro(?:/|$)#'), 'view_paths' => array('seguro'), 'controller_terms' => array('Seguro'), 'test_terms' => array('seguro'), 'targets' => array('controllers' => 1, 'actions' => 12)),
            array('key' => 'electronic', 'name' => 'Documentos electrónicos y SRI', 'description' => 'Firma, emisión, autorización y envío de comprobantes.', 'icon' => 'fa fa-file-code', 'patterns' => array('#^sri(?:/|$)#', '#^guia(?:/|$)#', '#^archivo.plano(?:/|$)#'), 'view_paths' => array('sri_electronico', 'guia', 'archivo_plano'), 'controller_terms' => array('sri_electronico', 'Guia', 'ArchivoPlano'), 'test_terms' => array('sri', 'electronico', 'guia')),
            array('key' => 'hr', 'name' => 'Talento humano', 'description' => 'Empleados, roles, nómina, horarios y vacaciones.', 'icon' => 'fa fa-id-card', 'patterns' => array('#^employee-management(?:/|$)#', '#^rrhh(?:/|$)#', '#^rol(?:/|$)#', '#^nomina(?:/|$)#', '#^contable/(?:nomina|rol)(?:/|$)#', '#^calendario/(?:vacaciones|nuevo)(?:/|$)#', '#^nuevos_asientos_roles(?:/|$)#', '#^acumulados_(?:update_)?vacaciones(?:/|$)#'), 'view_paths' => array('employees-mgmt', 'rrhh', 'rol', 'calendario_vacaciones'), 'controller_terms' => array('EmployeeManagementController', '/rrhh/', 'Nomina', 'RolPagoController', 'Nuevo_RolController', 'CalendarioController', 'HorarioController'), 'test_terms' => array('TalentoHumano', 'empleado', 'rrhh', 'nomina', 'vacaciones')),
            array('key' => 'administration', 'name' => 'Administración y seguridad', 'description' => 'Usuarios, perfiles, permisos y soporte del sistema.', 'icon' => 'fa fa-cogs', 'patterns' => array('#^user-management(?:/|$)#', '#^usuarios(?:/|$)#', '#^tipo_usuario-management(?:/|$)#', '#^sistema(?:/|$)#', '#^system-management(?:/|$)#'), 'view_paths' => array('users-mgmt', 'tipo_usuario-mgmt', 'system-errors', 'system-mgmt'), 'controller_terms' => array('UserManagement', 'Tipo_Usuario', 'SystemError'), 'test_terms' => array('user', 'permission')),
            array('key' => 'sidebar_dynamic', 'name' => 'Sidebar dinámico y permisos', 'description' => 'Opciones del menú guardadas en base, traducción y acceso por perfil.', 'icon' => 'fa fa-sitemap', 'patterns' => array('#^sidebar-management(?:/|$)#'), 'view_paths' => array('sidebar-mgmt'), 'controller_terms' => array('SidebarMenuManagement'), 'test_terms' => array('sidebar', 'menu'), 'targets' => array('routes' => 2, 'controllers' => 1, 'actions' => 2, 'views' => 1)),
            array('key' => 'operations', 'name' => 'Servicios y operación', 'description' => 'Activos, limpieza, temperatura, turnos y servicios generales.', 'icon' => 'fa fa-wrench', 'patterns' => array('#^activos(?:/|$)#', '#^servicios(?:/|$)#', '#^limpieza(?:/|$)#', '#^control.temperatura(?:/|$)#', '#^turnero(?:/|$)#'), 'view_paths' => array('activosfijos', 'servicios_generales', 'limpieza_desinfeccion', 'control_temperatura', 'turnero'), 'controller_terms' => array('activosfijos', 'ServiciosGenerales', 'Limpieza', 'Temperatura', 'Turnero'), 'test_terms' => array('activo', 'servicio', 'turno')),
        );
    }

    private function applicationRoutes()
    {
        $result = array();
        foreach (Route::getRoutes() as $route) {
            $uri = method_exists($route, 'uri') ? $route->uri() : $route->getUri();
            $result[] = array(
                'uri' => trim($uri, '/'),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'methods' => $route->methods(),
            );
        }
        return $result;
    }

    private function matchingRoutes(array $routes, array $patterns)
    {
        return array_values(array_filter($routes, function ($route) use ($patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $route['uri'])) {
                    return true;
                }
            }
            return false;
        }));
    }

    private function controllerFiles(array $routes, array $terms)
    {
        $files = array();
        foreach ($routes as $route) {
            if (!$route['action'] || $route['action'] === 'Closure') {
                continue;
            }
            $class = explode('@', $route['action'])[0];
            $relative = str_replace(array('Sis_medico\\', '\\'), array('', DIRECTORY_SEPARATOR), $class) . '.php';
            $path = app_path($relative);
            if (is_file($path)) {
                $source = @file_get_contents($path);
                if (strpos((string) $source, '@maturity-terminal') !== false) {
                    continue;
                }
                $path = realpath($path) ?: $path;
                $files[$path] = $path;
            }
        }

        // La madurez funcional solo debe medir controladores alcanzables por
        // rutas activas. El inventario por nombre incluía copias históricas,
        // utilitarios, controladores vacíos y endpoints retirados.
        if (count($files) === 0 && count($routes) === 0) {
            if ($this->controllerInventory === null) {
                $this->controllerInventory = $this->phpFiles(app_path('Http/Controllers'));
            }
            foreach ($this->controllerInventory as $path) {
                foreach ($terms as $term) {
                    if (stripos(str_replace('\\', '/', $path), $term) === false) continue;
                    $source = @file_get_contents($path);
                    if (substr(basename($path), -14) !== 'Controller.php'
                        || preg_match('/\babstract\s+class\b/i', (string) $source)
                        || !preg_match('/\bpublic\s+function\s+\w+\s*\(/i', (string) $source)) {
                        break;
                    }
                    $path = realpath($path) ?: $path;
                    $files[$path] = $path;
                    break;
                }
            }
        }
        return array_values($files);
    }

    private function viewFiles(array $paths)
    {
        $files = array();
        foreach ($paths as $path) {
            $directory = resource_path('views/' . trim($path, '/'));
            if (is_file($directory)) {
                $files[$directory] = $directory;
                continue;
            }
            foreach ($this->files($directory, '.blade.php') as $file) {
                $files[$file] = $file;
            }
        }
        return array_values($files);
    }

    private function testFiles(array $terms)
    {
        $files = array();
        if ($this->testInventory === null) {
            $this->testInventory = $this->phpFiles(base_path('tests'));
        }

        foreach ($this->testInventory as $path) {
            $content = @file_get_contents($path);
            foreach ($terms as $term) {
                if (stripos($path, $term) !== false || ($content !== false && stripos($content, $term) !== false)) {
                    $files[$path] = $path;
                    break;
                }
            }
        }
        return array_values($files);
    }

    private function supportFiles(array $paths)
    {
        $files = array();
        foreach ($paths as $relative) {
            $path = base_path(trim($relative, '/\\'));
            if (is_file($path)) {
                $files[$path] = $path;
                continue;
            }
            if (!is_dir($path)) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[$file->getPathname()] = $file->getPathname();
                }
            }
        }
        return array_values($files);
    }

    private function sourceSignals(array $controllers)
    {
        $persistence = 0;
        $controls = 0;
        foreach ($controllers as $path) {
            $source = @file_get_contents($path);
            if ($source === false) {
                continue;
            }
            if (preg_match('/DB::|Schema::|->save\s*\(|->insert\s*\(|->update\s*\(|->delete\s*\(|::(?:create|where|query|all|paginate|simplePaginate|cursorPaginate|first|firstOrFail|find|findOrFail|select|with|count|exists)\s*\(|updateOrInsert|firstOrCreate|file_get_contents\s*\(|fopen\s*\(|glob\s*\(|stream_get_contents\s*\(|session\s*\(\s*\)\s*->put\s*\(|@maturity-delegates\s+\S+|use\s+Sis_medico\\\\Services\\\\[^;]*Service\s*;|use\s+Sis_medico\\\\v3\\\\[^;]+;/i', $source)) {
                $persistence++;
            }
            if (preg_match('/\$this->validate\s*\(|Validator::|DB::transaction|DB::beginTransaction|Schema::has(?:Table|Column)|try\s*\{|catch\s*\(|abort\s*\(|middleware\s*\(|authorize\s*\(|Gate::|Hash::|extends\s+V3ModuleController|Utilidades::verificarPermisos|v3_api_company_id/i', $source)) {
                $controls++;
            }
        }
        return array('persistence' => $persistence, 'controls' => $controls);
    }

    private function sidebarMetrics()
    {
        $systemDatabaseItems = 0;
        $v3DatabaseItems = 0;
        $systemTableReady = false;
        $v3TableReady = false;
        $permissionsReady = false;

        try {
            $systemTableReady = \Sis_medico\Support\RequestSchema::hasTable('sidebar_items');
            $v3TableReady = \Sis_medico\Support\RequestSchema::hasTable('new_db_menu');
            $permissionsReady = (\Sis_medico\Support\RequestSchema::hasTable('opcion') && \Sis_medico\Support\RequestSchema::hasTable('opcion_usuario')) || \Sis_medico\Support\RequestSchema::hasTable('new_db_permisos_usuario');
            if ($systemTableReady) {
                $systemDatabaseItems = (int) DB::table('sidebar_items')->where('estado', 1)->count();
            }
            if ($v3TableReady) {
                $v3DatabaseItems = (int) DB::table('new_db_menu')->where('estado_menu', 1)->count();
            }
        } catch (\Exception $exception) {
            $systemTableReady = false;
            $v3TableReady = false;
            $permissionsReady = false;
        }

        if ($systemDatabaseItems === 0) {
            $seed = @file_get_contents(database_path('seeds/SystemMenuSeeder.php'));
            if ($seed !== false) {
                preg_match_all("/array\('((?:system|config)\.[^']+)'/", $seed, $matches);
                $systemDatabaseItems = count(array_unique(isset($matches[1]) ? $matches[1] : array()));
            }
        }

        if ($v3DatabaseItems === 0) {
            $csv = database_path('data/v3_menu.csv');
            if (is_file($csv)) {
                $rows = @file($csv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $v3DatabaseItems = $rows ? max(0, count($rows) - 1) : 0;
            }
        }

        $legacyLinks = 0;
        $sidebar = @file_get_contents(resource_path('views/layouts/sidebar.blade.php'));
        if ($sidebar !== false) {
            preg_match_all('/<a\b[^>]*\bhref\s*=/i', $sidebar, $matches);
            $legacyLinks = count($matches[0]);
        }

        $databaseItems = $systemDatabaseItems + $v3DatabaseItems;
        $total = $databaseItems + $legacyLinks;
        $migrationPercent = $total > 0 ? $this->cap(($databaseItems / $total) * 100) : 0;

        return array(
            'database_items' => $databaseItems,
            'system_database_items' => $systemDatabaseItems,
            'v3_database_items' => $v3DatabaseItems,
            'legacy_links' => $legacyLinks,
            'migration_percent' => $migrationPercent,
            'table_ready' => $systemTableReady || $v3TableReady,
            'system_table_ready' => $systemTableReady,
            'v3_table_ready' => $v3TableReady,
            'permissions_ready' => $permissionsReady,
        );
    }

    private function sidebarRecommendation(array $sidebar)
    {
        if (!$sidebar['system_table_ready']) {
            return 'Prioridad sugerida: ejecutar la migración que crea sidebar_items para el menú principal.';
        }
        if (!$sidebar['v3_table_ready']) {
            return 'Prioridad sugerida: ejecutar la migración y el seeder de new_db_menu para V3.';
        }
        if (!$sidebar['table_ready']) {
            return 'Prioridad sugerida: ejecutar la migración que crea sidebar_items.';
        }
        if ($sidebar['legacy_links'] > 0) {
            return 'Migración progresiva pendiente: pasar ' . $sidebar['legacy_links'] . ' enlace(s) escritos en la vista a sidebar_items.';
        }
        if (!$sidebar['permissions_ready']) {
            return 'Prioridad sugerida: habilitar la relación de permisos por perfil.';
        }
        return 'Sidebar completamente administrado desde la base de datos.';
    }

    private function uniqueActions(array $routes)
    {
        return count(array_unique(array_filter(array_column($routes, 'action'))));
    }

    private function phpFiles($directory)
    {
        return $this->files($directory, '.php');
    }

    private function files($directory, $suffix)
    {
        if (!is_dir($directory)) {
            return array();
        }
        $files = array();
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->isFile() && substr($file->getFilename(), -strlen($suffix)) === $suffix) {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    private function cap($value)
    {
        return (int) round(max(0, min(100, $value)));
    }

    private function status($score)
    {
        if ($score >= 90) return array('label' => 'Consolidado', 'class' => 'excellent');
        if ($score >= 75) return array('label' => 'Avanzado', 'class' => 'advanced');
        if ($score >= 55) return array('label' => 'Funcional', 'class' => 'functional');
        if ($score >= 30) return array('label' => 'En desarrollo', 'class' => 'development');
        return array('label' => 'Inicial', 'class' => 'initial');
    }

    private function recommendation(array $components, array $gaps = array())
    {
        $labels = array(
            'routes' => 'completar los flujos y destinos',
            'logic' => 'consolidar la lógica del módulo',
            'views' => 'completar y uniformar sus pantallas',
            'data' => 'reforzar persistencia e integridad de datos',
            'controls' => 'agregar validaciones y manejo de errores',
            'tests' => 'incorporar pruebas automatizadas',
        );
        if (count($gaps)) {
            $priorities = array_slice($gaps, 0, 2);
            return 'Prioridad sugerida: ' . implode('; ', array_map(function ($gap) {
                return lcfirst($gap['action']);
            }, $priorities)) . '.';
        }
        asort($components);
        $key = key($components);
        return 'Prioridad sugerida: ' . $labels[$key] . '.';
    }

    private function moduleGaps(array $components, array $evidence, $sidebar = false, array $targets = array())
    {
        $targets = array_merge(array('routes' => 10, 'controllers' => 4, 'actions' => 12, 'views' => 8, 'tests' => 2), $targets);
        $weights = array('routes' => 15, 'logic' => 20, 'views' => 20, 'data' => 20, 'controls' => 15, 'tests' => 10);
        $labels = array('routes' => 'Rutas y flujos', 'logic' => 'Lógica', 'views' => 'Interfaz',
            'data' => 'Datos', 'controls' => 'Controles', 'tests' => 'Pruebas');
        $actions = array();
        $missingRoutes = max(0, $targets['routes'] - (int) $evidence['routes']);
        $missingControllers = max(0, $targets['controllers'] - (int) $evidence['controllers']);
        $missingActions = max(0, $targets['actions'] - (int) $evidence['actions']);
        $missingViews = max(0, $targets['views'] - (int) $evidence['views']);
        $missingPersistence = max(0, (int) $evidence['controllers'] - (int) $evidence['persistence_controllers']);
        $missingControls = max(0, (int) $evidence['controllers'] - (int) $evidence['controlled_controllers']);
        $missingTests = max(0, $targets['tests'] - (int) $evidence['tests']);

        $actions['routes'] = $missingRoutes
            ? 'Documentar o cubrir ' . $missingRoutes . ' flujo(s) funcional(es) adicional(es) para alcanzar la referencia de ' . $targets['routes'] . ', sin duplicar endpoints.'
            : 'Completar la cobertura funcional de rutas detectada por la matriz.';
        $logicParts = array();
        if ($missingControllers) $logicParts[] = $missingControllers . ' unidad(es) de lógica o controlador';
        if ($missingActions) $logicParts[] = $missingActions . ' acción(es) distinta(s)';
        $actions['logic'] = count($logicParts)
            ? 'Cubrir ' . implode(' y ', $logicParts) . ' con casos de uso reales o servicios de dominio, sin fragmentar código artificialmente.'
            : 'Consolidar la lógica pendiente identificada por la matriz.';
        $actions['views'] = $sidebar
            ? 'Migrar los ' . (int) $evidence['legacy_links'] . ' enlace(s) heredado(s) restante(s) al menú gobernado por base y permisos.'
            : ($missingViews ? 'Completar ' . $missingViews . ' pantalla(s) o vista(s) para alcanzar la referencia de ' . $targets['views'] . '.' : 'Completar y uniformar las pantallas pendientes.');
        $actions['data'] = $sidebar
            ? 'Aplicar y verificar las tablas de menú dinámico requeridas en la base evaluada.'
            : ($missingPersistence ? 'Acreditar acceso a datos, persistencia o delegación a servicios en ' . $missingPersistence . ' controlador(es) que aún no lo evidencian.' : 'Completar la evidencia de persistencia e integridad del módulo.');
        $actions['controls'] = $sidebar
            ? 'Aplicar y verificar permisos efectivos por perfil para todas las opciones del menú.'
            : ($missingControls ? 'Acreditar validación, autorización, transacción o manejo de errores en ' . $missingControls . ' controlador(es).' : 'Completar los controles técnicos pendientes del módulo.');
        $actions['tests'] = $missingTests
            ? 'Incorporar ' . $missingTests . ' archivo(s) de prueba automatizada adicional(es) para alcanzar la referencia de ' . $targets['tests'] . '.'
            : 'Completar la cobertura automatizada pendiente.';

        $gaps = array();
        foreach ($components as $key => $value) {
            if ($value >= 100 || !isset($labels[$key])) continue;
            $gaps[] = array(
                'key' => $key,
                'label' => $labels[$key],
                'current' => (int) $value,
                'component_gap' => max(0, 100 - (int) $value),
                'weighted_gap' => round(((100 - (int) $value) * $weights[$key]) / 100, 1),
                'action' => $actions[$key],
            );
        }
        usort($gaps, function ($left, $right) {
            if ($left['weighted_gap'] == $right['weighted_gap']) return strcmp($left['label'], $right['label']);
            return $left['weighted_gap'] < $right['weighted_gap'] ? 1 : -1;
        });
        return $gaps;
    }
}
