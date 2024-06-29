<?php

namespace Luchavez\StarterKit\Interfaces;

use Illuminate\Routing\Router;

/**
 * Interface HttpKernelInterface
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
interface ProviderHttpKernelInterface
{
    /**
     * @param  Router  $router
     * @return void
     */
    public function registerToHttpKernel(Router $router): void;
}
