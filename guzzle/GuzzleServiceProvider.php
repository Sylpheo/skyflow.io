
Skip to content
This repository

    Explore
    Gist
    Blog
    Help

    @elodie05 elodie05

3
32

    11

Guzzle3/guzzle-silex-extension

guzzle-silex-extension/GuzzleServiceProvider.php
@logocomune logocomune on 18 Nov 2014 Update GuzzleServiceProvider.php

6 contributors
@igorw
@mtdowling
@jjungnickel
@logocomune
@lightglitch
@darklow
68 lines (58 sloc) 2.022 kB
<?php
namespace Guzzle;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Guzzle\Service\Builder\ServiceBuilder;
use Guzzle\Service\Client;
/**
 * Guzzle service provider for Silex
 *
 * = Parameters:
 *  guzzle.services: (optional) array|string|SimpleXMLElement Data describing
 *      your web service clients.  You can pass the path to a file
 *      (.xml|.js|.json), an array of data, or an instantiated SimpleXMLElement
 *      containing configuration data.  See the Guzzle docs for more info.
 *  guzzle.plugins: (optional) An array of guzzle plugins to register with the
 *      client.
 *
 * = Services:
 *   guzzle: An instantiated Guzzle ServiceBuilder.
 *   guzzle.client: A default Guzzle web service client using a dumb base URL.
 *
 * @author Michael Dowling <michael@guzzlephp.org>
 */
class GuzzleServiceProvider implements ServiceProviderInterface
{
    /**
     * Register Guzzle with Silex
     *
     * @param Application $app Application to register with
     */
    public function register(Application $app)
    {
        $app['guzzle.base_url'] = '/';
        if(!isset($app['guzzle.plugins'])){
            $app['guzzle.plugins'] = array();
        }
        // Register a Guzzle ServiceBuilder
        $app['guzzle'] = $app->share(function () use ($app) {
            if (!isset($app['guzzle.services'])) {
                $builder = new ServiceBuilder(array());
            } else {
                $builder = ServiceBuilder::factory($app['guzzle.services']);
            }
            return $builder;
        });
        // Register a simple Guzzle Client object (requires absolute URLs when guzzle.base_url is unset)
        $app['guzzle.client'] = $app->share(function() use ($app) {
            $client = new Client($app['guzzle.base_url']);
            foreach ($app['guzzle.plugins'] as $plugin) {
                $client->addSubscriber($plugin);
            }
            return $client;
        });
    }
    public function boot(Application $app)
    {
    }
}

    Status API Training Shop Blog About 

    © 2015 GitHub, Inc. Terms Privacy Security Contact 

