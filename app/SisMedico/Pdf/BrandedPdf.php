<?php

namespace Sis_medico\Pdf;

use Barryvdh\DomPDF\PDF;
use Sis_medico\Services\PdfBrandingService;

class BrandedPdf extends PDF
{
    private $branding;
    private $previewReference;

    public function setBrandingService(PdfBrandingService $branding)
    {
        $this->branding = $branding;
        return $this;
    }

    public function loadView(string $view, array $data = array(), array $mergeData = array(), ?string $encoding = null): self
    {
        $brandingView = isset($data['_pdf_branding_view']) ? $data['_pdf_branding_view'] : $view;
        unset($data['_pdf_branding_view']);

        $html = $this->view->make($view, $data, $mergeData)->render();
        $this->previewReference = null;
        if (!isset($data['_pdf_branding_override']) && $this->branding) {
            $this->previewReference = array(
                'view' => $brandingView,
                'company' => $this->branding->companyId($data),
                'html' => $html,
            );
        }
        if ($this->branding) {
            $html = $this->branding->decorate($html, $brandingView, $data);
        }
        return parent::loadHTML($html, $encoding);
    }

    public function loadHTML(string $string, ?string $encoding = null): self
    {
        $this->previewReference = null;
        return parent::loadHTML($string, $encoding);
    }

    public function render(): void
    {
        $reference = $this->previewReference;
        if ($reference) {
            $reference['paper'] = $this->dompdf->getPaperSize();
            $reference['orientation'] = $this->dompdf->getPaperOrientation();
            // getPaperSize already applies landscape; setPaper will apply it again.
            if ($reference['orientation'] === 'landscape') {
                $reference['paper'] = array($reference['paper'][0], $reference['paper'][1], $reference['paper'][3], $reference['paper'][2]);
            }
            $reference['options'] = clone $this->dompdf->getOptions();
            $reference['basePath'] = $this->dompdf->getBasePath();
            $reference['baseHost'] = $this->dompdf->getBaseHost();
            $reference['protocol'] = $this->dompdf->getProtocol();
        }
        parent::render();
        if ($reference) {
            try {
                app(\Sis_medico\Services\PdfBrandingPreviewStore::class)->put($reference['view'], $reference['company'], $reference);
            } catch (\Throwable $exception) {
                // A cache failure must never prevent issuing the original document.
                report($exception);
            }
        }
    }
}
