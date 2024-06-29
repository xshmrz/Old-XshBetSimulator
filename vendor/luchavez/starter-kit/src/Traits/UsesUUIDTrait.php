<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait UsesUUIDTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-11-19
 */
trait UsesUUIDTrait
{
    /**
     * Generates a UUID during model creation.
     */
    public static function bootUsesUuidTrait(): void
    {
        static::saving(
            static function (Model $model) {
                if (! isset($model->uuid)) {
                    $model->uuid = Str::uuid()->toString();
                }
            }
        );
    }
}
