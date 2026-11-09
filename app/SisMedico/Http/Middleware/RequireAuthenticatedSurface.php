<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sis_medico\Services\InitialSetupService;
use Sis_medico\Services\InstallationProgressStore;
use Sis_medico\Services\SecurityAccessAuditService;

/**
 * Close every web route unless it has an authenticated session, belongs to
 * the minimum sign-in/recovery surface, or carries its own V3 API guard.
 */
class RequireAuthenticatedSurface
{
    private $securityAudit;

    public function __construct(SecurityAccessAuditService $securityAudit)
    {
        $this->securityAudit = $securityAudit;
    }

    public function handle($request, Closure $next)
    {
        // Some installed clients and stale compiled views still point to the
        // historical "new patient" URL.  Resolve it before inspecting the
        // controller action so a stale route cache cannot send this safe,
        // exact compatibility URL into the quarantined AgendaController.
        if ($this->isLegacyPatientCreateRoute($request)) {
            return redirect()->route('v3.patients.create')
                ->header('Cache-Control', 'no-store, private');
        }

        $quarantinedScope = $this->quarantinedControllerScope($request);
        if ($quarantinedScope !== null) {
            $this->securityAudit->record('blocked_legacy_endpoint', 'blocked', $request, null,
                array('reason' => 'Controlador heredado en cuarentena: ' . $quarantinedScope));

            Log::warning('Quarantined legacy controller denied by the global web firewall.', array(
                'scope' => $quarantinedScope,
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ));

            return response()->json(array('error' => 'Recurso no disponible.'), 404)
                ->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }

        if (Str::is('sub_login*', trim((string) $request->path(), '/'))) {
            $this->securityAudit->record('blocked_legacy_login', 'blocked', $request, null,
                array('reason' => 'Ruta sub_login retirada'));

            return response()->json(array('error' => 'Recurso no disponible.'), 404)
                ->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }

        if (Auth::check() || $this->isAuthenticationRoute($request)
            || $this->isPublicClinicalSignatureVerification($request)
            || $this->hasV3ApiCredentialMiddleware($request)
            || $this->isUnfinishedOneTimeInstallation($request)) {
            return $next($request);
        }

        $this->securityAudit->record('unauthenticated_route', 'blocked', $request, null,
            array('reason' => 'La ruta requiere una sesión autenticada'));

        Log::warning('Unauthenticated request denied by the global web firewall.', array(
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->header('User-Agent'), 0, 255),
        ));

        if ($request->expectsJson() || Str::startsWith($request->path(), array('api/', 'apis/'))) {
            return response()->json(array('error' => 'Autenticación requerida.'), 401)
                ->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }

        return redirect()->guest(route('login'))
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * Keep risky legacy controllers closed independently from their URL.
     */
    private function quarantinedControllerScope($request)
    {
        $route = $request->route();
        if (!is_object($route) || !method_exists($route, 'getActionName')) {
            return null;
        }

        $action = ltrim((string) $route->getActionName(), '\\');
        $controllers = array(
            'Sis_medico\\Http\\Controllers\\AgendaController@' => 'agenda',
            'Sis_medico\\Http\\Controllers\\PreAgendaController@' => 'preagenda',
            'Sis_medico\\Http\\Controllers\\bo\\' => 'back_office',
            'Sis_medico\\Http\\Controllers\\ApiHumanlabs@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BdConsultasProcController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BdEstadisticosEndoscopyController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BdIecedController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BdLabsController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BdReportesRhController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\BigDataController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\LabsDoctorController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\MaquinasController@' => 'shared_secret_controller',
            'Sis_medico\\Http\\Controllers\\OrdenesDoctorController@' => 'shared_secret_controller',
        );

        foreach ($controllers as $controller => $scope) {
            if (Str::startsWith($action, $controller)) {
                return $scope;
            }
        }

        return null;
    }

    private function isAuthenticationRoute($request)
    {
        $method = strtoupper($request->method());
        $path = trim((string) $request->path(), '/');
        $allowed = array(
            'GET' => array('', 'login', 'password/reset', 'password/reset/*',
                'user/verification/reset', 'user/password/recover', 'doble-factor',
                'v3/recuperar/authenticator', 'v3/recuperar/nueva-clave', 'v3/acceso/authenticator'),
            'POST' => array('login', 'password/email', 'password/reset', 'user/password/recover',
                'doble-factor', 'doble-factor/cancelar', 'v3/recuperar/authenticator',
                'v3/recuperar/nueva-clave', 'v3/recuperar/cancelar', 'v3/acceso/authenticator'),
        );

        foreach (isset($allowed[$method]) ? $allowed[$method] : array() as $pattern) {
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    private function isLegacyPatientCreateRoute($request)
    {
        if (strtoupper($request->method()) !== 'GET') {
            return false;
        }

        return in_array(trim((string) $request->path(), '/'), array(
            'agenda/paciente/0/0/0',
            'agenda/paciente/0/0/0/0',
        ), true);
    }

    private function isPublicClinicalSignatureVerification($request)
    {
        return strtoupper($request->method()) === 'GET'
            && trim((string) $request->path(), '/') === 'validar/firma-clinica';
    }

    private function hasV3ApiCredentialMiddleware($request)
    {
        $route = $request->route();
        if (!is_object($route) || !method_exists($route, 'middleware')) {
            return false;
        }

        foreach ((array) $route->middleware() as $middleware) {
            if (Str::startsWith((string) $middleware, 'v3.api.client:')) {
                return true;
            }
        }

        return false;
    }

    private function isUnfinishedOneTimeInstallation($request)
    {
        if (!Str::is('instalacion*', trim((string) $request->path(), '/'))) {
            return false;
        }

        if (!(bool) config('authenticated_surface.installation_enabled', false)) {
            return false;
        }

        try {
            $state = app(InstallationProgressStore::class)->read();
            $bootstrapReady = app(InitialSetupService::class)->bootstrapIsReady();
            if ($bootstrapReady
                && (!empty($state['completed']) || $state['phase'] === 'complete')) {
                return false;
            }

            return !$bootstrapReady
                || in_array($state['status'], array('idle', 'running', 'pending', 'error'), true);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
