<?php

namespace Sis_medico\Services;

use Sis_medico\Empresa;

class CompanyPdfLogoService
{
    public function path($company = null)
    {
        if (! $company && function_exists('session') && session()->has('id_empresa')) {
            $company = Empresa::find(session('id_empresa'));
        }
        if (! $company) {
            return null;
        }

        // Clínica Borja utiliza el membrete horizontal institucional en todos
        // los formularios clínicos, independientemente del logo cuadrado que
        // pueda estar cargado para navegación u otros módulos.
        if ($this->isBorja($company)) {
            $borjaLogo = public_path('assets/images/logo_receta_borja.png');
            if ($this->validImage($borjaLogo)) {
                return $borjaLogo;
            }
        }

        foreach (['logo_form', 'logo'] as $attribute) {
            $filename = basename(str_replace('\\', '/', (string) $company->getAttribute($attribute)));
            if ($filename === '') {
                continue;
            }
            $path = storage_path('app/logo/'.$filename);
            if ($this->validImage($path)) {
                return $path;
            }
        }

        return null;
    }

    public function dataUri($company = null)
    {
        $path = $this->path($company);
        if (! $path) {
            return null;
        }
        $mime = $this->mime($path);

        return $mime ? 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path)) : null;
    }

    public function mime($path)
    {
        if (! $path || ! is_file($path)) {
            return null;
        }
        $info = @getimagesize($path);
        $mime = $info && ! empty($info['mime']) ? $info['mime'] : null;

        return in_array($mime, ['image/png', 'image/jpeg', 'image/gif'], true) ? $mime : null;
    }

    private function validImage($path)
    {
        return $this->mime($path) !== null;
    }

    private function isBorja($company)
    {
        if ((string) $company->id === '0992914246001') {
            return true;
        }
        $name = trim(implode(' ', array_filter([
            $company->getAttribute('nombrecomercial'),
            $company->getAttribute('razonsocial'),
            $company->getAttribute('nombre_corto'),
        ])));

        return stripos($name, 'BORJA') !== false;
    }
}
