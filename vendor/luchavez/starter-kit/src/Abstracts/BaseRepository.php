<?php

namespace Luchavez\StarterKit\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Luchavez\StarterKit\Interfaces\RepositoryInterface;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Abstract Class BaseRepository
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-11-19
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @param  QueryBuilder|null  $builder
     */
    public function __construct(protected ?QueryBuilder $builder = null)
    {
    }

    /**
     * @return QueryBuilder|null
     */
    public function builder(): ?QueryBuilder
    {
        return $this->builder?->clone();
    }

    /**
     * @return QueryBuilder|null
     */
    public function getBuilder(): ?QueryBuilder
    {
        return $this->builder();
    }

    /**
     * @param  mixed  $attributes
     * @return Collection|array|null
     */
    public function all(mixed $attributes = null): Collection|array|null
    {
        return $this->builder()?->get();
    }

    /**
     * @param  mixed  $attributes
     * @return Model|null
     */
    public function make(mixed $attributes = null): ?Model
    {
        return $this->builder()?->firstOrNew($attributes ?? []);
    }

    /**
     * @param  mixed  $attributes
     * @return Model|null
     */
    public function create(mixed $attributes = null): ?Model
    {
        $found_or_new = $this->make($attributes);

        if ($found_or_new && ! $found_or_new->exists) {
            if ($found_or_new->save()) {
                return $found_or_new;
            }

            return null;
        }

        return $found_or_new;
    }

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function get(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null
    {
        if ($id) {
            return $id instanceof Model ? $id : $this->builder()?->findOrFail($id);
        }

        return $this->all();
    }

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function update(int|string|array|Model|null $id = null, mixed $attributes = []): Model|Collection|array|null
    {
        $model = $id instanceof Model ? $id : $this->get($id);

        if ($model instanceof Model) {
            $model->fill($attributes);
            $model->save();
        } elseif ($model instanceof Collection) {
            $model->toQuery()->update($attributes);
            $model = $model->fresh();
        }

        return $model;
    }

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function delete(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null
    {
        if ($id instanceof Model) {
            return $id->delete() ? $id : null;
        }

        $builder = $this->builder()
            ->when($id, function (Builder $builder) use ($id) {
                $key = $builder->getModel()->getQualifiedKeyName();

                return is_array($id) ? $builder->whereIn($key, $id) : $builder->where($key, $id);
            });

        if ($builder->delete()) {
            $builder = $builder->onlyTrashed();

            return $id && ! is_array($id) ? $builder->first() : $builder->get();
        }

        return null;
    }

    /**
     * @param  int|string|array|null|Model  $id
     * @param  mixed  $attributes
     * @return Model|Collection|array|null
     */
    public function restore(int|string|array|Model|null $id = null, mixed $attributes = null): Model|Collection|array|null
    {
        if ($id instanceof Model && class_uses_trait($id, SoftDeletes::class)) {
            return $id->restore() ? $id : null;
        }

        $builder = $this->builder();

        if ($builder?->hasMacro('restore')) {
            $builder = $builder
                ->when($id, function (Builder $builder) use ($id) {
                    $key = $builder->getModel()->getQualifiedKeyName();

                    return is_array($id) ? $builder->whereIn($key, $id) : $builder->where($key, $id);
                });

            if ($builder->clone()->onlyTrashed()->restore()) {
                return $id && ! is_array($id) ? $builder->first() : $builder->get();
            }
        }

        return null;
    }
}
