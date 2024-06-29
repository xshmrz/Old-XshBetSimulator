<?php

namespace Luchavez\StarterKit\Traits;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Cache\CacheManager;
use RuntimeException;
use Throwable;

/**
 * Trait HasTaggableCacheTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait HasTaggableCacheTrait
{
    /**
     * @return string
     */
    abstract public function getMainTag(): string;

    /**
     * @var CacheManager|null
     */
    protected ?CacheManager $cache_manager = null;

    /**
     * @return CacheManager
     */
    public function getCacheManager(): CacheManager
    {
        return $this->cache_manager ?? cache();
    }

    /**
     * @param  CacheManager|null  $cache_manager
     */
    public function setCacheManager(?CacheManager $cache_manager): void
    {
        $this->cache_manager = $cache_manager;
    }

    /**
     * @return bool
     */
    public function isCacheTaggable(): bool
    {
        try {
            return method_exists($this->getCacheManager()->getStore(), 'tags');
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function clearCache(): bool
    {
        $manager = $this->getCacheManager();

        if ($this->isCacheTaggable()) {
            $manager = $manager->tags($this->getMainTag());
        }

        return $manager->flush();
    }

    /**
     * @param ...$tags
     * @return array
     */
    protected function getTags(...$tags): array
    {
        return collect($this->getMainTag())->merge($tags)->unique()->filter()->toArray();
    }

    /**
     * @param  string[]  $tags
     * @param  string  $key
     * @param  Closure  $closure
     * @param  bool  $rehydrate
     * @param  Closure|DateTimeInterface|DateInterval|int|null  $ttl
     * @return mixed
     */
    protected function getCache(array $tags, string $key, Closure $closure, bool $rehydrate = false, Closure|DateTimeInterface|DateInterval|int|null $ttl = null): mixed
    {
        $tags = $this->getTags(...$tags);

        $manager = $this->getCacheManager();

        $this->prepareCacheManagerAndKey(manager: $manager, key: $key, tags: $tags);

        if ($rehydrate) {
            $manager->forget($key);
        }

        // Copied and improved from \Illuminate\Cache\Repository's remember() function
        $value = $manager->get($key);

        if (! is_null($value)) {
            return $value;
        }

        // Pass reference to $ttl to provide option to override cache expiration
        $value = $closure($ttl);

        $ttl = value($ttl);

        if ($ttl !== 0) {
            $manager->put($key, $value, $ttl);
        }

        return $value;
    }

    /**
     * @param  string  $key
     * @param  string[]  $tags
     * @return bool
     */
    public function forgetCache(array $tags, string $key): bool
    {
        $tags = $this->getTags(...$tags);

        $manager = $this->getCacheManager();

        $this->prepareCacheManagerAndKey(manager: $manager, key: $key, tags: $tags);

        return $manager->forget($key);
    }

    /**
     * @param  CacheManager  $manager
     * @param  array  $tags
     * @param  string  $key
     * @return void
     */
    protected function prepareCacheManagerAndKey(CacheManager &$manager, string &$key, array $tags = []): void
    {
        if ($this->isCacheTaggable()) {
            $manager = $manager->tags($tags);
        } else {
            $key = collect(['tags' => $tags, 'key' => $key])->toJson();
        }
    }

    /**
     * @param  string  $class
     * @param  string  $base_class
     * @return void
     */
    public function validateClass(string &$class, string $base_class): void
    {
        $object = new $class();

        $class = get_class($object);

        if (! is_subclass_of($object, $base_class)) {
            throw new RuntimeException('Invalid '.$base_class.' class: '.$class);
        }
    }
}
