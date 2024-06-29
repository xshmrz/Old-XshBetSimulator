<?php

namespace Luchavez\StarterKit\Traits;

use Luchavez\StarterKit\Scopes\ModelUsedScope;

/**
 * Trait ModelUsedTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait ModelUsedTrait
{
    /**
     * Boot the model used trait for a model.
     *
     * @return void
     */
    public static function bootModelUsedTrait(): void
    {
        static::addGlobalScope(new ModelUsedScope());
    }

    /**
     * Initialize the model used trait for an instance.
     *
     * @return void
     */
    public function initializeModelUsedTrait(): void
    {
        if (! isset($this->casts[$this->getUsageLeftColumn()])) {
            $this->casts[$this->getUsageLeftColumn()] = 'int';
        }
    }

    /**
     * Get the name of the "usage left" column.
     *
     * @return string
     */
    public static function getUsageLeftColumn(): string
    {
        return defined('static::USAGE_LEFT') ? static::USAGE_LEFT : config('starter-kit.columns.usage.column');
    }

    /**
     * Get the fully qualified "usage left" column.
     *
     * @return string
     */
    public function getQualifiedUsageLeftColumn(): string
    {
        return $this->qualifyColumn($this->getUsageLeftColumn());
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @param  int  $amount
     * @return bool
     */
    public function use(int $amount = 1): bool
    {
        $column = self::getUsageLeftColumn();

        if ($this->$column) {
            $this->$column -= $amount;
        }

        return $this->save();
    }

    /**
     * @param  int  $amount
     * @return bool
     */
    public function unuse(int $amount = 1): bool
    {
        $column = self::getUsageLeftColumn();

        if ($this->$column >= 0) {
            $this->$column += $amount;
        }

        return $this->save();
    }

    /**
     * @return mixed
     */
    public function useQuietly(int $amount = 1): bool
    {
        return static::withoutEvents(fn () => $this->use($amount));
    }

    /**
     * @return mixed
     */
    public function unuseQuietly(int $amount = 1): bool
    {
        return static::withoutEvents(fn () => $this->unuse($amount));
    }
}
