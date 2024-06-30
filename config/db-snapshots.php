<?php
    return [
        'disk'                     => "snapshots",
        'default_connection'       => null,
        'temporary_directory_path' => storage_path('app/laravel-db-snapshots'),
        'compress'                 => true,
        'tables'                   => null,
        'exclude'                  => null,
    ];
