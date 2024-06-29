<?php

namespace Luchavez\StarterKit\Traits;

use Luchavez\StarterKit\Interfaces\ProviderDynamicRelationshipsInterface;

/**
 * Trait UsesProviderDynamicRelationshipsTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderDynamicRelationshipsTrait
{
    /**
     * @return void
     *
     * @link   https://laravel.com/docs/8.x/eloquent-relationships#dynamic-relationships
     */
    public function bootDynamicRelationships(): void
    {
        if ($this instanceof ProviderDynamicRelationshipsInterface && $this->isDynamicRelationshipsEnabled()) {
            $this->registerDynamicRelationships();
        }
    }

    /**
     * @return bool
     */
    public function isDynamicRelationshipsEnabled(): bool
    {
        return true;
    }
}
