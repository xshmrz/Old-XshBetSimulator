<?php

namespace Luchavez\StarterKit\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Carbon;

/**
 * Class ModelExpiringScope
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ModelExpiringScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = ['Unexpire', 'Expire', 'WithExpired', 'WithoutExpired', 'OnlyExpired'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->withoutExpired();
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  Builder  $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "expires at" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getExpiresAtColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedExpiresAtColumn();
        }

        return $builder->getModel()->getExpiresAtColumn();
    }

    /**
     * Add the unexpire extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addUnexpire(Builder $builder): void
    {
        $builder->macro('unexpire', function (Builder $builder) {
            $builder->withExpired();

            return $builder->update([$this->getExpiresAtColumn($builder) => null]);
        });
    }

    /**
     * Add to expire extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addExpire(Builder $builder): void
    {
        $builder->macro('expire', function (Builder $builder, Carbon|string|null $date_time = null) {
            $column = $this->getExpiresAtColumn($builder);

            return $builder->update([$column => $this->getExpirationDateTime($date_time)]);
        });
    }

    /**
     * Add the with-expired extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithExpired(Builder $builder): void
    {
        $builder->macro('withExpired', function (Builder $builder, bool $with_expired = true, Carbon|string|null $date_time = null) {
            if (! $with_expired) {
                return $builder->withoutExpired($date_time);
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-expired extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithoutExpired(Builder $builder): void
    {
        $builder->macro('withoutExpired', function (Builder $builder, Carbon|string|null $date_time = null) {
            $column = $this->getExpiresAtColumn($builder);

            $builder->withoutGlobalScope($this)
                ->whereNull($column)
                ->orWhere($column, '>', $this->getExpirationDateTime($date_time));

            return $builder;
        });
    }

    /**
     * Add the only-expired extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addOnlyExpired(Builder $builder): void
    {
        $builder->macro('onlyExpired', function (Builder $builder, Carbon|string|null $date_time = null) {
            $column = $this->getExpiresAtColumn($builder);

            return $builder->withoutGlobalScope($this)
                ->whereNotNull($column)
                ->where($column, '<=', $this->getExpirationDateTime($date_time));
        });
    }

    /**
     * @param  Carbon|string|null  $date_time
     * @return string
     */
    public static function getExpirationDateTime(Carbon|string|null $date_time = null): string
    {
        if ($date_time) {
            $date_time = $date_time instanceof Carbon ? $date_time : Carbon::parse($date_time);
        } else {
            $date_time = now();
        }

        return $date_time->toDateTimeString();
    }
}
