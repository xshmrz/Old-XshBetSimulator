<?php

namespace Luchavez\StarterKit\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class MustBeSlugCase
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class MustBeSlugCase implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected string $separator = '-')
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Str::of($value)->slug($this->separator)->exactly($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must be must be in slug case.';
    }
}
