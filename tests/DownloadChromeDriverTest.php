<?php

namespace DuskChromeDriver\Tests;

use Orchestra\Testbench\TestCase;
use DuskChromeDriver\Commands\DownloadChromeDriver;
use DuskChromeDriver\DuskChromeDriverServiceProvider;
use Illuminate\Support\Facades\File;

class DownloadChromeDriverTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [DuskChromeDriverServiceProvider::class];
    }

    /** @test */
    public function command_can_be_called()
    {
        $this->artisan('dusk:download-chromedriver --all')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_chromedriver_download_command_creates_driver_file()
    {
        $outputDir = base_path('vendor/laravel/dusk/bin');

        // Clean up before test
        File::deleteDirectory($outputDir);

        // Run the command
        $this->artisan('dusk:download-chromedriver --all')
            ->assertExitCode(0);

        // Assert the directory exists
        $this->assertDirectoryExists($outputDir, 'The output directory was not created.');

        // Assert at least one OS-specific driver file was created
        $this->assertTrue(
            collect(['chromedriver-win', 'chromedriver-linux', 'chromedriver-mac'])->contains(function ($file) use ($outputDir) {
                return file_exists($outputDir . DIRECTORY_SEPARATOR . $file);
            }),
            'No ChromeDriver file was found in the output directory.'
        );
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('vendor/laravel/dusk/bin'));
        parent::tearDown();
    }
}
