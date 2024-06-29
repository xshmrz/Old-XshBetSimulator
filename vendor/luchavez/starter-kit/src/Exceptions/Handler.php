<?php

namespace Luchavez\StarterKit\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-11-09
 *
 * Extended the render() and unauthenticated() errors to output a unified format based on ExtendedResponse
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param  Request  $request
     * @param  Throwable  $e
     * @return Response|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\JsonResponse|Response
    {
        if ($closure = starterKit()->getExceptionRenders($e)) {
            return $closure($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * @param  Request  $request
     * @param  AuthenticationException  $exception
     * @return JsonResponse
     */
    public function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return simpleResponse()
            ->message('You do not have a valid authentication token.')
            ->slug('missing bearer token')
            ->failed(401)
            ->generate();
    }

    /**
     * @param $request
     * @param  ValidationException  $exception
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return simpleResponse()
            ->message($exception->getMessage())
            ->data($exception->errors())
            ->failed($exception->status)
            ->generate();
    }
}
