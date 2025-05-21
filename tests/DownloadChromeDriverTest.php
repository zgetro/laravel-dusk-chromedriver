<?php

namespace DuskChromeDriver\Tests;

use Orchestra\Testbench\TestCase;
use DuskChromeDriver\Commands\DownloadChromeDriver;
use DuskChromeDriver\DuskChromeDriverServiceProvider;

class DownloadChromeDriverTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [DuskChromeDriverServiceProvider::class];
    }

    /** @test */
    public function command_can_be_called()
    {
        $this->artisan('dusk:download-chromedriver')
             ->assertExitCode(0);
    }
}
