<?php
    return [
        'group_by_default'             => true,
        'auto_id'                      => false,
        'verbose_summaries_by_default' => false,
        'validate_by_default'          => false,
        'inline_validation'            => false,
        'validation_script_path'       => env('APP_URL').'/vendor/aire/js/aire.js',
        'default_attributes'           => [
            'form' => [
                'method' => 'POST',
            ],
        ],
        'default_classes'              => [
            'label'    => "form-label ps-2 mb-1 fs-sm",
            'input'    => "form-control",
            'select'   => "form-select",
            'textarea' => "form-control",
        ],
        'variant_classes'              => [],
        'validation_classes'           => [
            'none'    => [],
            'valid'   => [],
            'invalid' => [],
        ],
    ];
