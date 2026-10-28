<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Application\Queries\FindBoundedContext;
use Illuminate\Contracts\View\View;

final class ArchitectureContextController extends Controller
{
    public function __construct(private readonly FindBoundedContext $query) {}

    public function __invoke(string $contextKey): View
    {
        $context = $this->query->handle($contextKey);

        abort_if($context === null, 404, 'El contexto solicitado no existe en el mapa modular.');

        return view('operations.context', [
            'title' => $context->name,
            'context' => $context,
        ]);
    }
}
