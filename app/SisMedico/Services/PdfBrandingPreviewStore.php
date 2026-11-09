<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/** Short-lived reference, isolated by authenticated session and company. */
class PdfBrandingPreviewStore
{
    private function key($view, $companyId)
    {
        if (!app()->bound('request') || !request()->hasSession() || !auth()->check() || !$companyId) {
            return null;
        }
        return 'pdf-branding-preview:' . hash('sha256', session()->getId() . '|' . auth()->id() . '|' . $companyId . '|' . $view);
    }

    public function put($view, $companyId, array $reference)
    {
        if ($key = $this->key($view, $companyId)) {
            Cache::put($key, Crypt::encrypt($reference), now()->addMinutes(30));
        }
    }

    public function get($view, $companyId)
    {
        $key = $this->key($view, $companyId);
        $value = $key ? Cache::get($key) : null;
        return $value ? Crypt::decrypt($value) : null;
    }
}
