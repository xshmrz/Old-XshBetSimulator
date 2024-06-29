<?php
    return [
        'group_by_default'             => TRUE,
        'auto_id'                      => FALSE,
        'verbose_summaries_by_default' => FALSE,
        'validate_by_default'          => FALSE,
        'inline_validation'            => FALSE,
        'validation_script_path'       => env('APP_URL') . '/vendor/aire/js/aire.js',
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
