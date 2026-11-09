<?php

namespace Sis_medico\Services;

class PatientLibraryAccess
{
    public function allows($user, $company): bool
    {
        return $user && $company && ($company->tipo_empresa ?? null) === 'hospitalario'
            && ($user->isSystemUser() || $user->hasUserTypeName(['RECEPCION', 'RECEPCIÓN']));
    }

    public function company($request)
    {
        $selected = (string) $request->session()->get('id_empresa', '');
        $company = app(TenantContext::class)->availableFor($request->user())->first(function ($company) use ($selected) {
            return (string) $company->id === $selected;
        });
        abort_unless($this->allows($request->user(), $company), 403, 'Biblioteca disponible para Sistema y Recepción en empresas hospitalarias.');
        return $company;
    }
}
