<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Throw an Exception on warnings from dompdf

    'public_path' => null,  // Override the public path if needed

    /*
     * Dejavu Sans font is missing glyphs for converted entities, turn it off if you need to show € and £.
     */
    'convert_entities' => true,

    'options' => [
        /*
         * Font directory — DomPDF stores font files and metrics here.
         * Must be writable by the web server.
         */
        'font_dir'   => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),

        'temp_dir' => sys_get_temp_dir(),

        'chroot' => realpath(base_path()),

        'allowed_protocols' => [
            ''        => ['rules' => []],   // local paths (no protocol)
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://'  => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        'artifactPathValidation' => null,
        'log_output_file'        => null,

        /*
         * Enable font subsetting to keep PDF file sizes manageable when
         * embedding large Unicode fonts (e.g. Bangla).
         */
        'enable_font_subsetting' => true,

        'pdf_backend'        => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',

        'default_paper_orientation' => 'portrait',

        /*
         * Default font — falls back to this when no matching font is found.
         * DejaVu Sans covers Latin + many scripts; Bangla text will use
         * notosansbengali via CSS font-family declarations in the template.
         */
        'default_font' => 'dejavu sans',

        'dpi' => 96,

        'enable_php'        => false,
        'enable_javascript' => true,

        /*
         * Enable remote file access so @font-face src URLs and any remote
         * assets can be loaded during PDF generation.
         */
        'enable_remote' => true,

        'allowed_remote_hosts' => null,

        'font_height_ratio' => 1.1,

        'enable_html5_parser' => true,
    ],

];
