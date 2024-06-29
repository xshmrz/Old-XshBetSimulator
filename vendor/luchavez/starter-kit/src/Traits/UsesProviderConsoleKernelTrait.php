<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Console\Scheduling\Schedule;
use Luchavez\StarterKit\Interfaces\ProviderConsoleKernelInterface;

/**
 * Trait UsesProviderConsoleKernelTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderConsoleKernelTrait
{
    /**
     * @return void
     */
    public function bootConsoleKernel(): void
    {
        if ($this instanceof ProviderConsoleKernelInterface) {
            app()->booted(function () {
                if (method_exists($this, 'registerToConsoleKernel')) {
                    $this->registerToConsoleKernel(app(Schedule::class));
                }
            });
        }
    }
}
