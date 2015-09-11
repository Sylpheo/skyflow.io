<?php

/**
 * Service provider for the Skyflow application.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Provider;

use GuzzleHttp\Client as HttpClient;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Skyflow\DAO\EventDAO;
use Skyflow\DAO\FlowDAO;
use Skyflow\DAO\MappingDAO;
use Skyflow\DAO\SkyflowUserDAO;
use Skyflow\Security\AesEncryption;
use Skyflow\Service\GenerateToken;

/**
 * Service provider for the Skyflow application.
 */
class SkyflowServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['skyflow.security.encryption_key'] = 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3';

        $app['skyflow.security.encryption'] = $app->share(function () use ($app) {
            return new AesEncryption($app['skyflow.security.encryption_key']);
        });

        /**
         * Definition of the user.
         *
         * When browsing the Skyflow web interface, the user is automatically authentified
         * "fully" by the Silex SecurityServiceProvider.
         *
         * When handling a request for the api, the user is authentified by its Skyflow-Token.
         *
         * When executing a flow via the command-line interface, the user is authentified
         * by the Skyflow-Token provided as an argument to the command.
         */
        $app['user'] = $app->share(function () use ($app) {
            $user = null;

            if (PHP_SAPI === 'cli') {
                if (defined('CLI_SKYFLOW_TOKEN')) {
                    $user = $app['dao.user']->findByToken(CLI_SKYFLOW_TOKEN);
                }
            } else {
                if (0 === strpos($app['request']->headers->get('Content-Type'), 'application/json')) {
                    // Skyflow JSON API request
                    if ($app['request']->headers->has('Skyflow-Token')) {
                        $token = $app['request']->headers->get('Skyflow-Token');
                        $user = $app['dao.user']->findByToken($token);
                    } else {
                        throw new \Exception('Missing skyflow-token');
                    }
                } else {
                    // Skyflow interface request
                    if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
                        $user = $app['security']->getToken()->getUser();
                    }
                }
            }

            return $user;
        });

        /**
         * Definition of the current executing flow when handling an event.
         *
         * When handling an HTTP request to the flow route, the flow is automatically
         * instantiated from the requested event and saved into $app['flow'].
         */
        $app->before(function (Request $request, Application $app) {
            if ($request->get('_route') === 'flow') {
                $eventName = $request
                    ->attributes
                    ->get('_route_params')['event'];

                if ($eventName !== '') {
                    $userId = $app['user']->getId();
                    $event = $app['dao.event']->findOne($eventName, $userId);

                    if (isset($event)) {
                        $mapping = $app['dao.mapping']->findByEventUser($event->getId(), $userId);

                        if (isset($mapping)) {
                            $flow = $mapping->getFlow();
                            $class = $flow->getClass();

                            $app['flow'] = new $class();
                        }
                    }
                }
            }
        });

        $app['dao.user'] = $app->share(function ($app) {
            $dao = new SkyflowUserDAO($app['db']);
            $dao->setEncryption($app['skyflow.security.encryption']);

            return $dao;
        });

        $app['dao.event'] = $app->share(function ($app) {
            return new EventDAO($app['db']);
        });

        $app['dao.flow'] = $app->share(function ($app) {
            return new FlowDAO($app['db']);
        });

        $app['dao.mapping'] = $app->share(function ($app) {
            $mappingDAO = new MappingDAO($app['db']);
            $mappingDAO->setEventDAO($app['dao.event']);
            $mappingDAO->setFlowDAO($app['dao.flow']);
            return $mappingDAO;
        });

        $app['generatetoken'] = $app->share(function ($app) {
            $generate = new GenerateToken();
            return $generate;
        });

        $app['http.client'] = $app->share(function ($app) {
            return new HttpClient();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        /**
         * Automatic redirection of unauthenticated users to /login.
         */
        $app->before(function (Request $request, Application $app) {
            if (!($app['request']->headers->has('Skyflow-Token'))) {
                if ($app['user'] === null && $request->get('_route') !== 'login') {
                    return new RedirectResponse('/login');
                }
            }
        });
    }
}
