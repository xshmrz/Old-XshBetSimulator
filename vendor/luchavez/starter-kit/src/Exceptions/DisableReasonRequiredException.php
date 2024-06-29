<?php

namespace Luchavez\StarterKit\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DisableReasonRequiredException
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class DisableReasonRequiredException extends Exception
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
            ->message('Disable reason is required.')
            ->failed()
            ->generate();
    }
}
