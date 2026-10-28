<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\Laboratory\Application\Queries\DescribeLaboratory;
use Illuminate\Contracts\View\View;

final class LaboratoryController extends Controller
{
    public function __construct(private readonly DescribeLaboratory $query) {}

    public function __invoke(): View
    {
        return view('operations.show', [
            'title' => __('new_system.operations.laboratory_title'),
            'moduleStatus' => $this->query->handle(),
        ]);
    }
}
