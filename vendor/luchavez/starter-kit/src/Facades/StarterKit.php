<?php

namespace Luchavez\StarterKit\Facades;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Luchavez\StarterKit\Data\ServiceProviderData;

/**
 * @method static string getMainTag()
 * @method static Collection getProviders()
 * @method static Collection getProvidersFromList(string|null $package = null, string|null $domain = null)
 * @method static ServiceProviderData addToProviders(ServiceProvider $provider)
 * @method static Collection getPaths(string|null $package = null, string|null $domain = null)
 * @method static bool addToPaths(ServiceProviderData $data)
 * @method static Collection getTargetDirectories()
 * @method static Collection|null getFromPaths(string|null $package = null, string|null $domain = null, string|null $dot_notation = null)
 * @method static Collection|null getPathsOnly(string|null $package = null, string|null $domain = null, array $only = [])
 * @method static Collection|null getDomains(string|null $package = null)
 * @method static Collection getRoot()
 * @method static Collection|null getPackages()
 * @method static Collection|null getConfigs(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getMigrationsPath(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getHelpers(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getRoutes(string|null $package = null, string|null $domain = null)
 * @method static string|null getTranslations(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getModels(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getPossibleModels(string|null $package = null, string|null $domain = null)
 * @method static Collection|null getModelRelatedFiles(string $directory, string|null $package = null, string|null $domain = null, array $map = [])
 * @method static Collection|null getPolicies(string|null $package = null, string|null $domain = null, array $policy_map = [])
 * @method static Collection|null getObservers(string|null $package = null, string|null $domain = null, array $observer_map = [])
 * @method static Collection|null getRepositories(string|null $package = null, string|null $domain = null, array $repository_map = [])
 * @method static Collection|Closure|callable|null getExceptionRenders(string|object|null $exception_class = null)
 * @method static bool addExceptionRender(string $exception_class, Closure|callable $closure, bool $override = false)
 * @method static string|null getUserModel()
 * @method static Builder|null getUserQueryBuilder()
 * @method static Collection getMorphMap()
 * @method static string|null getMorphMapKey(string $model_name)
 * @method static array getRouteMiddleware(bool $is_api, string $separator = ';')
 * @method static bool shouldOverrideExceptionHandler()
 * @method static bool shouldEnforceMorphMap()
 * @method static string getChangeLocaleKey()
 * @method static bool isChangeLocaleEnabled()
 *
 * @see \Luchavez\StarterKit\Services\StarterKit
 */
class StarterKit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'starter-kit';
    }
}
