<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use Luchavez\StarterKit\Exceptions\DisableReasonRequiredException;
use Luchavez\StarterKit\Exceptions\DisablerRequiredException;
use Luchavez\StarterKit\Scopes\ModelDisablingScope;

/**
 * Trait ModelDisablesTrait
 *
 * @method static static|Builder|\Illuminate\Database\Query\Builder withDisabled(bool $with_disabled = true)
 * @method static static|Builder|\Illuminate\Database\Query\Builder onlyDisabled()
 * @method static static|Builder|\Illuminate\Database\Query\Builder withoutDisabled()
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait ModelDisablingTrait
{
    /**
     * Boot the disabling trait for a model.
     *
     * @return void
     */
    public static function bootModelDisablingTrait(): void
    {
        static::addGlobalScope(new ModelDisablingScope());
    }

    /**
     * Initialize the disabling trait for an instance.
     *
     * @return void
     */
    public function initializeModelDisablingTrait(): void
    {
        if (! isset($this->casts[$this->getDisabledAtColumn()])) {
            $this->casts[$this->getDisabledAtColumn()] = 'datetime';
        }
    }

    /**
     * Get the name of the "disabled at" column.
     *
     * @return string
     */
    public static function getDisabledAtColumn(): string
    {
        return defined('static::DISABLED_AT') ? static::DISABLED_AT : config('starter-kit.columns.disables.column');
    }

    /**
     * Get the fully qualified "disabled at" column.
     *
     * @return string
     */
    public function getQualifiedDisabledAtColumn(): string
    {
        return $this->qualifyColumn(self::getDisabledAtColumn());
    }

    /**
     * Get the name of the "disabler id" column.
     *
     * @return string
     */
    public static function getDisablerIdColumn(): string
    {
        return defined('static::DISABLER_ID') ? static::DISABLER_ID : config('starter-kit.columns.disables.disabler_column');
    }

    /**
     * Get the fully qualified "disabler id" column.
     *
     * @return string
     */
    public function getQualifiedDisablerIdColumn(): string
    {
        return $this->qualifyColumn(self::getDisablerIdColumn());
    }

    /**
     * Get the name of the "disable reason" column.
     *
     * @return string
     */
    public static function getDisableReasonColumn(): string
    {
        return defined('static::DISABLE_REASON') ? static::DISABLE_REASON : config('starter-kit.columns.disables.disable_reason_column');
    }

    /**
     * Get the fully qualified "disable reason" column.
     *
     * @return string
     */
    public function getQualifiedDisableReasonColumn(): string
    {
        return $this->qualifyColumn(self::getDisableReasonColumn());
    }

    /***** RELATIONSHIPS *****/

    /**
     * @return BelongsTo
     */
    public function disabler(): BelongsTo
    {
        $model = starterKit()->getUserModel();

        return $this->belongsTo($model, self::getDisablerIdColumn());
    }

    /***** ACCESSORS *****/

    /**
     * @return bool
     */
    public function getIsEnabledAttribute(): bool
    {
        $column = $this->getDisabledAtColumn();

        return is_null($this->$column);
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @param  string|null  $reason
     * @param  User|null  $disabler
     * @return bool
     *
     * @throws DisablerRequiredException|DisableReasonRequiredException
     */
    public function disable(?string $reason = null, ?User $disabler = null): bool
    {
        $disabler = $disabler ?? auth()->user();

        if (is_null($disabler) && config('starter-kit.columns.disables.disabler_required')) {
            throw new DisablerRequiredException();
        } else {
            $this->{self::getDisablerIdColumn()} = $disabler?->getKey();
        }

        if (is_null($reason) && config('starter-kit.columns.disables.disable_reason_required')) {
            throw new DisableReasonRequiredException();
        } else {
            $this->{self::getDisableReasonColumn()} = $reason;
        }

        $this->{self::getDisabledAtColumn()} = $this->freshTimestampString();

        return $this->save();
    }

    /**
     * @return bool
     */
    public function enable(): bool
    {
        $this->{self::getDisabledAtColumn()} = null;
        $this->{self::getDisablerIdColumn()} = null;
        $this->{self::getDisableReasonColumn()} = null;

        return $this->save();
    }

    /**
     * @return mixed
     *
     * @throws DisableReasonRequiredException|DisablerRequiredException
     */
    public function disableQuietly(?string $reason = null, ?User $disabler = null): bool
    {
        return static::withoutEvents(fn () => $this->disable($reason, $disabler));
    }

    /**
     * @return mixed
     */
    public function enableQuietly(): bool
    {
        return static::withoutEvents(fn () => $this->enable());
    }
}
