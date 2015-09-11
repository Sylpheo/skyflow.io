<?php

/**
 * Abstract web service class for the Skyflow addon web services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Service;

use Skyflow\Service\AbstractService;
use Skyflow\Service\ServiceInterface;

/**
 * Abstract web service class for the Skyflow addon web services.
 *
 * This class is abstract because it has no service methods. Child classes must
 * define the service methods. Furthermore we don't know if it is a REST or SOAP
 * service.
 */
abstract class AbstractWebService extends AbstractService
{
    /**
     * The service endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The endpoint extension.
     *
     * endpoint/version/extension
     *
     * @var string
     */
    private $extension;

    /**
     * Service constructor.
     *
     * @param ServiceInterface $parentService The parent service.
     * @param array            $config        The service configuration: endpoint,
     *                                        extension.
     */
    public function __construct(
        $parentService,
        $config
    ) {
        parent::__construct($parentService, $config);

        if (isset($config['endpoint'])) {
            $this->setEndpoint($config['endpoint']);
        } elseif ($this->getParentService() !== null) {
            $this->setEndpoint($this->getParentService()->getEndpoint());
        } else {
            throw new \Exception('Service endpoint required');
        }

        $extension = '';
        if (isset($config['extension'])) {
            $extension = $config['extension'];
            if ($this->getParentService() !== null) {
                $extension = $this->getParentService()->getExtension() . $extension;
            }
        } elseif ($this->getParentService() !== null) {
            $extension = $this->getParentService()->getExtension();
        }
        $this->setExtension($extension);
    }

    /**
     * Set the service endpoint.
     *
     * @param string $endpoint The service endpoint.
     */
    protected function setEndpoint($endpoint)
    {
        $this->endpoint = rtrim($endpoint, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }


    /**
     * Set the endpoint extension.
     *
     * endpoint/version/extension
     *
     * @param string $extension The endpoint extension.
     */
    protected function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
