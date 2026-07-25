<?php

namespace App\Console\Commands;

use FontLib\Font;
use Illuminate\Console\Command;

class LoadBanglaFont extends Command
{
    protected $signature   = 'dompdf:load-bangla-font';
    protected $description = 'Register Noto Sans Bengali font with DomPDF font cache';

    /** Font definitions: family (lowercase), variant key, source TTF, dest prefix */
    private array $fonts = [
        ['family' => 'notosansbengali', 'variant' => 'normal', 'weight' => 'normal', 'src' => 'NotoSerifBengali-Regular.ttf', 'prefix' => 'notosansbengali_normal'],
        ['family' => 'notosansbengali', 'variant' => 'bold',   'weight' => 'bold',   'src' => 'NotoSerifBengali-Bold.ttf',    'prefix' => 'notosansbengali_bold'],
    ];

    public function handle(): int
    {
        $fontDir            = storage_path('fonts');
        $installedFontsPath = $fontDir . DIRECTORY_SEPARATOR . 'installed-fonts.json';

        // Load existing registry
        $installedFonts = [];
        if (file_exists($installedFontsPath)) {
            $installedFonts = json_decode(file_get_contents($installedFontsPath), true) ?? [];
        }

        foreach ($this->fonts as $def) {
            $src    = $fontDir . DIRECTORY_SEPARATOR . $def['src'];
            $prefix = $def['prefix'];
            $dest   = $fontDir . DIRECTORY_SEPARATOR . $prefix;

            if (! file_exists($src)) {
                $this->error("Source font not found: {$src}");
                return self::FAILURE;
            }

            // 1. Copy TTF with prefixed name
            if (! copy($src, $dest . '.ttf')) {
                $this->error("Could not copy: {$src}");
                return self::FAILURE;
            }

            // 2. Parse TTF and generate UFM metrics
            $font = Font::load($dest . '.ttf');
            if (! $font) {
                $this->error("Font::load failed for: {$dest}.ttf");
                return self::FAILURE;
            }

            $font->parse();
            $font->saveAdobeFontMetrics($dest . '.ufm.json');
            $font->close();

            if (! file_exists($dest . '.ufm.json')) {
                $this->error("UFM generation failed for: {$dest}");
                return self::FAILURE;
            }

            // 3. Register in installed-fonts map (absolute path without extension)
            $installedFonts[$def['family']][$def['variant']] = $dest;

            $this->info("Registered [{$def['family']}][{$def['variant']}] -> {$prefix}");
        }

        // 4. Persist the registry
        file_put_contents(
            $installedFontsPath,
            json_encode($installedFonts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('installed-fonts.json saved to: ' . $fontDir);
        return self::SUCCESS;
    }
}
