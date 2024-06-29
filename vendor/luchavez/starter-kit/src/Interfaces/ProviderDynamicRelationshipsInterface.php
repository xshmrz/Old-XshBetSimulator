<?php

namespace Luchavez\StarterKit\Interfaces;

/**
 * Interface ProviderDynamicRelationshipsInterface
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
interface ProviderDynamicRelationshipsInterface
{
    /**
     * @return void
     *
     * @link   https://laravel.com/docs/8.x/eloquent-relationships#dynamic-relationships
     */
    public function registerDynamicRelationships(): void;
}
