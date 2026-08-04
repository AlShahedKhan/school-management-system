<?php

namespace App\Support;

use RuntimeException;
use Spatie\Browsershot\Browsershot;

class TranscriptPdfRenderer
{
    public function render(string $url): string
    {
        $browsershot = Browsershot::url($url)
            ->setNodeModulePath(base_path('node_modules'))
            ->windowSize(1200, 1600)
            ->waitUntilNetworkIdle(false)
            ->waitForFunction(
                "document.querySelector('#resultContainer[data-pdf-ready=\"true\"] .transcript-page') !== null",
                timeout: 60_000
            )
            ->showBackground()
            ->format('Legal')
            ->scale(0.72)
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
            throw new RuntimeException('Unable to render the transcript PDF with Chrome.', 0, $exception);
        }
    }

    private function chromePath(): ?string
    {
        $configuredPath = config('services.browsershot.chrome_path');

        if (is_string($configuredPath) && $configuredPath !== '') {
            return $configuredPath;
        }

        $candidates = PHP_OS_FAMILY === 'Windows'
            ? [
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            ]
            : [
                '/usr/bin/google-chrome',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
            ];

        // Puppeteer-managed Chrome is common on servers where the distro package
        // is unavailable (for example, Ubuntu's chromium snap package). Discover
        // the installed executable without hard-coding Puppeteer's version.
        if (PHP_OS_FAMILY !== 'Windows') {
            $candidates = array_merge(
                glob('/home/*/.cache/puppeteer/chrome/*/chrome-linux*/chrome') ?: [],
                ['/opt/puppeteer-chrome/chrome'],
                $candidates
            );
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
