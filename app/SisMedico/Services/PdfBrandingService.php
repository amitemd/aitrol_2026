<?php

namespace Sis_medico\Services;

use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Empresa;
use Sis_medico\PdfBrandingSetting;

class PdfBrandingService
{
    private $catalog;

    public function __construct(PdfTemplateCatalogService $catalog)
    {
        $this->catalog = $catalog;
    }

    public function decorate($html, $view, array $data = array())
    {
        $companyId = $this->companyId($data);
        if (!$companyId) {
            return $html;
        }

        $view = $this->catalog->normalizeView($view);
        $setting = null;
        if (Schema::hasTable('pdf_branding_settings')) {
            $setting = PdfBrandingSetting::where('id_empresa', $companyId)
                ->where('view_name', $view)
                ->first();
        }

        $override = isset($data['_pdf_branding_override']) && is_array($data['_pdf_branding_override']) ? $data['_pdf_branding_override'] : array();
        $isPrescription = $view === 'hc_admision.receta.menbretadaor';
        if ($isPrescription && !$setting && !$override && empty($data['_pdf_branding_logo_path'])) {
            return $html;
        }
        if (!(array_key_exists('enabled', $override) ? (bool) $override['enabled'] : (!$setting || $setting->enabled))) {
            return $html;
        }

        $absolutePath = $setting ? $this->logoPathForSetting($setting) : $this->defaultLogoPath($view, $companyId);
        if (!empty($data['_pdf_branding_logo_path']) && $this->imageMime($data['_pdf_branding_logo_path'])) {
            $absolutePath = $data['_pdf_branding_logo_path'];
        }
        if (!$absolutePath) {
            return $html;
        }

        $mime = $this->imageMime($absolutePath);

        $value = function ($name, $fallback) use ($override, $setting) {
            if (array_key_exists($name, $override)) {
                return $override[$name];
            }
            if (!$setting || is_null($setting->getAttribute($name))) {
                return $fallback;
            }
            return $setting->getAttribute($name);
        };
        $positionValue = $value('position', $isPrescription ? 'top-center' : 'top-right');
        $position = in_array($positionValue, array('top-left', 'top-center', 'top-right'), true) ? $positionValue : 'top-right';
        $width = max(15, min(190, (int) $value('width_mm', $isPrescription ? 73 : 38)));
        $height = max(8, min(60, (int) $value('height_mm', $isPrescription ? 21 : 20)));
        $offsetX = max(-60, min(60, (int) $value('offset_x_mm', 0)));
        $offsetY = max(-40, min(80, (int) $value('offset_y_mm', 0)));
        $opacity = max(20, min(100, (int) $value('opacity_percent', 100))) / 100;
        $rotation = max(-20, min(20, (int) $value('rotation_deg', 0)));
        $keepAspect = (bool) $value('keep_aspect_ratio', true);
        $lockContent = (bool) $value('lock_content_position', true);
        $dimensions = @getimagesize($absolutePath);
        $renderedHeight = $height;
        $renderedWidth = $width;
        if ($keepAspect && $dimensions && !empty($dimensions[0]) && !empty($dimensions[1])) {
            $scale = min($width / $dimensions[0], $height / $dimensions[1]);
            $renderedWidth = $dimensions[0] * $scale;
            $renderedHeight = $dimensions[1] * $scale;
        }
        $image = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absolutePath));
        $sizeStyle = 'width:' . $renderedWidth . 'mm;height:' . $renderedHeight . 'mm;max-width:none;max-height:none;';
        $imageTag = '<img src="' . $image . '" style="' . $sizeStyle . 'opacity:' . $opacity . ';transform:rotate(' . $rotation . 'deg);" alt="Logo">';
        $slotToken = '<!--AITROL_PDF_BRANDING_SLOT-->';

        // A document may deliberately reuse another template's branding profile
        // while providing its own slot (for example, the biopsy order inherits
        // the configured prescription letterhead). Honour the slot before the
        // prescription-specific replacement of the legacy static images.
        if (strpos($html, $slotToken) !== false) {
            $alignment = $position === 'top-left' ? 'left' : ($position === 'top-right' ? 'right' : 'center');
            $slotHeight = $lockContent ? 20 : $renderedHeight + max(0, $offsetY) + 2;
            $slotBranding = '<div class="aitrol-pdf-branding-slot" style="position:relative;left:' . $offsetX . 'mm;top:' . $offsetY . 'mm;width:100%;height:' . $height . 'mm;text-align:' . $alignment . ';line-height:0;">'
                . $imageTag . '</div>';
            $html = preg_replace('/<!--AITROL_PDF_BRANDING_FALLBACK_START-->.*?<!--AITROL_PDF_BRANDING_FALLBACK_END-->/s', '', $html);
            $html = str_replace('/*AITROL_PDF_BRANDING_HEIGHT*/55px', $slotHeight . 'mm', $html);
            $html = str_replace('<!--AITROL_PDF_BRANDING_WATERMARK-->', '<img src="' . $image . '" alt="">', $html);
            return str_replace($slotToken, $slotBranding, $html);
        }

        if ($isPrescription) {
            // Replace only the two existing header logos. Keep the original
            // template, table geometry, footer, watermark and patient data intact.
            $original = @getimagesize(public_path('assets/images/logo_receta_borja.png'));
            $slotHeight = $original && $original[0] ? min(126, 430 * $original[1] / $original[0]) : 126;
            $horizontal = $position === 'top-left' ? 'left:' . $offsetX . 'mm;'
                : ($position === 'top-right' ? 'right:' . (-$offsetX) . 'mm;'
                : 'left:50%;margin-left:' . (-$renderedWidth / 2 + $offsetX) . 'mm;');
            $replacement = '<div style="position:relative;width:100%;height:' . $slotHeight . 'px;line-height:0;">'
                . '<div style="position:absolute;top:' . $offsetY . 'mm;' . $horizontal . 'width:' . $renderedWidth . 'mm;">' . $imageTag . '</div></div>';
            return preg_replace_callback('~<img\b[^>]*\bsrc=["\'][^"\']*[/\\\\]logo_receta_borja\.png["\'][^>]*>~i', function () use ($replacement) {
                return $replacement;
            }, $html);
        }

        $positionType = (bool) $value('repeat_on_each_page', false) ? 'fixed' : 'absolute';
        $horizontal = $position === 'top-left'
            ? 'left:' . (12 + $offsetX) . 'mm;'
            : ($position === 'top-center' ? 'left:50%;margin-left:' . ((-$renderedWidth / 2) + $offsetX) . 'mm;' : 'right:' . (12 - $offsetX) . 'mm;');
        $branding = '<div class="aitrol-pdf-branding" style="position:' . $positionType . ';top:' . (8 + $offsetY) . 'mm;' . $horizontal . 'width:' . $renderedWidth . 'mm;z-index:9999;line-height:0;">'
            . $imageTag
            . '</div>';

        if (preg_match('/<body\b[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = $matches[0][1] + strlen($matches[0][0]);
            return substr($html, 0, $offset) . $branding . substr($html, $offset);
        }

        return $branding . $html;
    }

    public function logoPathForSetting(PdfBrandingSetting $setting)
    {
        if ($setting->logo_path) {
            $configuredPath = storage_path('app/' . ltrim($setting->logo_path, '/\\'));
            if ($this->imageMime($configuredPath)) {
                return $configuredPath;
            }
        }

        return $this->defaultLogoPath($setting->view_name, $setting->id_empresa);
    }

    public function defaultLogoPath($view, $companyId)
    {
        if ($view === 'hc_admision.receta.menbretadaor') {
            $path = public_path('assets/images/logo_receta_borja.png');
            return $this->imageMime($path) ? $path : null;
        }
        return $this->companyLogoPath($companyId);
    }

    public function companyLogoPath($companyId)
    {
        $company = Empresa::find($companyId);
        if (!$company) {
            return null;
        }

        foreach (array('logo', 'logo_form') as $attribute) {
            $filename = basename((string) $company->getAttribute($attribute));
            if ($filename === '') {
                continue;
            }
            $path = storage_path('app/logo/' . $filename);
            if ($this->imageMime($path)) {
                return $path;
            }
        }

        return null;
    }

    private function imageMime($path)
    {
        if (!$path || !is_file($path)) {
            return null;
        }
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';
        return in_array($mime, array('image/png', 'image/jpeg', 'image/gif'), true) ? $mime : null;
    }

    public function companyId(array $data)
    {
        if (!empty($data['_pdf_branding_company_id'])) {
            return (string) $data['_pdf_branding_company_id'];
        }
        foreach (array('empresa', 'empresa1') as $key) {
            if (isset($data[$key]) && is_object($data[$key]) && isset($data[$key]->id)) {
                return (string) $data[$key]->id;
            }
        }
        if (function_exists('session') && session()->has('id_empresa')) {
            return (string) session('id_empresa');
        }
        return null;
    }
}
