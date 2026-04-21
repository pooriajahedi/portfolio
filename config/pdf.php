<?php

return [
    'mode'                     => 'utf-8',
    'format'                   => 'A4',
    'default_font_size'        => 12,
    'default_font'             => 'pinar',
    'margin_left'              => 10,
    'margin_right'             => 10,
    'margin_top'               => 10,
    'margin_bottom'            => 10,
    'margin_header'            => 0,
    'margin_footer'            => 0,
    'orientation'              => 'P',
    'title'                    => 'CV',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => '',
    'watermark_image_alpha'    => 0.2,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'P',

    // Important: keep trailing slash and use lowercase font key.
    'custom_font_dir'          => public_path('fonts/pinar') . '/',
    'custom_font_data'         => [
        'pinar' => [
            'R' => 'Pinar-DS1-FD-Medium.ttf',
            'B' => 'Pinar-DS1-FD-Bold.ttf',
        ],
    ],

    'auto_language_detection'  => true,
    'temp_dir'                 => storage_path('app/mpdf-temp'),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'use_active_forms'         => false,
];
