<?php

namespace App\Support;

use RuntimeException;
use Spatie\Browsershot\Browsershot;

class AdmitCardPdfRenderer
{
    public function render(string $url): string
    {
        $browsershot = Browsershot::url($url)
            ->setNodeModulePath(base_path('node_modules'))
            ->windowSize(1200, 1600)
            ->waitUntilNetworkIdle(false)
            ->waitForFunction('document.body.dataset.admitCardReady === "true"', timeout: 60_000)
            ->showBackground()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->timeout(120);

        if ($nodeBinary = config('services.browsershot.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($chromePath = $this->chromePath()) {
            $browsershot->setChromePath($chromePath);
        }

        if (config('services.browsershot.no_sandbox', false)) {
            $browsershot->noSandbox();
        }

        try {
            return $browsershot->pdf();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to render the admit-card PDF with Chrome.', 0, $exception);
        }
    }

    private function chromePath(): ?string
    {
        $configuredPath = config('services.browsershot.chrome_path');

        if (is_string($configuredPath) && $configuredPath !== '') {
            return $configuredPath;
        }

        $candidates = PHP_OS_FAMILY === 'Windows'
            ? ['C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', 'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe']
            : ['/usr/bin/google-chrome', '/usr/bin/google-chrome-stable', '/usr/bin/chromium', '/usr/bin/chromium-browser'];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
