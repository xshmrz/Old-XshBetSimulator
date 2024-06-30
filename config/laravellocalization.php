<?php
    return [
        'supportedLocales'        => [
            'en' => ['name' => 'English', 'script' => 'Latn', 'native' => '', 'regional' => ''],
            'tr' => ['name' => 'Turkish', 'script' => 'Latn', 'native' => '', 'regional' => ''],
        ],
        'useAcceptLanguageHeader' => true,
        'hideDefaultLocaleInURL'  => true,
        'localesOrder'            => [],
        'localesMapping'          => [],
        'utf8suffix'              => env('LARAVELLOCALIZATION_UTF8SUFFIX', '.UTF-8'),
        'urlsIgnored'             => ['/skipped'],
        'httpMethodsIgnored'      => ['POST', 'PUT', 'PATCH', 'DELETE'],
    ];
