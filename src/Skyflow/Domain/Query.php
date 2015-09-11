<?php

/**
 * Query class for Skyflow addons queries.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

use Skyflow\Domain\AbstractUserOwnedModel;

/**
 * Query class for Skyflow addon queries.
 */
class Query extends AbstractUserOwnedModel
{
    /**
     * The query string.
     *
     * @var string
     */
    private $query;

    /**
     * The name of the addon which sent this query.
     *
     * @var string
     */
    private $addon;

    /**
     * The name of the service which sent this query.
     *
     * @var string
     */
    private $service;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'query';
    }

    /**
     * Get the query string.
     *
     * @return string The query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the query string.
     *
     * @param string $query The query string.
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Set the name of the addon which sent this query.
     *
     * @param string $addon The addon name.
     */
    public function setAddon($addon)
    {
        $this->addon = $name;
    }

    /**
     * Get the name of the addon which sent this query.
     *
     * @return string The addon name.
     */
    public function getAddon()
    {
        return $this->addon;
    }

    /**
     * Set the name of the service which sent this query.
     *
     * @param string $service The service name.
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Get the name of the service which sent this query.
     *
     * @return string The service name.
     */
    public function getService()
    {
        return $this->service;
    }
}
