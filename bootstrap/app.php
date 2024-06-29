<?php
    $app = new Illuminate\Foundation\Application(
        $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
    );
    # ->
    function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }
    if ($app->runningInConsole() || isLocalhost()) {
        $app->loadEnvironmentFrom('.env.local');
    }
    else {
        $app->loadEnvironmentFrom('.env.production');
    }
    # ->
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        App\Http\Kernel::class
    );
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
    return $app;
