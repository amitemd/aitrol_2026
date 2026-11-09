<?php

namespace Sis_medico\Services;

use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Support\RequestQueryCache;
use Sis_medico\Empresa;

class TenantContext
{
    const SELECTION_REQUIRED_KEY = 'tenant_selection_required';
    const SELECTION_CONFIRMED_KEY = 'tenant_selection_confirmed';

    /**
     * Empresas a las que puede acceder el usuario autenticado.
     *
     * Un usuario normal sin una asignacion explicita no recibe ninguna empresa.
     * Solo el rol Sistemas conserva acceso de recuperacion a la empresa
     * principal para poder corregir asignaciones antiguas.
     */
    public function availableFor($user)
    {
        if (!$user) {
            return collect();
        }

        // Compartido entre las instancias del middleware y los servicios, solo en esta petición.
        return RequestQueryCache::remember(__METHOD__.json_encode([$user->id, $user->id_tipo_usuario ?? null]), function () use ($user) {
            return $this->loadAvailableFor($user);
        }, null, false, ['empresa', 'usuario_empresa', 'tipousuario']);
    }

    private function loadAvailableFor($user)
    {
        if (!$this->tablesAreReady()) {
            return collect();
        }

        $companies = Empresa::query()
            ->join('usuario_empresa', 'empresa.id', '=', 'usuario_empresa.id_empresa')
            ->where('usuario_empresa.id_usuario', $user->id)
            ->where('empresa.estado', 1)
            ->select('empresa.*')
            ->distinct()
            ->orderBy('empresa.prioridad', 'asc')
            ->orderBy('empresa.nombrecomercial', 'asc')
            ->get();

        if ($companies->isEmpty() && method_exists($user, 'isSystemUser') && $user->isSystemUser()) {
            $principal = Empresa::where('prioridad', 1)
                ->where('estado', 1)
                ->first();
            if ($principal) {
                $companies = collect(array($principal));
            }
        }

        return $companies;
    }

    /**
     * Resuelve y guarda la empresa activa de la sesion.
     */
    public function resolve($request, $user = null)
    {
        $user = $user ?: auth()->user();
        $companies = $this->availableFor($user);

        if ($companies->isEmpty()) {
            $request->session()->forget(array('id_empresa', 'tenant_id'));
            return null;
        }

        $selectedId = (string) $request->session()->get('id_empresa', '');
        $current = $companies->first(function ($company) use ($selectedId) {
            return (string) $company->id === $selectedId;
        });

        if (!$current) {
            $current = $companies->first();
        }

        $request->session()->put('id_empresa', $current->id);
        $request->session()->put('tenant_id', $current->id);

        return $current;
    }

    /**
     * Cambia la empresa solo si esta asignada al usuario.
     */
    public function switchTo($request, $user, $companyId)
    {
        $company = $this->availableFor($user)->first(function ($available) use ($companyId) {
            return (string) $available->id === (string) $companyId;
        });

        if (!$company) {
            return null;
        }

        $request->session()->put('id_empresa', $company->id);
        $request->session()->put('tenant_id', $company->id);

        return $company;
    }

    /**
     * Prepara el contexto empresarial después de completar la autenticación.
     * Una sola empresa se activa de inmediato; varias requieren una elección
     * explícita antes de entrar a cualquier panel.
     */
    public function beginAuthenticatedSession($request, $user)
    {
        $companies = $this->availableFor($user);

        $request->session()->forget(array(
            'id_empresa', 'tenant_id', self::SELECTION_REQUIRED_KEY, self::SELECTION_CONFIRMED_KEY,
        ));

        if ($companies->count() > 1) {
            $request->session()->put(self::SELECTION_REQUIRED_KEY, true);
            return true;
        }

        $request->session()->forget(self::SELECTION_REQUIRED_KEY);
        if ($companies->count() === 1) {
            $this->switchTo($request, $user, $companies->first()->id);
        }

        return false;
    }

    public function selectionIsRequired($request)
    {
        return (bool) $request->session()->get(self::SELECTION_REQUIRED_KEY, false);
    }

    /**
     * Exige la pantalla dedicada incluso para sesiones antiguas que ya tenían
     * una empresa activa antes de habilitar el flujo multempresa.
     */
    public function requireSelectionWhenNeeded($request, $companies)
    {
        if ($companies->count() <= 1) {
            $request->session()->forget(array(self::SELECTION_REQUIRED_KEY, self::SELECTION_CONFIRMED_KEY));
            return false;
        }

        if (!$request->session()->get(self::SELECTION_CONFIRMED_KEY, false)) {
            $request->session()->put(self::SELECTION_REQUIRED_KEY, true);
        }

        return $this->selectionIsRequired($request);
    }

    public function confirmSelection($request)
    {
        $request->session()->forget(self::SELECTION_REQUIRED_KEY);
        $request->session()->put(self::SELECTION_CONFIRMED_KEY, true);
    }

    public function id($request = null)
    {
        $request = $request ?: request();
        $company = $this->resolve($request);

        return $company ? $company->id : null;
    }

    private function tablesAreReady()
    {
        try {
            return Schema::hasTable('empresa')
                && Schema::hasTable('usuario_empresa')
                && Schema::hasTable('users');
        } catch (\Exception $exception) {
            return false;
        }
    }
}
