<?php

namespace Luchavez\StarterKit\Traits;

/**
 * Trait UsesProviderRepositoryMapTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderRepositoryMapTrait
{
    /**
     * Laravel Repository Map
     *
     * @example [ UserRepository::class => User::class ]
     *
     * @var array
     */
    protected array $repository_map = [];

    /**
     * @return bool
     */
    public function areRepositoriesEnabled(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getRepositoryMap(): array
    {
        return $this->repository_map;
    }

    /**
     * @param  array  $repository_map
     */
    public function setRepositoryMap(array $repository_map): void
    {
        $this->repository_map = $repository_map;
    }

    /**
     * @param  array  $repository_map
     * @return $this
     */
    public function repositoryMap(array $repository_map): static
    {
        $this->setRepositoryMap($repository_map);

        return $this;
    }
}
