<?php

namespace Luchavez\StarterKit\Traits;

/**
 * Trait UsesProviderRoutesTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderRoutesTrait
{
    /**
     * @var string
     */
    protected string $append_key = 'append_to_prefix';

    /**
     * @var string|null
     */
    protected ?string $route_prefix = null;

    /**
     * @var bool
     */
    protected bool $prefix_route_with_file_name = false;

    /**
     * @var bool
     */
    protected bool $prefix_route_with_directory = false;

    /**
     * @var array
     */
    protected array $api_middleware = [];

    /**
     * @var array
     */
    protected array $web_middleware = [];

    /**
     * @var array|null
     */
    protected ?array $default_api_middleware = null;

    /**
     * @var array|null
     */
    protected ?array $default_web_middleware = null;

    /**
     * @return bool
     */
    public function areRoutesEnabled(): bool
    {
        return true;
    }

    /***** PREFIXING *****/

    /**
     * @return string|null
     */
    public function getRoutePrefix(): ?string
    {
        return $this->route_prefix;
    }

    /**
     * @param  string|null  $route_prefix
     */
    public function setRoutePrefix(?string $route_prefix): void
    {
        $this->route_prefix = $route_prefix;
    }

    /**
     * @param  string|null  $route_prefix
     * @return $this
     */
    public function routePrefix(?string $route_prefix): static
    {
        $this->route_prefix = $route_prefix;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldPrefixRouteWithFileName(): bool
    {
        return $this->prefix_route_with_file_name;
    }

    /**
     * @return $this
     */
    public function prefixRouteWithFileName(bool $bool = true): static
    {
        $this->prefix_route_with_file_name = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldPrefixRouteWithDirectory(): bool
    {
        return $this->prefix_route_with_directory;
    }

    /**
     * @return $this
     */
    public function prefixRouteWithDirectory(bool $bool = true): static
    {
        $this->prefix_route_with_directory = $bool;

        return $this;
    }

    /***** MIDDLEWARE *****/

    /**
     * @return array
     */
    public function getWebMiddleware(): array
    {
        return $this->web_middleware;
    }

    /**
     * @param  array  $web_middleware
     */
    public function setWebMiddleware(array $web_middleware): void
    {
        $this->web_middleware = $web_middleware;
    }

    /**
     * @param  array  $web_middleware
     * @return $this
     */
    public function webMiddleware(array $web_middleware): static
    {
        $this->web_middleware = $web_middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getApiMiddleware(): array
    {
        return $this->api_middleware;
    }

    /**
     * @param  array  $api_middleware
     */
    public function setApiMiddleware(array $api_middleware): void
    {
        $this->api_middleware = $api_middleware;
    }

    /**
     * @param  array  $api_middleware
     * @return $this
     */
    public function apiMiddleware(array $api_middleware): static
    {
        $this->api_middleware = $api_middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultWebMiddleware(): array
    {
        return $this->default_web_middleware ?? starterKit()?->getRouteMiddleware(false) ?? [];
    }

    /**
     * @param  array|null  $default_web_middleware
     */
    public function setDefaultWebMiddleware(?array $default_web_middleware): void
    {
        $this->default_web_middleware = $default_web_middleware;
    }

    /**
     * @param  array|null  $default_web_middleware
     * @return $this
     */
    public function defaultWebMiddleware(?array $default_web_middleware): static
    {
        $this->default_web_middleware = $default_web_middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultApiMiddleware(): array
    {
        return $this->default_api_middleware ?? starterKit()?->getRouteMiddleware(true) ?? [];
    }

    /**
     * @param  array|null  $default_api_middleware
     */
    public function setDefaultApiMiddleware(?array $default_api_middleware): void
    {
        $this->default_api_middleware = $default_api_middleware;
    }

    /**
     * @param  array|null  $default_api_middleware
     * @return $this
     */
    public function defaultApiMiddleware(?array $default_api_middleware): static
    {
        $this->default_api_middleware = $default_api_middleware;

        return $this;
    }
}
