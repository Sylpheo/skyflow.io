<?php

namespace Skyflow\Flows;

/**
 * Flow abstract class.
 *
 * Extend this class to create custom flow.
 * Put your custom flow in directory "skyflow.io/src/Skyflow/Flows".
 *
 * This class cannot be instanciated.
 */
abstract class AbstractFlow {

    /**
     * Instance of Silex Application.
     *
     * @var Silex/Application
     */
    public $app;

    /**
     * Flow class contructor.
     *
     * @param Silex/Application Instance of Silex Application.
     */
    public function __construct($app){
        $this->app = $app;
    }

    /**
     * Flow execution when run via an event (HTTP POST request with JSON content).
     *
     * @param Symfony\Component\HttpFoundation\Request The JSON request handled by the application.
     * @return ??? (TODO)
     */
    public abstract function event($requestJson);
}