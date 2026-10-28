<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\PatientBiopsy\Application\Queries\DescribePatientBiopsy;
use Illuminate\Contracts\View\View;

final class PatientBiopsyController extends Controller
{
    public function __construct(private readonly DescribePatientBiopsy $query) {}

    public function __invoke(): View
    {
        return view('operations.show', [
            'title' => __('new_system.operations.patients_title'),
            'moduleStatus' => $this->query->handle(),
        ]);
    }
}
