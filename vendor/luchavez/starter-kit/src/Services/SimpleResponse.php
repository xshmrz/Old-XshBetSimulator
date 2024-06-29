<?php

namespace Luchavez\StarterKit\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Luchavez\StarterKit\Traits\UsesDataParsingTrait;

/**
 * Class SimpleResponse
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-11-19
 */
class SimpleResponse
{
    use UsesDataParsingTrait;

    /**
     * @var string|null
     */
    protected ?string $message = null;

    /**
     * @var mixed|null
     */
    protected mixed $data = null;

    /**
     * @var int
     */
    protected int $code = 200;

    /**
     * @var bool
     */
    protected bool $success = true;

    /**
     * @var string|null
     */
    protected ?string $slug = null;

    /**
     * @var array
     */
    protected array $pagination = [];

    /**
     * ExtendedResponse constructor.
     *
     * @param  string|null  $message
     * @param  mixed  $data
     * @param  int  $code
     */
    public function __construct(?string $message = null, mixed $data = null, int $code = 200)
    {
        $this
            ->message($message)
            ->data($data)
            ->code($code);
    }

    /***** HTTP CODE RELATED *****/

    /**
     * Set status code
     *
     * @param  int  $code
     * @return $this
     */
    public function code(int $code): SimpleResponse
    {
        $this->code = $code;

        $this->success = $code < 400;

        return $this;
    }

    /**
     * Generic success code
     *
     * @param  int  $code
     * @return $this
     */
    public function success(int $code = 200): SimpleResponse
    {
        return $this->code($code);
    }

    /**
     * Generic failure code
     *
     * @param  int  $code
     * @return $this
     */
    public function failed(int $code = 400): SimpleResponse
    {
        return $this->code($code);
    }

    /**
     * Lacks authentication method
     * If auth middleware is not activated by default
     *
     * @return $this
     */
    public function unauthorized(int $code = 401): SimpleResponse
    {
        return $this->code($code);
    }

    /**
     * User permission specific errors
     *
     * @return $this
     */
    public function forbidden(int $code = 403): SimpleResponse
    {
        return $this->code($code);
    }

    /**
     * Model search related errors
     *
     * @return $this
     */
    public function notFound(int $code = 404): SimpleResponse
    {
        return $this->code($code);
    }

    /***** HTTP MESSAGE RELATED *****/

    /**
     * Set a custom slug
     *
     * @param  string  $title
     * @param  string[]  $dictionary
     * @return $this
     */
    public function slug(string $title, array $dictionary = []): SimpleResponse
    {
        $title = Str::after($title, '::');

        $default_dictionary = ['@' => 'at', '/' => ' ', '.' => ' '];
        $dictionary = array_merge($default_dictionary, $dictionary);

        $this->slug = Str::slug(title: $title, separator: '_', dictionary: $dictionary);

        return $this;
    }

    /**
     * Set message
     *
     * @param  string|null  $message
     * @param  array  $replace
     * @return $this
     */
    public function message(?string $message, array $replace = []): SimpleResponse
    {
        if ($message) {
            if (! $this->slug) {
                $this->slug(title: $message);
            }

            // Translate the message
            $this->message = __(key: $message, replace: $replace, locale: App::getLocale());
        }

        return $this;
    }

    /**
     * Set data
     *
     * @param  mixed  $value
     * @return $this
     */
    public function data(mixed $value = null): SimpleResponse
    {
        if ($value instanceof ResourceCollection || $value instanceof AbstractPaginator) {
            $pagination = $value instanceof ResourceCollection ?
                $value->response(request())->getData(true) :
                $value->toArray();

            // extract data from pagination
            $data = $pagination['data'];
            unset($pagination['data']);

            // separate them on two different array keys to create uniformity
            $this->pagination = $pagination;
            $this->data = $data;
        } elseif ($value instanceof JsonResource) {
            $this->data = $value->toArray(request());
        } else {
            $this->data = $this->parse($value);
        }

        return $this;
    }

    /**
     * Generate response
     *
     * @return JsonResponse
     */
    public function generate(): JsonResponse
    {
        $data = collect([
            'success' => $this->success,
            'code' => $this->code,
            'locale' => App::getLocale(),
            'slug' => $this->slug,
            'message' => $this->message,
        ]);

        // Add the data or errors based on status code
        if (! empty($this->data)) {
            $data->put($this->success ? 'data' : 'errors', $this->data);
        }

        // Add pagination if not empty
        if (! empty($this->pagination)) {
            $data->put('pagination', $this->pagination);
        }

        return response()->json($data->toArray(), $this->code);
    }
}
