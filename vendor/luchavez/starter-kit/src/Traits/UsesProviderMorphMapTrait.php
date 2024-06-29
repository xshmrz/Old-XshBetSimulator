<?php

namespace Luchavez\StarterKit\Traits;

/**
 * Trait UsesProviderMorphMapTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderMorphMapTrait
{
    /**
     * Polymorphism Morph Map
     *
     * @link    https://laravel.com/docs/8.x/eloquent-relationships#custom-polymorphic-types
     *
     * @example [ 'user' => User::class ]
     *
     * @var array
     */
    protected array $morph_map = [];

    /**
     * @return array
     */
    public function getMorphMap(): array
    {
        return $this->morph_map;
    }

    /**
     * @param  array  $morph_map
     */
    public function setMorphMap(array $morph_map): void
    {
        $this->morph_map = $morph_map;
    }

    /**
     * @param  array  $morph_map
     * @return UsesProviderMorphMapTrait
     */
    public function morphMap(array $morph_map): static
    {
        $this->setMorphMap($morph_map);

        return $this;
    }
}
