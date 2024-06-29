<?php
    return [
        'filename'                              => '_ide_helper.php',
        'models_filename'                       => '_ide_helper_models.php',
        'meta_filename'                         => '.phpstorm.meta.php',
        'include_fluent'                        => TRUE,
        'include_factory_builders'              => TRUE,
        'write_model_magic_where'               => TRUE,
        'write_model_external_builder_methods'  => TRUE,
        'write_model_relation_count_properties' => TRUE,
        'write_eloquent_model_mixins'           => TRUE,
        'include_helpers'                       => TRUE,
        'helper_files'                          => [
            base_path() . '/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
        ],
        'model_locations'                       => [
            'app/Models/Core',
        ],
        'ignored_models'                        => [
        ],
        'model_hooks'                           => [
        ],
        'extra'                                 => [
            'Eloquent' => ['Illuminate\Database\Eloquent\Builder', 'Illuminate\Database\Query\Builder'],
            'Session'  => ['Illuminate\Session\Store'],
        ],
        'magic'                                 => [

        ],
        'interfaces'                            => [
        ],
        'model_camel_case_properties'           => FALSE,
        'type_overrides'                        => [
            'integer' => 'int',
            'boolean' => 'bool',
        ],
        'include_class_docblocks'               => FALSE,
        'force_fqn'                             => FALSE,
        'use_generics_annotations'              => TRUE,
        'additional_relation_types'             => [],
        'additional_relation_return_types'      => [],
        'post_migrate'                          => [
        ],
    ];
