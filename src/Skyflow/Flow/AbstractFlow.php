<?php

/**
 * Flow abstract class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Flow;

use Skyflow\Flow\FlowInterface;

/**
 * Flow abstract class.
 *
 * Extend this class to create a custom flow.
 * Put your custom flow in directory "skyflow.io/src/Skyflow/Flow".
 *
 * This class cannot be instanciated.
 */
abstract class AbstractFlow implements FlowInterface
{
    /**
     * Instance of Silex Application.
     *
     * @var Silex/Application
     */
    public $app;

    protected function getSaleforce()
    {
        return $app['salesforce'];
    }

    protected function getWave()
    {
        return $app['wave'];
    }

    /**
     * Flow class contructor.
     *
     * @param Silex/Application Instance of Silex Application.
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function event($requestJson);

    /**
     * {@inheritdoc}
     */
    abstract public function run();
}
