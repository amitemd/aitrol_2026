<?php

namespace App\Modules\Shared\Infrastructure\Routing;

use App\Modules\Shared\Application\Contracts\RouteReadinessGateway;
use App\Modules\Shared\Application\Data\OperationalWorkflowStatus;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;
use Closure;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;

final readonly class LaravelRouteReadinessGateway implements RouteReadinessGateway
{
    public function __construct(
        private Router $router,
        private UrlGenerator $url,
    ) {}

    public function inspect(OperationalWorkflow $workflow): OperationalWorkflowStatus
    {
        $route = $this->router->getRoutes()->getByName($workflow->routeName);

        if (! $route instanceof Route) {
            return new OperationalWorkflowStatus(
                workflow: $workflow,
                routeExists: false,
                controllerCallable: false,
                methods: [],
                uri: null,
                url: null,
                action: null,
            );
        }

        $action = $route->getActionName();

        return new OperationalWorkflowStatus(
            workflow: $workflow,
            routeExists: true,
            controllerCallable: $this->isCallableAction($route, $action),
            methods: array_values(array_diff($route->methods(), ['HEAD'])),
            uri: $route->uri(),
            url: $this->url->route($workflow->routeName, $workflow->routeParameters),
            action: $action,
        );
    }

    private function isCallableAction(Route $route, string $action): bool
    {
        if ($route->getAction('uses') instanceof Closure) {
            return true;
        }

        if (! str_contains($action, '@')) {
            return is_callable($action);
        }

        [$controller, $method] = explode('@', $action, 2);

        return class_exists($controller) && method_exists($controller, $method);
    }
}
