<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\CashManagement\Application\Queries\DescribeCashManagement;
use Illuminate\Contracts\View\View;

final class CashManagementController extends Controller
{
    public function __construct(private readonly DescribeCashManagement $query) {}

    public function __invoke(): View
    {
        return view('operations.show', [
            'title' => __('new_system.operations.cash_title'),
            'moduleStatus' => $this->query->handle(),
        ]);
    }
}
