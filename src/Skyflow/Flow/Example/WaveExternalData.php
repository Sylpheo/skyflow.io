<?php

/**
 * Example flow for Wave External Data
 *
 * This flow demonstrates import of an External Data in Wave (aka Salesforce
 * Analytics Cloud) using the wave.externaldata service.
 *
 * Do NOT forget to setup the client id and client secret for the Wave addon or
 * this flow won't work !
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow\Example;

use Skyflow\Flow\AbstractFlow;

class WaveExternalData extends AbstractFlow
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
        $data = array(
            array(
                'FirstName' => 'John',
                'LastName' => 'Doe',
                'Email' => 'john.doe@gmail.com'
            ),
            array(
                'FirstName' => 'Jane',
                'LastName' => 'Doe',
                'Email' => 'jane.doe@gmail.com'
            )
        );

        // The dataset "Skyflow Example TV Series Characters" is reused in
        // Skyflow\Flow\Example\WaveSimpleSaqlQuery

        $dataset = $this->get('wave.externaldata')->create(
            'Skyflow Example TV Series Characters',
            array(
                'edgemartAlias' => 'Skyflow_Example_TV_Series_Characters',
                'format' => 'Csv',
                'operation' => 'Overwrite',
                'notificationSent' => 'Warnings',
                'notificationEmail' => 'your.email@gmail.com',
                'description' => 'Some TV Series Characters'
            )
        );

        // Append the headers
        $dataset->appendCsvLine(array_keys($data[0]));

        // Append the values
        $dataset->appendCsvLines(array_values($data));

        // Send data to Wave
        $dataset->process();

        return $data;
    }
}
