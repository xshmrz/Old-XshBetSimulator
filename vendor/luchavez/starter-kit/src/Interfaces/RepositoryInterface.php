<?php

namespace Luchavez\StarterKit\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface RepositoryInterface
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-11-19
 */
interface RepositoryInterface
{
    /**
     * @param  mixed  $attributes
     * @return Collection|array|null
     */
    public function all(mixed $attributes = null): Collection|array|null;

    /**
     * @param  mixed  $attributes
     * @return Model|null
     */
    public function make(mixed $attributes = null): ?Model;

    /**
     * @param  mixed  $attributes
     * @return Model|null
     */
    public function create(mixed $attributes = null): ?Model;

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function get(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null;

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function update(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null;

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function delete(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null;

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function restore(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null;
}
