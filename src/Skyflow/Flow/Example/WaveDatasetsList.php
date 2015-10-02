<?php

/**
 * Example flow that lists the Wave datasets.
 *
 * This flow demonstrates usage of the datasets() method from the wave.data
 * service to list the available Wave datasets.
 *
 * Do NOT forget to setup the client id and client secret for the Wave addon or
 * this flow won't work !
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow\Example;

use Skyflow\Flow\AbstractFlow;

class WaveDatasetsList extends AbstractFlow
{
    /**
     * {@inheritdoc}
     */
    public function event($requestJson)
    {
        return $this->run();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $datasets = $this->get('wave.data')->datasets();
        return $datasets;
    }
}
