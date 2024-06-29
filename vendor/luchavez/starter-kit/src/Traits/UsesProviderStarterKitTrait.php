<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Support\Collection;
use Luchavez\StarterKit\Services\StarterKit;

/**
 * Trait UsesProviderStarterKitTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderStarterKitTrait
{
    use UsesProviderConsoleKernelTrait;
    use UsesProviderDynamicRelationshipsTrait;
    use UsesProviderEnvVarsTrait;
    use UsesProviderHttpKernelTrait;
    use UsesProviderMorphMapTrait;
    use UsesProviderObserverMapTrait;
    use UsesProviderPolicyMapTrait;
    use UsesProviderRepositoryMapTrait;
    use UsesProviderRoutesTrait;

    /**
     * Artisan Commands
     *
     * @var array
     */
    protected array $commands = [];

    /**
     * @return Collection
     */
    public function getExcludedTargetDirectories(): Collection
    {
        return collect()
            ->when(! $this->areConfigsEnabled(), fn ($collection) => $collection->push(StarterKit::CONFIG_DIR))
            ->when(! $this->areMigrationsEnabled(), fn ($collection) => $collection->push(StarterKit::MIGRATIONS_DIR))
            ->when(! $this->areHelpersEnabled(), fn ($collection) => $collection->push(StarterKit::HELPERS_DIR))
            ->when(! $this->areTranslationsEnabled(), fn ($collection) => $collection->push(StarterKit::LANG_DIR))
            ->when(! $this->areRoutesEnabled(), fn ($collection) => $collection->push(StarterKit::ROUTES_DIR))
            ->when(! $this->areRepositoriesEnabled(), fn ($collection) => $collection->push(StarterKit::REPOSITORIES_DIR))
            ->when(! $this->arePoliciesEnabled(), fn ($collection) => $collection->push(StarterKit::POLICIES_DIR))
            ->when(! $this->areObserversEnabled(), fn ($collection) => $collection->push(StarterKit::OBSERVERS_DIR));
    }

    /***** OVERRIDABLE METHODS *****/

    /**
     * Console-specific booting.
     *
     * @return void
     */
    abstract protected function bootForConsole(): void;

    /***** HELPER FILES RELATED *****/

    /**
     * @return bool
     */
    public function areHelpersEnabled(): bool
    {
        return true;
    }

    /***** TRANSLATIONS RELATED *****/

    /**
     * @return bool
     */
    public function areTranslationsEnabled(): bool
    {
        return true;
    }

    /***** CONFIGS RELATED *****/

    /**
     * @return bool
     */
    public function areConfigsEnabled(): bool
    {
        return true;
    }

    /***** MIGRATIONS RELATED *****/

    /**
     * @return bool
     */
    public function areMigrationsEnabled(): bool
    {
        return true;
    }
}
