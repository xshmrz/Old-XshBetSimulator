<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Luchavez\StarterKit\Scopes\ModelExpiringScope;

/**
 * Trait ModelExpiringTrait
 *
 * @method static static|Builder|\Illuminate\Database\Query\Builder withExpired(bool $with_expired = true, Carbon|string|null $date_time = null)
 * @method static static|Builder|\Illuminate\Database\Query\Builder onlyExpired(Carbon|string|null $date_time = null)
 * @method static static|Builder|\Illuminate\Database\Query\Builder withoutExpired(Carbon|string|null $date_time = null)
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait ModelExpiringTrait
{
    /**
     * Boot the expiring trait for a model.
     *
     * @return void
     */
    public static function bootModelExpiringTrait(): void
    {
        static::addGlobalScope(new ModelExpiringScope());
    }

    /**
     * Initialize the expiring trait for an instance.
     *
     * @return void
     */
    public function initializeModelExpiringTrait(): void
    {
        if (! isset($this->casts[$this->getExpiresAtColumn()])) {
            $this->casts[$this->getExpiresAtColumn()] = 'datetime';
        }
    }

    /**
     * Get the name of the "expires at" column.
     *
     * @return string
     */
    public static function getExpiresAtColumn(): string
    {
        return defined('static::EXPIRES_AT') ? static::EXPIRES_AT : config('starter-kit.columns.expires.column');
    }

    /**
     * Get the fully qualified "expires at" column.
     *
     * @return string
     */
    public function getQualifiedExpiresAtColumn(): string
    {
        return $this->qualifyColumn($this->getExpiresAtColumn());
    }

    /**
     * @return bool
     */
    public function getIsExpiredAttribute(): bool
    {
        $column = $this->getExpiresAtColumn();
        $value = $this->$column;

        return $value && $value->isPast();
    }

    /**
     * @param  Carbon|string|null  $date_time
     * @return bool
     */
    public function expire(Carbon|string|null $date_time = null): bool
    {
        $column = self::getExpiresAtColumn();
        $this->$column = ModelExpiringScope::getExpirationDateTime($date_time);

        return $this->save();
    }

    /**
     * @return bool
     */
    public function unexpire(): bool
    {
        $column = self::getExpiresAtColumn();
        $this->$column = null;

        return $this->save();
    }

    /**
     * @param  Carbon|string|null  $date_time
     * @return bool
     */
    public function expireQuietly(Carbon|string|null $date_time = null): bool
    {
        return static::withoutEvents(fn () => $this->expire($date_time));
    }

    /**
     * @return bool
     */
    public function unexpireQuietly(): bool
    {
        return static::withoutEvents(fn () => $this->unexpire());
    }
}
