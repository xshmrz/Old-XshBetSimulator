<?php

namespace Luchavez\StarterKit\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class ModelUsedScope
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ModelUsedScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = ['Use', 'Unuse', 'WithUsed', 'WithoutUsed', 'OnlyUsed'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->withoutUsed();
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
     * Get the "usage left" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getUsageLeftColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedUsageLeftColumn();
        }

        return $builder->getModel()->getUsageLeftColumn();
    }

    /**
     * Add the use extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addUse(Builder $builder): void
    {
        $builder->macro('use', function (Builder $builder, int $amount = 1) {
            return $builder->decrement(column: $this->getUsageLeftColumn($builder), amount: $amount);
        });
    }

    /**
     * Add the unuse extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addUnuse(Builder $builder): void
    {
        $builder->macro('unuse', function (Builder $builder, int $amount = 1) {
            return $builder->increment(column: $this->getUsageLeftColumn($builder), amount: $amount);
        });
    }

    /**
     * Add the with-used extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithUsed(Builder $builder): void
    {
        $builder->macro('withUsed', function (Builder $builder, $with_used = true) {
            if (! $with_used) {
                return $builder->withoutUsed();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-used extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithoutUsed(Builder $builder): void
    {
        $builder->macro('withoutUsed', function (Builder $builder) {
            $column = $this->getUsageLeftColumn($builder);
            $builder->withoutGlobalScope($this)->where(function (Builder $q) use ($column) {
                $q->whereNull($column)->orWhere($column, '>', 0);
            });

            return $builder;
        });
    }

    /**
     * Add the only-used extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addOnlyUsed(Builder $builder): void
    {
        $builder->macro('onlyUsed', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)->where($this->getUsageLeftColumn($builder), '<=', 0);
        });
    }
}
