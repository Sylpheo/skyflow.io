<?php

/**
 * Abstract service class for the Skyflow addon services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use skyflow\Facade;
use skyflow\Service\ServiceInterface;

/**
 * Abstract service class for the Skyflow addon services.
 *
 * This class is abstract because it has no service methods. Child classes must
 * define the service methods.
 */
abstract class AbstractService extends Facade implements ServiceInterface
{
    /**
     * The parent service.
     *
     * @var ServiceInterface
     */
    private $parentService;

    /**
     * The service provider name.
     *
     * @var string
     */
    private $provider;

    /**
     * The service endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The service version.
     *
     * @var string
     */
    private $version;

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
     * Use the parent Facade constructor.
     *
     * @param ServiceInterface $parentService The parent service.
     * @param array            $config        The service configuration: provider,
     *                                        endpoint, version, extension.
     */
    public function __construct(
        $parentService,
        $config
    ) {
        parent::__construct();

        if (isset($parentService)) {
            $this->setParentService($parentService);
        }

        if (isset($config['provider'])) {
            $this->setProvider($config['provider']);
        } elseif ($this->getParentService() !== null) {
            $this->setProvider($this->getParentService()->getProvider());
        } else {
            $this->setProvider(null);
        }

        if (isset($config['endpoint'])) {
            $this->setEndpoint($config['endpoint']);
        } elseif ($this->getParentService() !== null) {
            $this->setEndpoint($this->getParentService()->getEndpoint());
        } else {
            throw new \Exception('Service endpoint required');
        }

        if (isset($config['version'])) {
            $this->setVersion($config['version']);
        } elseif ($this->getParentService() !== null) {
            $this->setVersion($this->getParentService()->getVersion());
        } else {
            $this->setVersion(null);
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
     * Set the parent service.
     *
     * @param ServiceInterface $parentService The parent service.
     */
    protected function setParentService(ServiceInterface $parentService)
    {
        $this->parentService = $parentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentService()
    {
        return $this->parentService;
    }

    /**
     * Set the name of the service provider.
     *
     * May it be "Salesforce", "Wave", "Office360"...
     *
     * @param string $provider The name of the service provider.
     */
    protected function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the name of the service provider.
     *
     * @return string The name of the service provider.
     */
    public function getProvider()
    {
        return $this->provider;
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
     * Set the service version.
     *
     * @param string $version The service version.
     */
    protected function setVersion($version)
    {
        $this->version = ltrim(rtrim($version, '/'), '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
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
