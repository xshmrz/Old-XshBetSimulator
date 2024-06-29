<?php

namespace Luchavez\StarterKit\Interfaces;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Interface ConsoleKernelInterface
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
interface ProviderConsoleKernelInterface
{
    /**
     * @param  Schedule  $schedule
     * @return void
     */
    public function registerToConsoleKernel(Schedule $schedule): void;
}
