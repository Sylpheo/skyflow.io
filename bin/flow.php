<?php

/**
 * Execute a flow from the command-line.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\bin;

/**
 * Invokable class to execute a flow from the command-line.
 */
class Flow
{
    /**
     * Show the usage of the flow command.
     */
    public function showUsage()
    {
        echo "ERROR: missing skyflow-token and flow !\n\n";
        echo "Usage:\n";
        echo "php bin/flow.php --skyflow-token 'your_skyflow_token' --flow 'your_flow_name'\n";
    }

    public function __invoke()
    {
        $options = getopt('', array('skyflow-token:', 'flow:'));

        // check the required options
        if (!isset($options['skyflow-token']) && !isset($options['flow'])) {
            $this->showUsage();
            exit(1);
        }

        define('CLI_SKYFLOW_TOKEN', $options['skyflow-token']);
        define('CLI_FLOW_NAME', $options['flow']);

        // run the application
        include_once(__DIR__ . '/../web/index.php');
    }
}

$flow = new Flow();
$flow();
