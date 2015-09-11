<?php

/**
 * Service interface for the Skyflow addon services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Service;

use Skyflow\FacadeInterface;

/**
 * Service interface for the Skyflow addon services.
 */
interface ServiceInterface extends FacadeInterface
{
    /**
     * Get the parent service.
     *
     * @return ServiceInterface The parent service.
     */
    public function getParentService();

    /**
     * Get the name of the service provider.
     *
     * @return string The name of the service provider.
     */
    public function getProvider();

    /**
     * Get the service version.
     *
     * @return string The service version.
     */
    public function getVersion();
}
