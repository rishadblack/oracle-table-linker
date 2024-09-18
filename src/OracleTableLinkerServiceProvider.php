<?php

namespace Rishadblack\OracleTableLinker;

use Illuminate\Support\ServiceProvider;
use Rishadblack\OracleTableLinker\Commands\SetDbLinkCommand;

class OracleTableLinkerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // dd('OracleTableLinkerServiceProvider');
        // Only register commands when the application is running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetDbLinkCommand::class,
            ]);

            // Publishing the configuration file (optional, remove if not needed)
            $this->publishes([
                __DIR__ . '/../config/oracle-table-linker.php' => config_path('oracle-table-linker.php'),
            ], 'oracle-table-linker-config');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        // If you have any package services, register them here
        $this->app->singleton('oracle-table-linker', function ($app) {
            return new OracleTableLinker();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['oracle-table-linker'];
    }
}
