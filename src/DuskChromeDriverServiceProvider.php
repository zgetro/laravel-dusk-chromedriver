<?php

namespace DuskChromeDriver;

use Illuminate\Support\ServiceProvider;
use DuskChromeDriver\Commands\DownloadChromeDriver;

class DuskChromeDriverServiceProvider extends ServiceProvider
{
    /**
     * Register the application's command.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DownloadChromeDriver::class,
            ]);
        }
    }
}
