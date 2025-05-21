<?php

namespace DuskChromeDriver\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use ZipArchive;

class DownloadChromeDriver extends Command
{
    protected $signature = 'dusk:download-chromedriver {--all : Download ChromeDriver for all OS platforms}';
    protected $description = 'Download ChromeDriver matching the installed Chrome version and rename appropriately';

    public function handle()
    {
        $binDir = base_path('.vendor/laravel/dusk/bin');
        if (!is_dir($binDir)) {
            mkdir($binDir, 0755, true);
        }

        $version = $this->getChromeVersion();
        if (!$version) {
            $this->error('Unable to detect local Chrome version.');
            return 1;
        }

        $this->info("Detected Chrome version: {$version}");

        $metaUrl = 'https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions-with-downloads.json';
        $meta = json_decode(@file_get_contents($metaUrl), true);

        if (!isset($meta['channels']['Stable']['version'])) {
            $this->error('Could not fetch ChromeDriver metadata.');
            return 1;
        }

        $driverVersion = $meta['channels']['Stable']['version'];
        $this->info("Using ChromeDriver version: {$driverVersion}");

        $platforms = $this->option('all')
            ? ['win64', 'linux64', 'mac-x64', 'mac-arm64']
            : [$this->getCurrentPlatformKey()];

        foreach ($platforms as $platformKey) {
            $zipName = "chromedriver-{$platformKey}.zip";
            $downloadUrl = "https://edgedl.me.gvt1.com/edgedl/chrome/chrome-for-testing/{$driverVersion}/{$platformKey}/{$zipName}";

            $this->info("Downloading ChromeDriver for {$platformKey}...");

            $zipPath = storage_path($zipName);
            $file = @fopen($downloadUrl, 'r');
            if (!$file) {
                $this->error("Failed to download from: {$downloadUrl}");
                continue;
            }
            file_put_contents($zipPath, $file);

            $extractDir = storage_path("chromedriver_temp_{$platformKey}");
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($extractDir);
                $zip->close();
                unlink($zipPath);
            } else {
                $this->error("Failed to unzip {$zipName}");
                continue;
            }

            $sourcePath = $extractDir . "/chromedriver-{$platformKey}/chromedriver";
            if ($platformKey === 'win64') {
                $sourcePath .= '.exe';
            }

            $targetName = $this->renameTarget($platformKey);
            $targetPath = $binDir . '/' . $targetName;
            if ($platformKey === 'win64') {
                $targetPath .= '.exe';
            }

            if (!file_exists($sourcePath)) {
                $this->error("Could not find extracted driver at {$sourcePath}");
                continue;
            }

            rename($sourcePath, $targetPath);
            chmod($targetPath, 0755);
            $this->info("Saved as: {$targetName}");
            $this->deleteDirectory($extractDir);
        }

        $this->info('All requested ChromeDriver downloads complete.');
        return 0;
    }

    protected function getChromeVersion()
    {
        if ($this->isWSL()) {
            $cmd = 'cmd.exe /c reg query "HKEY_CURRENT_USER\Software\Google\Chrome\BLBeacon" /v version';
            $process = Process::fromShellCommandline($cmd);
            $process->run();

            if ($process->isSuccessful()) {
                if (preg_match('/\d+\.\d+\.\d+\.\d+/', $process->getOutput(), $matches)) {
                    return $matches[0];
                }
            }
        }

        $os = PHP_OS_FAMILY;
        $commands = [
            'Windows' => ['reg query "HKEY_CURRENT_USER\\Software\\Google\\Chrome\\BLBeacon" /v version'],
            'Darwin' => ['/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version'],
            'Linux' => ['google-chrome --version', 'chromium-browser --version', 'chromium --version'],
        ];

        if (!isset($commands[$os])) {
            return null;
        }

        foreach ($commands[$os] as $cmd) {
            $process = Process::fromShellCommandline($cmd);
            $process->run();
            if ($process->isSuccessful()) {
                if (preg_match('/\d+\.\d+\.\d+\.\d+/', $process->getOutput(), $matches)) {
                    return $matches[0];
                }
            }
        }

        return null;
    }

    protected function getCurrentPlatformKey()
    {
        $os = strtolower(PHP_OS);
        if (strpos($os, 'win') !== false) {
            return 'win64';
        } elseif (strpos($os, 'darwin') !== false) {
            $arch = trim(@shell_exec('uname -m'));
            return $arch === 'arm64' ? 'mac-arm64' : 'mac-x64';
        } elseif (strpos($os, 'linux') !== false) {
            return 'linux64';
        }

        return null;
    }

    protected function renameTarget($platformKey)
    {
        $map = [
            'win64' => 'chromedriver-win',
            'linux64' => 'chromedriver-linux',
            'mac-x64' => 'chromedriver-mac',
            'mac-arm64' => 'chromedriver-mac-arm',
        ];

        return isset($map[$platformKey]) ? $map[$platformKey] : 'chromedriver-unknown';
    }

    protected function isWSL()
    {
        return stripos(PHP_OS, 'Linux') !== false
            && is_file('/proc/version')
            && stripos(file_get_contents('/proc/version'), 'Microsoft') !== false;
    }

    protected function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
