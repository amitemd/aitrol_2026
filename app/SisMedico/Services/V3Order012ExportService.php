<?php

namespace Sis_medico\Services;

class V3Order012ExportService
{
    private $orders;
    private $exporter;

    public function __construct(V3Order012Service $orders, ClinicalOrder012ExportService $exporter)
    {
        $this->orders = $orders;
        $this->exporter = $exporter;
    }

    public function export($id, $companyId)
    {
        $this->orders->find($id, $companyId);
        return $this->exporter->export($id);
    }
}
