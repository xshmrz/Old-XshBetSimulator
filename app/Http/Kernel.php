<?php
    namespace App\Http;
    use Illuminate\Foundation\Http\Kernel as HttpKernel;
    class Kernel extends HttpKernel {
        protected $middleware        = [
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\TrustHosts::class,
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ];
        protected $middlewareGroups  = [
        ];
        protected $middlewareAliases = [
            'auth'             => \App\Http\Middleware\Authenticate::class,
            'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'              => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive'     => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed'           => \App\Http\Middleware\ValidateSignature::class,
            'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ];
        protected $routeMiddleware
                                     = [
                'MiddlewareSite'        => 'App\Http\Middleware\MiddlewareSite',
                'MiddlewarePanel'       => 'App\Http\Middleware\MiddlewarePanel',
                'MiddlewareDashboard'   => 'App\Http\Middleware\MiddlewareDashboard',
                'MiddlewareApi'         => 'App\Http\Middleware\MiddlewareApi',
                # ->
                'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
                'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
                'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
                'localeCookieRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
                'localeViewPath'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            ];
    }
