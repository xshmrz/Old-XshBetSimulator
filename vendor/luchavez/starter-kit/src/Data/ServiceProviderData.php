<?php

namespace Luchavez\StarterKit\Data;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Luchavez\StarterKit\Abstracts\BaseJsonSerializable;
use Luchavez\StarterKit\Abstracts\BaseStarterKitServiceProvider;

/**
 * Class ServiceProviderData
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ServiceProviderData extends BaseJsonSerializable
{
    /**
     * @var string
     */
    public string $class;

    /**
     * @var string
     */
    public string $composer;

    public ?ServiceProvider $provider;

    /**
     * @var string|null
     */
    public ?string $package = null;

    /**
     * @var string|null
     */
    public ?string $domain = null;

    /**
     * @var string
     */
    public string $path;

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     */
    public function __construct(mixed $data = [], ?string $key = null)
    {
        parent::__construct($data, $key);

        // Add to StarterKit's paths
        if ($data instanceof ServiceProvider) {
            starterKit()->addToPaths($this);
        }
    }

    /***** GETTERS *****/

    /**
     * @param  bool  $as_namespace
     * @param  string  $separator
     * @return string|null
     */
    public function getDecodedDomain(bool $as_namespace = false, string $separator = '.'): ?string
    {
        if ($this->domain) {
            return domain_decode($this->domain, $as_namespace, $separator);
        }

        return null;
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @param  ServiceProvider  $provider
     * @return array
     */
    protected function parseServiceProvider(ServiceProvider $provider): array
    {
        $this->provider = $provider;

        $class = get_class($provider);
        $domain = domain_encode($class);
        $provider_directory = get_dir_from_object_class_dir($provider);

        $domain_decoded = null;

        $directory = Str::of($provider_directory)
            ->when(
                $domain,
                function (Stringable $str) use ($domain, &$domain_decoded) {
                    $domain_decoded = domain_decode($domain);

                    return $str->before($domain_decoded);
                },
                function (Stringable $str) {
                    $base = str_replace('\\', '/', base_path());

                    return $str->after($base)->before('src/')->before('app/')->prepend($base);
                }
            )
            ->jsonSerialize();

        $search = 'composer.json';
        $composer = guess_file_or_directory_path($directory, $search, true);
        $package = get_contents_from_composer_json($composer)?->get('name');
        $package = $package == 'laravel/laravel' ? null : $package;

        $path = Str::of($composer)
            ->before($search)
            ->when($domain_decoded, fn (Stringable $str) => $str->rtrim('/')->append($domain_decoded))
            ->jsonSerialize();

        return [
            'class' => get_class($provider),
            'composer' => $composer,
            'package' => $package,
            'domain' => $domain,
            'path' => $path,
        ];
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->provider ?? app()->getProvider($this->class);
    }

    /**
     * @return Collection|null
     */
    public function getPackageEnvVars(): ?Collection
    {
        $provider = $this->getServiceProvider();
        if ($provider instanceof BaseStarterKitServiceProvider) {
            return collect($provider->getEnvVars())
                ->map(fn ($item) => (is_string($item) || is_null($item)) ? $item : json_encode($item));
        }

        return null;
    }

    /**
     * @return bool
     */
    public function publishEnvVars(): bool
    {
        if ($env_vars = $this->getPackageEnvVars()) {
            $title = $this->package ?? 'Laravel';
            if ($this->domain) {
                $title .= ' ('.$this->domain.')';
            }

            return add_contents_to_env($env_vars, $title);
        }

        return false;
    }
}
