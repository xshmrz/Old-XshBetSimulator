<?php

namespace Luchavez\StarterKit\Traits;

use Luchavez\StarterKit\Interfaces\ProviderHttpKernelInterface;

/**
 * Trait UsesProviderHttpKernelTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderHttpKernelTrait
{
    /**
     * @return void
     */
    public function bootHttpKernel(): void
    {
        if ($this instanceof ProviderHttpKernelInterface) {
            app()->booted(function () {
                if (method_exists($this, 'registerToHttpKernel')) {
                    $this->registerToHttpKernel(app('router'));
                }
            });
        }
    }
}
