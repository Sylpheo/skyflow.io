<?php

/**
 * Interface for Web services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

/**
 * Interface for Web services.
 */
interface WebServiceInterface
{
    /**
     * Get the service endpoint.
     *
     * @return string The service endpoint.
     */
    public function getEndpoint();

    /**
     * Get the endpoint extension.
     *
     * endpoint/version/extension
     *
     * @return string The endpoint extension.
     */
    public function getExtension();
}
