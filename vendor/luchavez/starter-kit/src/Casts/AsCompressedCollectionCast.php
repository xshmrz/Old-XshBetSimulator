<?php

namespace Luchavez\StarterKit\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Support\Collection;

/**
 * Class AsCompressedCollectionCast
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since 2022-05-04
 */
class AsCompressedCollectionCast extends AsCollection
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return CastsAttributes|string
     */
    public static function castUsing(array $arguments): CastsAttributes|string
    {
        return new class() implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes): ?Collection
            {
                return isset($attributes[$key]) ?
                    new Collection(json_decode(gzinflate($attributes[$key]), true)) :
                    null;
            }

            public function set($model, $key, $value, $attributes): array
            {
                return [$key => gzdeflate(json_encode($value), 9)];
            }
        };
    }
}
