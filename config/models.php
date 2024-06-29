<?php
    return [
        '*' => [
            'path'                    => app_path('Models/Core'),
            'namespace'               => 'App\Models\Core',
            'parent'                  => Illuminate\Database\Eloquent\Model::class,
            'use'                     => [
                // Reliese\Database\Eloquent\BitBooleans::class,
                // Reliese\Database\Eloquent\BlamableBehavior::class,

            ],
            'connection'              => false,
            'timestamps'              => true,
            'soft_deletes'            => true,
            'date_format'             => 'Y-m-d H:i:s',
            'per_page'                => 15,
            'base_files'              => false,
            'snake_attributes'        => false,
            'indent_with_space'       => 0,
            'qualified_tables'        => false,
            'hidden'                  => [
                '*secret*', '*password', '*password_re', '*token',
            ],
            'guarded'                 => [
            ],
            'casts'                   => [
                '*_json'   => 'json',
            ],
            'except'                  => [
                'failed_jobs',
                'password_resets',
                'personal_access_tokens',
                'password_reset_tokens',
            ],
            'only'                    => [
            ],
            'table_prefix'            => '',
            'lower_table_name_first'  => false,
            'model_names'             => [
            ],
            'relation_name_strategy'  => 'related',
            'with_property_constants' => false,
            'pluralize'               => false,
            'override_pluralize_for'  => [
            ],
            'hidden_in_base_files'    => false,
            'fillable_in_base_files'  => true,
            'enable_return_types'     => false,
        ],
    ];
