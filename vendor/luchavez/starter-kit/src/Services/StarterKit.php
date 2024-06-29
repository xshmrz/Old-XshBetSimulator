<?php

namespace Luchavez\StarterKit\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Luchavez\StarterKit\Data\ServiceProviderData;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class StarterKit
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since 2021-11-25
 */
class StarterKit
{
    /**
     * @var array
     */
    protected array $paths = [];

    /**
     * @var array
     */
    protected array $providers = [];

    /**
     * @var array
     */
    protected array $exception_renders = [];

    /***** FOLDER NAMES OF LARAVEL FILES *****/

    public const CONFIG_DIR = 'config';

    public const MIGRATIONS_DIR = 'database/migrations';

    public const HELPERS_DIR = 'helpers';

    public const LANG_DIR = 'lang';

    public const TESTS_DIR = 'tests';

    public const ROUTES_DIR = 'routes';

    public const MODELS_DIR = 'Models';

    public const PROVIDERS_DIR = 'Providers';

    public const REPOSITORIES_DIR = 'Repositories';

    public const POLICIES_DIR = 'Policies';

    public const OBSERVERS_DIR = 'Observers';

    public const DOMAINS_DIR = 'domains';

    /**
     * @return string
     */
    public function getMainTag(): string
    {
        return 'starter-kit';
    }

    /**
     * @return Collection
     */
    public function getProviders(): Collection
    {
        return collect($this->providers);
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection<ServiceProvider>
     */
    public function getProvidersFromList(?string $package = null, ?string $domain = null): Collection
    {
        return $this->getProviders()
            ->where('package', $package)
            ->where('domain', $domain)
            ->map(fn (array $data) => ServiceProviderData::from($data)->getServiceProvider());
    }

    /**
     * @param  ServiceProvider  $provider
     * @return ServiceProviderData
     */
    public function addToProviders(ServiceProvider $provider): ServiceProviderData
    {
        $providers = $this->getProviders();

        $class = get_class($provider);

        $data = ServiceProviderData::from($provider);
        $providers = $providers->put($class, $data->toArray());
        $this->providers = $providers->toArray();

        return $data;
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection
     */
    public function getPaths(?string $package = null, ?string $domain = null): Collection
    {
        $paths = collect($this->paths);

        if ($package) {
            $paths = Arr::get($paths, 'packages.'.str_replace('/', '.', $package));
        }

        // Since domain name might contain dots due to encoding, dot notation is not possible.
        if ($domain && $paths && isset($paths[self::DOMAINS_DIR][$domain])) {
            $paths = $paths[self::DOMAINS_DIR][$domain];
        }

        return collect($paths);
    }

    /**
     * @param  ServiceProviderData  $data
     * @return bool
     */
    public function addToPaths(ServiceProviderData $data): bool
    {
        $paths = $this->getPaths()->toArray();

        $targets = $this->getFilesFromPaths($data->path);

        if ($data->package) {
            [$vendor_name, $package] = explode('/', $data->package);

            if ($data->domain) {
                $paths['packages'][$vendor_name][$package][self::DOMAINS_DIR][$data->domain]['path'] = $data->path;
                $paths['packages'][$vendor_name][$package][self::DOMAINS_DIR][$data->domain]['directories'] = $targets;
            } else {
                $paths['packages'][$vendor_name][$package]['path'] = $data->path;
                $paths['packages'][$vendor_name][$package]['directories'] = $targets;
            }
        } else {
            if ($data->domain) {
                $paths[self::DOMAINS_DIR][$data->domain]['path'] = $data->path;
                $paths[self::DOMAINS_DIR][$data->domain]['directories'] = $targets;
            } else {
                $paths['path'] = $data->path;
                $paths['directories'] = $targets;
            }
        }

        $this->paths = $paths;

        return true;
    }

    /**
     * @return Collection
     */
    public function getTargetDirectories(): Collection
    {
        return collect([
            self::CONFIG_DIR,
            self::MIGRATIONS_DIR,
            self::HELPERS_DIR,
            self::LANG_DIR,
            self::TESTS_DIR,
            self::ROUTES_DIR,
            self::MODELS_DIR,
            self::PROVIDERS_DIR,
            self::REPOSITORIES_DIR,
            self::POLICIES_DIR,
            self::OBSERVERS_DIR,
        ]);
    }

    /**
     * @param  string  $source_dir
     * @return array
     */
    protected function getFilesFromPaths(string $source_dir): array
    {
        return guess_file_or_directory_path($source_dir, $this->getTargetDirectories())
            ->mapWithKeys(function ($path, $directory) {
                $files = match ($directory) {
                    self::PROVIDERS_DIR,
                    self::MODELS_DIR => collect_classes_from_path($path)
                        ->mapWithKeys(fn ($model) => [
                            // ex: App/Models/User => User
                            $model => Str::of($model)->afterLast('\\')->jsonSerialize(),
                        ])->toArray(),

                    self::REPOSITORIES_DIR,
                    self::POLICIES_DIR,
                    self::OBSERVERS_DIR => collect_classes_from_path(
                        $path,
                        Str::of($directory)
                            ->singular()
                            ->studly()
                            ->jsonSerialize()
                    )->toArray(),

                    self::CONFIG_DIR,
                    self::HELPERS_DIR,
                    self::ROUTES_DIR => collect(File::allFiles($path))->map(fn (SplFileInfo $info) => [
                        'file' => $info->getFilename(),
                        'path' => Str::replace('\\', '/', $info->getRealPath()),
                        'name' => $info->getFilenameWithoutExtension(),
                    ]),

                    default => null
                };

                $result[$directory]['path'] = $path;

                if ($files) {
                    $result[$directory]['files'] = $files;
                }

                return $result;
            })
            ->toArray();
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  string|null  $dot_notation
     * @return Collection|null
     */
    public function getFromPaths(?string $package = null, ?string $domain = null, ?string $dot_notation = null): ?Collection
    {
        if ($value = Arr::get($this->getPaths($package, $domain), $dot_notation)) {
            return collect($value);
        }

        return null;
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  array  $only
     * @return Collection|null
     */
    public function getPathsOnly(?string $package = null, ?string $domain = null, array $only = []): ?Collection
    {
        $paths = $this->getFromPaths($package, $domain, 'directories')?->map(fn ($item) => $item['path']);

        return $paths?->only($only);
    }

    /**
     * @param  string|null  $package
     * @return Collection|null
     */
    public function getDomains(?string $package = null): ?Collection
    {
        return $this->getFromPaths($package, null, self::DOMAINS_DIR)?->map(fn ($value) => $value['path']);
    }

    /**
     * @return Collection
     */
    public function getRoot(): Collection
    {
        return $this->getFromPaths()->forget('packages');
    }

    /**
     * @return Collection|null
     */
    public function getPackages(): ?Collection
    {
        return $this->getFromPaths(dot_notation: 'packages');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getConfigs(?string $package = null, ?string $domain = null): ?Collection
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::CONFIG_DIR.'.files');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getMigrationsPath(?string $package = null, ?string $domain = null): ?Collection
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::MIGRATIONS_DIR.'.path');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getHelpers(?string $package = null, ?string $domain = null): ?Collection
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::HELPERS_DIR.'.files');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getRoutes(?string $package = null, ?string $domain = null): ?Collection
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::ROUTES_DIR.'.files');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return string|null
     */
    public function getTranslations(?string $package = null, ?string $domain = null): ?string
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::LANG_DIR)->get('path');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getModels(?string $package = null, ?string $domain = null): ?Collection
    {
        return $this->getFromPaths($package, $domain, 'directories.'.self::MODELS_DIR.'.files');
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @return Collection|null
     */
    public function getPossibleModels(?string $package = null, ?string $domain = null): ?Collection
    {
        $possibleModels = collect();

        // In getting possible models for Repository, Observer, and Policy files,
        // it should start from domain level, then to package level, then to root level.

        // Domain level
        if ($domain) {
            $possibleModels = $possibleModels->merge($this->getModels($package, $domain));
        }

        // Package level
        $possibleModels = $possibleModels->merge($this->getModels($package));

        // Root level
        $possibleModels = $possibleModels->merge($this->getModels());

        return $possibleModels->mapToGroups(
            function ($item, $key) {
                return [$item => $key];
            }
        );
    }

    /**
     * @param  string  $directory
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  array  $map
     * @return Collection|null
     */
    public function getModelRelatedFiles(string $directory, ?string $package = null, ?string $domain = null, array $map = []): ?Collection
    {
        if ($files = $this->getFromPaths($package, $domain, 'directories.'.$directory.'.files')) {
            $files = collect($files)->mapWithKeys(fn ($item, $key) => [$item => $key]);
            $map = collect($map)->only($files->keys());
            $files = $files->merge($map);
            $unmatched = $files->except($map->keys());

            if ($unmatched->count()) {
                $possible_models = $this->getPossibleModels($package, $domain);
                $unmatched = $unmatched->map(fn ($item) => $possible_models->get($item) ?? []);
                $files = $files->merge($unmatched);
            }

            return $files;
        }

        return null;
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  array  $policy_map
     * @return Collection|null
     */
    public function getPolicies(?string $package = null, ?string $domain = null, array $policy_map = []): ?Collection
    {
        return $this->getModelRelatedFiles(self::POLICIES_DIR, $package, $domain, $policy_map);
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  array  $observer_map
     * @return Collection|null
     */
    public function getObservers(?string $package = null, ?string $domain = null, array $observer_map = []): ?Collection
    {
        return $this->getModelRelatedFiles(self::OBSERVERS_DIR, $package, $domain, $observer_map);
    }

    /**
     * @param  string|null  $package
     * @param  string|null  $domain
     * @param  array  $repository_map
     * @return Collection|null
     */
    public function getRepositories(?string $package = null, ?string $domain = null, array $repository_map = []): ?Collection
    {
        return $this->getModelRelatedFiles(self::REPOSITORIES_DIR, $package, $domain, $repository_map);
    }

    /***** EXCEPTION RELATED *****/

    /**
     * @param  string|object|null  $exception_class
     * @return Collection|Closure|callable|null
     */
    public function getExceptionRenders(string|object|null $exception_class = null): Collection|Closure|callable|null
    {
        $result = collect($this->exception_renders);

        if ($exception_class) {
            return $result->get(get_class_name_from_object($exception_class));
        }

        return $result;
    }

    /**
     * @param  string  $exception_class
     * @param  Closure|callable  $closure
     * @param  bool  $override
     * @return bool
     */
    public function addExceptionRender(string $exception_class, Closure|callable $closure, bool $override = false): bool
    {
        // Check whether already exists
        if ($this->getExceptionRenders()->has($exception_class) && ! $override) {
            return false;
        }

        $this->exception_renders[$exception_class] = $closure;

        return true;
    }

    /***** USER MODEL *****/

    /**
     * @return string|null
     */
    public function getUserModel(): ?string
    {
        if (class_exists($model = config('starter-kit.user_model')) && is_eloquent_model($model)) {
            return $model;
        }

        return null;
    }

    /**
     * @return Builder|null
     */
    public function getUserQueryBuilder(): ?Builder
    {
        if ($model = $this->getUserModel()) {
            return call_user_func($model.'::query');
        }

        return null;
    }

    /***** POLYMORPHIC MAP *****/

    /**
     * @return Collection
     */
    public function getMorphMap(): Collection
    {
        return collect(Relation::morphMap());
    }

    /**
     * @param  string  $model_name
     * @return string|null
     */
    public function getMorphMapKey(string $model_name): ?string
    {
        if (is_eloquent_model($model_name)) {
            return $this->getMorphMap()->mapWithKeys(fn ($item, $key) => [$item => $key])->get($model_name);
        }

        return null;
    }

    /***** ROUTE MIDDLEWARES *****/

    /**
     * @param  bool  $is_api
     * @param  string  $separator
     * @return array
     */
    public function getRouteMiddleware(bool $is_api, string $separator = ';'): array
    {
        $middleware = $is_api ? config('starter-kit.api_middleware') : config('starter-kit.web_middleware');

        if (is_string($middleware)) {
            return explode($separator, $middleware);
        }

        return $middleware;
    }

    /***** OTHER METHODS *****/

    /**
     * @return bool
     */
    public function shouldOverrideExceptionHandler(): bool
    {
        return config('starter-kit.override_exception_handler');
    }

    /**
     * @return bool
     */
    public function shouldEnforceMorphMap(): bool
    {
        return config('starter-kit.enforce_morph_map');
    }

    /***** LOCALE RELATED *****/

    /**
     * @return string
     */
    public function getChangeLocaleKey(): string
    {
        return config('starter-kit.change_locale.key');
    }

    /**
     * @return bool
     */
    public function isChangeLocaleEnabled(): bool
    {
        return (bool) config('starter-kit.change_locale.enabled');
    }
}
