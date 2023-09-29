<?php

namespace Lunar\Hub\Listeners;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Str;
use Lunar\Hub\Http\Middleware\Authenticate;

class SetStaffAuthMiddlewareListener
{
    protected RouteMatched $event;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(RouteMatched $event)
    {
        $this->event = $event;

        if (! $this->routeAlreadyHasStaffMiddleware() && $this->shouldApplyStaffMiddleware()) {
            $event->route->middleware(
                array_merge($event->route->middleware(), ['auth:staff'])
            );
        }
    }

    /**
     * Check if the route already has the staff middleware.
     */
    protected function routeAlreadyHasStaffMiddleware(): bool
    {
        return collect($this->event->route->middleware())->flip()->has(Authenticate::class);
    }

    /**
     * Matches livewire routes which are allowed to use auth:staff middleware.
     */
    protected function shouldApplyStaffMiddleware(): bool
    {
        return $this->isLivewireRoute() && ($this->onlyHubRoutes() && ! $this->disallowRoutes());
    }

    /**
     * Matches hub routes.
     */
    protected function onlyHubRoutes(): bool
    {
        return Str::of($this->event->route->name)->contains([
            'hub.pages',
            'hub.components',
            'lunar',
        ]);
    }

    /**
     * List of livewire routes which are not allowed to use auth:staff middleware.
     */
    protected function disallowRoutes(): bool
    {
        return Str::of($this->event->route->name)->contains([
            'login',
            'password-reset',
        ]);
    }

    /**
     * Matches livewire route which use dot separator.
     */
    protected function isLivewireRoute(): bool
    {
        return str_starts_with($this->event->route->getName(), 'livewire.');
    }
}
