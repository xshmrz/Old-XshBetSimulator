<?php

namespace Luchavez\StarterKit\Services;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Translation\Translator;
use Luchavez\StarterKit\Abstracts\BaseStarterKitServiceProvider;
use Luchavez\StarterKit\Data\ServiceProviderData;
use Luchavez\StarterKit\Traits\UsesProviderMorphMapTrait;
use Luchavez\StarterKit\Traits\UsesProviderObserverMapTrait;
use Luchavez\StarterKit\Traits\UsesProviderPolicyMapTrait;
use Luchavez\StarterKit\Traits\UsesProviderRepositoryMapTrait;
use Luchavez\StarterKit\Traits\UsesProviderRoutesTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

/**
 * Class PackageDomain
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class PackageDomain
{
    use UsesProviderMorphMapTrait;
    use UsesProviderObserverMapTrait;
    use UsesProviderPolicyMapTrait;
    use UsesProviderRepositoryMapTrait;
    use UsesProviderRoutesTrait;

    /**
     * @var ServiceProvider|null
     */
    protected ?ServiceProvider $provider = null;

    /**
     * @var Collection|array
     */
    protected Collection|array $excluded_directories = [];

    /**
     * @var ServiceProviderData|null
     */
    protected ?ServiceProviderData $provider_data = null;

    /**
     * @var Collection|null
     */
    protected ?Collection $existing_paths = null;

    /**
     * @param  Application  $app
     * @param  StarterKit  $starter_kit
     * @param  Repository  $config
     * @param  Migrator  $migrator
     * @param  Translator  $translator
     */
    public function __construct(
        protected Application $app,
        protected StarterKit $starter_kit,
        protected Repository $config,
        protected Migrator $migrator,
        protected Translator $translator,
    ) {
        //
    }

    // Setters

    /**
     * @param  ServiceProvider  $provider
     */
    public function setProvider(ServiceProvider $provider): void
    {
        $this->provider = $provider;

        if ($this->provider instanceof BaseStarterKitServiceProvider) {
            $this
                // General
                ->excludeDirectories($this->provider->getExcludedTargetDirectories())

                // Morph Map Related
                ->morphMap($this->provider->getMorphMap())

                // Routes Related
                ->routePrefix($this->provider->getRoutePrefix())
                ->prefixRouteWithFileName($this->provider->shouldPrefixRouteWithFileName())
                ->prefixRouteWithDirectory($this->provider->shouldPrefixRouteWithDirectory())
                ->webMiddleware($this->provider->getWebMiddleware())
                ->apiMiddleware($this->provider->getApiMiddleware())
                ->defaultWebMiddleware($this->provider->getDefaultWebMiddleware())
                ->defaultApiMiddleware($this->provider->getDefaultApiMiddleware())

                // Observers Related
                ->observerMap($this->provider->getObserverMap())

                // Policies Related
                ->policyMap($this->provider->getPolicyMap())

                // Repositories Related
                ->repositoryMap($this->provider->getRepositoryMap());
        }
    }

    /**
     * @param  ServiceProvider  $provider
     * @return $this
     */
    public function provider(ServiceProvider $provider): static
    {
        $this->setProvider($provider);

        return $this;
    }

    /**
     * @param  array|Collection  $excluded_directories
     */
    public function setExcludedDirectories(array|Collection $excluded_directories): void
    {
        $this->excluded_directories = $excluded_directories;
    }

    /**
     * @param  array|Collection  $excluded_directories
     * @return $this
     */
    public function excludeDirectories(array|Collection $excluded_directories): static
    {
        $this->setExcludedDirectories($excluded_directories);

        return $this;
    }

    /**
     * @return static
     */
    public function bootLaravelFiles(): static
    {
        if ($this->provider) {
            $this
                ->bootMorphMap()
                ->bootMigrations()
                ->bootRoutes()
                ->bootObservers()
                ->bootPolicies()
                ->bootRepositories();
        }

        return $this;
    }

    /**
     * @return static
     */
    public function registerLaravelFiles(): static
    {
        if ($this->provider) {
            // set the StarterKit instance to be used by load methods
            $this->provider_data = $this->starter_kit->addToProviders($this->provider);

            // set existing paths to be used by
            $package = $this->provider_data->package;
            $domain = $this->provider_data->domain;
            $only = $this->starter_kit->getTargetDirectories()->diff($this->excluded_directories)->toArray();
            $this->existing_paths = $this->starter_kit->getPathsOnly($package, $domain, $only);

            $this
                ->registerTranslations()
                ->registerConfigs()
                ->registerHelpers();
        }

        return $this;
    }

    /***** HELPERS *****/

    /**
     * Load Helpers
     *
     * @return $this
     */
    protected function registerHelpers(): static
    {
        if ($this->existing_paths?->has(StarterKit::HELPERS_DIR)) {
            $this->starter_kit->getHelpers($this->provider_data->package, $this->provider_data->domain)
                ?->filter(fn ($item) => file_exists($item['path']))
                ->each(fn ($item) => require $item['path']);
        }

        return $this;
    }

    /***** CONFIGS *****/

    /**
     * Load Configs
     *
     * @return $this
     */
    protected function registerConfigs(): static
    {
        if (
            $this->existing_paths?->has(StarterKit::CONFIG_DIR) &&
            ! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())
        ) {
            $this->starter_kit->getConfigs($this->provider_data->package, $this->provider_data->domain)
                ?->filter(fn ($item) => file_exists($item['path']))
                ->each(fn ($item) => $this->config->set($item['name'], array_merge(require $item['path'], $this->config->get($item['name'], []))));
        }

        return $this;
    }

    /***** MIGRATIONS *****/

    /**
     * Register database migration paths.
     *
     * @return $this
     */
    protected function bootMigrations(): static
    {
        if ($this->existing_paths?->has(StarterKit::MIGRATIONS_DIR)) {
            $this->starter_kit->getMigrationsPath($this->provider_data->package, $this->provider_data->domain)
                ?->filter(fn ($path) => file_exists($path))
                ->each(fn ($path) => $this->migrator->path($path));
        }

        return $this;
    }

    /***** TRANSLATIONS *****/

    /**
     * Register a translation file namespace.
     *
     * @return $this
     */
    protected function registerTranslations(): static
    {
        if (
            $this->existing_paths?->has(StarterKit::LANG_DIR) &&
            $paths = $this->starter_kit->getTranslations($this->provider_data->package, $this->provider_data->domain)
        ) {
            $namespace = trim($this->provider_data->package.$this->provider_data->getDecodedDomain(), '/');

            // Register Short Keys
            $this->translator->addNamespace($namespace, $paths);

            // Register Strings as Keys
            $this->translator->addJsonPath($paths);
        }

        return $this;
    }

    /***** OBSERVERS *****/

    /**
     * Load Observers
     *
     * @return $this
     */
    protected function bootObservers(): static
    {
        if ($this->existing_paths?->has(StarterKit::OBSERVERS_DIR)) {
            $this->starter_kit->getObservers($this->provider_data->package, $this->provider_data->domain, $this->getObserverMap())
                ?->each(function ($model, $observer) {
                    if ($model instanceof Collection) {
                        $model = $model->first();
                    }
                    try {
                        call_user_func($model.'::observe', $observer);
                    } catch (Throwable) {
                        //
                    }
                });
        }

        return $this;
    }

    /***** POLICIES *****/

    /**
     * Load Policies
     *
     * @return $this
     */
    protected function bootPolicies(): static
    {
        if ($this->existing_paths?->has(StarterKit::POLICIES_DIR)) {
            $this->starter_kit->getPolicies($this->provider_data->package, $this->provider_data->domain, $this->getPolicyMap())
                ?->each(function ($model, $policy) {
                    if ($model instanceof Collection) {
                        $model = $model->first();
                    }
                    try {
                        Gate::policy($model, $policy);
                    } catch (Throwable) {
                        //
                    }
                });
        }

        return $this;
    }

    /***** REPOSITORIES *****/

    /**
     * Load Repositories
     *
     * @return $this
     */
    protected function bootRepositories(): static
    {
        if ($this->existing_paths?->has(StarterKit::REPOSITORIES_DIR)) {
            $this->starter_kit->getRepositories($this->provider_data->package, $this->provider_data->domain, $this->getRepositoryMap())
                ?->each(function ($model, $repository) {
                    if ($model instanceof Collection) {
                        $model = $model->first();
                    }
                    try {
                        $this->app
                            ->when($repository)
                            ->needs(QueryBuilder::class)
                            ->give(fn () => QueryBuilder::for($model));
                    } catch (Throwable) {
                        //
                    }
                });
        }

        return $this;
    }

    /***** ROUTES *****/

    protected function bootRoutes(): static
    {
        if (
            $this->existing_paths?->has(StarterKit::ROUTES_DIR) &&
            ! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())
        ) {
            $routes = $this->starter_kit->getRoutes($this->provider_data->package, $this->provider_data->domain)
                ->filter(fn ($item) => file_exists($item['path']))
                ->when($this->shouldPrefixRouteWithDirectory(), function (Collection $collection) {
                    return $collection->map(function ($item) {
                        $append_to_prefix = Str::of($item['path'])
                            ->after('routes/')
                            ->before($item['file'])
                            ->trim('/')
                            ->jsonSerialize();
                        if ($append_to_prefix) {
                            $item[$this->append_key][] = $append_to_prefix;
                        }

                        return $item;
                    });
                })
                ->when($this->shouldPrefixRouteWithFileName(), function (Collection $collection) {
                    return $collection->map(function ($item) {
                        $append_to_prefix = $item['name'];
                        if (! in_array($append_to_prefix, ['api', 'web', 'console', 'channels'])) {
                            $item[$this->append_key][] = Str::of($append_to_prefix)
                                ->replace([' ', '.', '_'], '-') // replace space, dot, and underscore with dash
                                ->whenEndsWith('api', function (Stringable $str) {
                                    return $str->beforeLast('api');
                                })
                                ->rtrim('-')
                                ->jsonSerialize();
                        }

                        return $item;
                    });
                });

            // Separate api and non-api routes

            $web_paths = collect();

            $api_paths = $routes->filter(function ($item) use ($web_paths) {
                $matches = preg_match('/api./', $item['file']);

                if (! $matches) {
                    $web_paths->add($item);
                }

                return $matches;
            });

            $api_paths->each(function ($item) {
                $config = $this->getRouteApiConfiguration($item[$this->append_key] ?? null);
                Route::group($config, $item['path']);
            });

            $web_paths->each(function ($item) {
                $config = $this->getRouteWebConfiguration($item[$this->append_key] ?? null);
                Route::group($config, $item['path']);
            });
        }

        return $this;
    }

    /**
     * @param  array|null  $append_to_prefix
     * @return array
     */
    public function getRouteApiConfiguration(?array $append_to_prefix = null): array
    {
        return $this->getRouteConfiguration(true, $append_to_prefix);
    }

    /**
     * @param  array|null  $append_to_prefix
     * @return array
     */
    public function getRouteWebConfiguration(?array $append_to_prefix = null): array
    {
        return $this->getRouteConfiguration(false, $append_to_prefix);
    }

    /**
     * @param  bool  $is_api
     * @param  array|null  $append_to_prefix
     * @return string[]
     */
    public function getRouteConfiguration(bool $is_api, ?array $append_to_prefix = null): array
    {
        $config = [
            'middleware' => $is_api ? $this->getApiMiddleware() : $this->getWebMiddleware(),
            'prefix' => $this->getRoutePrefix(),
            'name' => null,
        ];

        // Prepare middleware

        if ($middleware = $is_api ? $this->getDefaultApiMiddleware() : $this->getDefaultWebMiddleware()) {
            $config['middleware'] = array_unique(array_merge($config['middleware'], $middleware));
        }

        // Middleware Group

        $middleware_group = $is_api ? 'api' : 'web';

        if (! in_array($middleware_group, $config['middleware'])) {
            $config['middleware'][] = $middleware_group;
        }

        // Prepare prefix and name

        if ($is_api) {
            $prefixes[] = 'api';
        }

        $prefixes[] = $config['prefix']; // Add previous prefix

        if ($append_to_prefix) {
            $prefixes[] = collect($append_to_prefix)->implode('/');
        }

        $config['prefix'] = collect($prefixes)->filter()->implode('/');

        if ($config['prefix']) {
            $config['name'] = Str::of($config['prefix'])
                ->after('api')
                ->finish('/')
                ->ltrim('/')
                ->replace('/', '.')
                ->jsonSerialize();
        }

        return $config;
    }

    /***** MORPH MAP *****/

    /**
     * @return $this
     */
    protected function bootMorphMap(): static
    {
        if (starterKit()->shouldEnforceMorphMap()) {
            enforceMorphMap($this->getMorphMap());
        }

        return $this;
    }
}
