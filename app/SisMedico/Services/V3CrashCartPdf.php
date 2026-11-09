<?php

namespace Sis_medico\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class V3CrashCartPdf
{
    public function render(array $data)
    {
        $options = array_merge((array) app('dompdf.options'), array('defaultPaperSize' => 'a3',
            'defaultPaperOrientation' => 'landscape', 'pdfBackend' => 'CPDF',
            'isRemoteEnabled' => false, 'isPhpEnabled' => false, 'isJavascriptEnabled' => false));
        // Keep only the PDF drawing commands between blocks, not a full HTML/CSS
        // tree for every monthly cell and every daily detail at the same time.
        $owner = new Dompdf(new Options($options));
        $canvas = $owner->getCanvas();
        $first = true;
        foreach ($this->parts($data) as $part) {
            if (!$first) $canvas->new_page();
            $renderer = new Dompdf(new Options($options));
            $renderer->setBasePath(public_path());
            $renderer->setCanvas($canvas);
            $renderer->getFontMetrics()->setCanvas($canvas);
            $renderer->loadHtml(view('v3.clinical.crash_cart.pdf', $part)->render(), 'UTF-8');
            $renderer->render();
            $first = false;
            unset($renderer);
            gc_collect_cycles();
        }
        return $canvas->output();
    }

    private function parts(array $data)
    {
        $chunks = array_chunk($data['matrix'], 12);
        if (!$chunks) $chunks = array(array());
        foreach ($chunks as $index => $chunk) {
            yield array_merge($data, array('pdfPart' => 'matrix', 'matrix' => $chunk,
                'matrixOffset' => $index * 12, 'matrixTotal' => count($data['matrix'])));
        }
        foreach ($data['records'] as $record) {
            $chunks = $data['reportDetails']->get($record->id, collect())->chunk(35);
            // Retain the record even if an imported control has no detail rows.
            if ($chunks->isEmpty()) $chunks = collect(array(collect()));
            foreach ($chunks as $index => $chunk) {
                yield array_merge($data, array('pdfPart' => 'detail', 'records' => collect(array($record->id => $record)),
                    'reportDetails' => collect(array($record->id => $chunk)),
                    'detailContinuation' => $index > 0, 'detailLast' => $index === $chunks->count() - 1));
            }
        }
    }
}
