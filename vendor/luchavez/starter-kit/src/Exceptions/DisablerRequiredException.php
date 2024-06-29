<?php

namespace Luchavez\StarterKit\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DisablerRequiredException
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class DisablerRequiredException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return simpleResponse()
            ->message('Disabler is required.')
            ->failed()
            ->generate();
    }
}
