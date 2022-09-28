<?php

namespace Dongrim\DatatableInertia;

use Illuminate\Support\ServiceProvider;
use Inertia\Response as InertiaResponse;
use Dongrim\DatatableInertia\Console\Commands\DatatableInertiaCommand;

class DatatableInertiaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishResource();
        }
        $this->bootingResource();
    }


    public function register(): void
    {
        $this->commands([
            DatatableInertiaCommand::class
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'datatables');
    }


    protected function publishResource(): void
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('datatables.php'),
        ], 'config');
    }

    protected function bootingResource(): void
    {
        InertiaResponse::macro('table', function ($datatable) {
            return app(DatatableInertia::class)
                ->with($datatable)
                ->applyTo($this);
        });
    }
}
