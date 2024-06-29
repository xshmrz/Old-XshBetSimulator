<?php

namespace Luchavez\StarterKit\Interfaces;

/**
 * Interface UsesHttpStatusCodeInterface
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
interface HasHttpStatusCodeInterface
{
    /**
     * Determine if the request was successful.
     *
     * @return bool
     */
    public function successful(): bool;

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok(): bool;

    /**
     * Determine if the response was a 401 "Unauthorized" response.
     *
     * @return bool
     */
    public function unauthorized(): bool;

    /**
     * Determine if the response was a 403 "Forbidden" response.
     *
     * @return bool
     */
    public function forbidden(): bool;

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    public function failed(): bool;

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError(): bool;

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError(): bool;
}
