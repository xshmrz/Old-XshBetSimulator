<?php
    return [
        'disk'                     => "snapshots",
        'default_connection'       => NULL,
        'temporary_directory_path' => storage_path('app/laravel-db-snapshots'),
        'compress'                 => TRUE,
        'tables'                   => NULL,
        'exclude'                  => NULL,
    ];
