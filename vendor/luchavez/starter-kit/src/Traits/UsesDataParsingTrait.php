<?php

namespace Luchavez\StarterKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Luchavez\StarterKit\Abstracts\BaseJsonSerializable;
use ReflectionException;

/**
 * Trait NeedsDataParsingTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesDataParsingTrait
{
    /**
     * @param  array  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseArray(array $response, ?string $key = null): array
    {
        return $key && isset($response[$key]) ? Arr::wrap($response[$key]) : $response;
    }

    /**
     * @param  Response  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseResponse(Response $response, ?string $key = null): array
    {
        if ($response->ok() && $array = $response->json($key)) {
            return $this->parse($array);
        }

        return [];
    }

    /**
     * @param  Request  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseRequest(Request $response, ?string $key = null): array
    {
        $response = $response instanceof FormRequest ? $response->validated() : $response->all();

        return $this->parse($response, $key);
    }

    /**
     * @param  Collection  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseCollection(Collection $response, ?string $key = null): array
    {
        return $key && $response->has($key) ? $this->parse($response->get($key)) : $response->toArray();
    }

    /**
     * @param  BaseJsonSerializable  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseBaseJsonSerializable(BaseJsonSerializable $response, ?string $key = null): array
    {
        return $this->parse($response->toArray(), $key);
    }

    /**
     * @param  Model  $response
     * @param  string|null  $key
     * @return array
     */
    public function parseModel(Model $response, ?string $key = null): array
    {
        return $key && isset($response->$key) ? $this->parse($response->$key) : $response->toArray();
    }

    /**
     * @param  mixed  $data
     * @param  string|null  $key
     * @return array
     */
    public function parse(mixed $data = [], ?string $key = null): array
    {
        if (is_array($data)) {
            return $this->parseArray($data, $key);
        }

        if (is_subclass_of($data, BaseJsonSerializable::class)) {
            return $this->parseBaseJsonSerializable($data, $key);
        }

        if (is_subclass_of($data, Response::class)) {
            return $this->parseResponse($data, $key);
        }

        if (is_subclass_of($data, Request::class)) {
            return $this->parseRequest($data, $key);
        }

        if (is_subclass_of($data, Collection::class)) {
            return $this->parseCollection($data, $key);
        }

        if (is_subclass_of($data, Model::class)) {
            return $this->parseModel($data, $key);
        }

        try {
            if (is_object($data)) {
                if (($class = get_class($data)) && method_exists($this, $method = 'parse'.class_basename($class))) {
                    return $this->$method($data, $key);
                }

                // Attempt checking for parents
                while ($class = get_parent_class($class)) {
                    if (method_exists($this, $method = 'parse'.class_basename($class))) {
                        return $this->$method($data, $key);
                    }
                }
            }
        } catch (ReflectionException) {
            return [];
        }

        return [];
    }
}
