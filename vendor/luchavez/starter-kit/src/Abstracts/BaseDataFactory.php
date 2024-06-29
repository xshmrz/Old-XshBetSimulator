<?php

namespace Luchavez\StarterKit\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseDataFactory
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since 2022-05-04
 */
abstract class BaseDataFactory extends BaseJsonSerializable
{
    /**
     * @return Builder
     *
     * @example User::query()
     */
    abstract public function getBuilder(): Builder;

    /**
     * To avoid duplicate entries on database, checking if the model already exists by its unique keys is a must.
     *
     * @return array
     */
    public function getUniqueKeys(): array
    {
        return [];
    }

    /**
     * This is to avoid merging incorrect fields to Eloquent model. This is used on `mergeFieldsToModel()`.
     *
     * @return array
     */
    public function getExceptKeys(): array
    {
        return [];
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return Builder|Model
     */
    public function make(mixed $data = [], ?string $key = null): Model|Builder
    {
        $this->mergeDataToFields($data, $key);

        $model = $this->getBuilder()->getModel()->newModelInstance();

        $this->mergeFieldsToModel($model);

        return $model;
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return Model|Builder|null
     */
    public function create(mixed $data = [], ?string $key = null): Model|Builder|null
    {
        $model = $this->make($data, $key);

        return $model->save() ? $model : null;
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return Model|Builder|null
     */
    public function firstOrNew(mixed $data = [], ?string $key = null): Model|Builder|null
    {
        $this->mergeDataToFields($data, $key);

        $unique_keys = $this->getFieldKeys()->intersect($this->getUniqueKeys());

        $attributes = $this->collect()->only($unique_keys);

        $builder = $this->getBuilder();

        // If attributes is not empty, check if exists on database
        if ($attributes->isNotEmpty()) {
            $attributes->each(function ($item, $key) use ($builder) {
                $builder->where($key, $item);
            });

            if ($model = $builder->first()) {
                return $model;
            }
        }

        // If it does not exist on database, make a model
        $model = $builder->getModel()->newModelInstance();

        $this->mergeFieldsToModel($model);

        return $model;
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return Model|Builder|null
     */
    public function firstOrCreate(mixed $data = [], ?string $key = null): Model|Builder|null
    {
        $model = $this->firstOrNew($data, $key);

        return $model->save() ? $model : null;
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return Model|Builder|null
     */
    public function updateOrCreate(mixed $data = [], ?string $key = null): Model|Builder|null
    {
        $model = $this->firstOrNew($data, $key);

        // Update instead if model exists
        if ($model->exists()) {
            $this->mergeFieldsToModel($model);
            if ($model->isDirty()) {
                $model->save();
            }

            return $model;
        }

        return $model->save() ? $model : null;
    }

    /**
     * @param  Model  $model
     * @return void
     */
    public function mergeFieldsToModel(Model $model): void
    {
        $except_keys = $this->getFieldKeys()->intersect($this->getExceptKeys());

        $this->collect()->except($except_keys)->each(function ($item, $key) use ($model) {
            $model->$key = $item;
        });
    }
}
