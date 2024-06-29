<?php

namespace Luchavez\StarterKit\Abstracts;

use Illuminate\Support\ServiceProvider;
use Luchavez\StarterKit\Traits\UsesProviderStarterKitTrait;

/**
 * Class BaseStarterKitServiceProvider
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
abstract class BaseStarterKitServiceProvider extends ServiceProvider
{
    use UsesProviderStarterKitTrait;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Boot Laravel Files
        packageDomain()->provider($this)
            ->registerLaravelFiles()
            ->bootLaravelFiles();

        // For Console Kernel
        $this->bootConsoleKernel();

        // For Http Kernel
        $this->bootHttpKernel();

        // For Dynamic Relationships
        $this->bootDynamicRelationships();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }
}
