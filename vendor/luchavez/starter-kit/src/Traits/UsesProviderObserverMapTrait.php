<?php

namespace Luchavez\StarterKit\Traits;

/**
 * Trait UsesProviderObserverMapTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderObserverMapTrait
{
    /**
     * Laravel Observer Map
     *
     * @link    https://laravel.com/docs/8.x/eloquent#observers
     *
     * @example [ UserObserver::class => User::class ]
     *
     * @var array
     */
    protected array $observer_map = [];

    /**
     * @return bool
     */
    public function areObserversEnabled(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getObserverMap(): array
    {
        return $this->observer_map;
    }

    /**
     * @param  array  $observer_map
     */
    public function setObserverMap(array $observer_map): void
    {
        $this->observer_map = $observer_map;
    }

    /**
     * @param  array  $observer_map
     * @return $this
     */
    public function observerMap(array $observer_map): static
    {
        $this->setObserverMap($observer_map);

        return $this;
    }
}
