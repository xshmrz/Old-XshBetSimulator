<?php

namespace Luchavez\StarterKit\Scopes;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Foundation\Auth\User;

/**
 * Class ModelOwnedScope
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ModelOwnedScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = ['Owned'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        //
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
     * Get the "owner id" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getOwnerIdColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedOwnerIdColumn();
        }

        return $builder->getModel()->getOwnerIdColumn();
    }

    /**
     * Add the owned extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addOwned(Builder $builder): void
    {
        $builder->macro('owned', function (Builder $builder, User|bool|null $owner = null) {
            if (is_null($owner) && is_null($owner = auth()->user())) {
                return $builder;
            }

            return $builder->withoutGlobalScope($this)
                ->when(
                    $owner instanceof Authenticatable,
                    fn (Builder $q) => $q->where($this->getOwnerIdColumn($builder), $owner->getKey()),
                    fn (Builder $q) => $q->whereNull(columns: $this->getOwnerIdColumn($builder), not: $owner),
                );
        });
    }
}
