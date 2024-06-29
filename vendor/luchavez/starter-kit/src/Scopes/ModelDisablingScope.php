<?php

namespace Luchavez\StarterKit\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Foundation\Auth\User;
use Luchavez\StarterKit\Exceptions\DisableReasonRequiredException;
use Luchavez\StarterKit\Exceptions\DisablerRequiredException;

/**
 * Class ModelDisablingScope
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ModelDisablingScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = ['Enable', 'Disable', 'WithDisabled', 'WithoutDisabled', 'OnlyDisabled'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->withoutDisabled();
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
     * Get the "disabled at" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getDisabledAtColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedDisabledAtColumn();
        }

        return $builder->getModel()->getDisabledAtColumn();
    }

    /**
     * Get the "disabler id" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getDisablerIdColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedDisablerIdColumn();
        }

        return $builder->getModel()->getDisablerIdColumn();
    }

    /**
     * Get the "disable reason" column for the builder.
     *
     * @param  Builder  $builder
     * @return string
     */
    protected function getDisableReasonColumn(Builder $builder): string
    {
        if (count($builder->getQuery()->joins ?? []) > 0) {
            return $builder->getModel()->getQualifiedDisableReasonColumn();
        }

        return $builder->getModel()->getDisableReasonColumn();
    }

    /**
     * Add to disable extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addDisable(Builder $builder): void
    {
        $builder->macro('disable', function (Builder $builder, ?string $reason = null, ?User $disabler = null) {
            $disabler = $disabler ?? auth()->user();

            if (is_null($disabler) && config('starter-kit.columns.disables.disabler_required')) {
                throw new DisablerRequiredException();
            } else {
                $attributes[$this->getDisablerIdColumn($builder)] = $disabler?->getKey();
            }

            if (is_null($reason) && config('starter-kit.columns.disables.disable_reason_required')) {
                throw new DisableReasonRequiredException();
            } else {
                $attributes[$this->getDisableReasonColumn($builder)] = $reason;
            }

            $attributes[$this->getDisabledAtColumn($builder)] = $builder->getModel()->freshTimestampString();

            return $builder->update($attributes);
        });
    }

    /**
     * Add the enable extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addEnable(Builder $builder): void
    {
        $builder->macro('enable', function (Builder $builder) {
            $builder->withDisabled();

            return $builder->update([
                $this->getDisabledAtColumn($builder) => null,
                $this->getDisablerIdColumn($builder) => null,
                $this->getDisableReasonColumn($builder) => null,
            ]);
        });
    }

    /**
     * Add the with-disabled extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithDisabled(Builder $builder): void
    {
        $builder->macro('withDisabled', function (Builder $builder, $with_disabled = true) {
            if (! $with_disabled) {
                return $builder->withoutDisabled();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-disabled extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addWithoutDisabled(Builder $builder): void
    {
        $builder->macro('withoutDisabled', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNull($this->getDisabledAtColumn($builder));

            return $builder;
        });
    }

    /**
     * Add the only-disabled extension to the builder.
     *
     * @param  Builder  $builder
     * @return void
     */
    protected function addOnlyDisabled(Builder $builder): void
    {
        $builder->macro('onlyDisabled', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNotNull($this->getDisabledAtColumn($builder));

            return $builder;
        });
    }
}
