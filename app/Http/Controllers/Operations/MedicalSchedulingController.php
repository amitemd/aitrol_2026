<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\MedicalScheduling\Application\Queries\DescribeMedicalScheduling;
use Illuminate\Contracts\View\View;

final class MedicalSchedulingController extends Controller
{
    public function __construct(private readonly DescribeMedicalScheduling $query) {}

    public function __invoke(): View
    {
        return view('operations.show', [
            'title' => __('new_system.operations.medical_title'),
            'moduleStatus' => $this->query->handle(),
        ]);
    }
}
