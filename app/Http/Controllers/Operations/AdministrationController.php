<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\Administration\Application\Queries\DescribeAdministration;
use Illuminate\Contracts\View\View;

final class AdministrationController extends Controller
{
    public function __construct(private readonly DescribeAdministration $query) {}

    public function __invoke(): View
    {
        return view('operations.show', [
            'title' => 'Administración, empresas y usuarios',
            'moduleStatus' => $this->query->handle(),
        ]);
    }
}
