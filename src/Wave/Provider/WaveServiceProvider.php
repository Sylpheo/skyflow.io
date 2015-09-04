<?php

/**
 * Service provider for the Wave addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Provider;

use GuzzleHttp\Client as HttpClient;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Controller\OAuthController;
use skyflow\Facade;

use Salesforce\Authenticator\SalesforceOAuthAuthenticator;
use Salesforce\DAO\SalesforceUserDAO;
use Salesforce\Domain\SalesforceUser;
use Salesforce\Form\Type\SalesforceOAuthCredentialsType;
use Salesforce\Form\Type\SalesforceSoqlQueryType;
use Salesforce\Service\SalesforceOAuthService;

use Wave\Controller\WaveHelperController;
use Wave\Controller\WaveOAuthUserController;
use Wave\DAO\WaveRequestDAO;
use Wave\Domain\WaveRequest;
use Wave\Service\WaveDataService;

/**
 * Service provider for the Wave addon.
 */
class WaveServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['wave.authenticator'] = $app->share(function () use ($app) {
            $user = $app['wave.user'];

            $sandbox = $user->getIsSandbox();
            $loginUrl = null;
            if ($sandbox) {
                $loginUrl = 'https://test.salesforce.com';
            } else {
                $loginUrl = 'https://login.salesforce.com';
            }

            $server = $_SERVER['SERVER_NAME'];
            $host = $_SERVER['HTTP_HOST'];

            // code must be defined later in the AuthController callback action
            $authenticator = new SalesforceOAuthAuthenticator(array(
                'login_url'     => $loginUrl,
                'response_type' => 'code',
                'grant_type'    => 'code',
                'client_id'     => $user->getClientId(),
                'client_secret' => $user->getClientSecret(),
                'redirect_uri'  => ($server === 'localhost' ? 'http' : 'https')
                    . '://' . $host . '/wave/auth/callback',
                'code'          => null,
                'instance_url'  => $user->getInstanceUrl(),
                'refresh_token' => $user->getRefreshToken()
            ));
            $authenticator->setHttpClient($app['http.client']);

            return $authenticator;
        });

        $app['wave.controller.helper'] = $app->share(function () use ($app) {
            $controller = new WaveHelperController(
                $app['request'],
                $app['wave'],
                $app['wave.form.query']
            );
            $controller->setTwig($app['twig']);

            return $controller;
        });

        $app['wave.controller.oauth'] = $app->share(function () use ($app) {
            return new OAuthController(
                $app['request'],
                $app['wave.oauth'],
                '/wave/auth'
            );
        });

        $app['wave.controller.user'] = $app->share(function () use ($app) {
            $controller = new WaveOAuthUserController(
                $app['request'],
                $app['wave.oauth'],
                $app['wave.user'],
                $app['wave.user.dao'],
                $app['wave.form.credentials']
            );
            $controller->setTwig($app['twig']);

            return $controller;
        });

        $app['wave.user'] = $app->share(function () use ($app) {
            return $app['wave.user.dao']->findById($app['user']->getId());
        });

        $app['wave.user.dao'] = $app->share(function () use ($app) {
            return new SalesforceUserDAO(
                $app['db'],
                $app,
                'users',
                'Salesforce\\Domain\\SalesforceUser',
                'Wave'
            );
        });

        $app['wave.wave_request.dao'] = $app->share(function () use ($app) {
            return new WaveRequestDAO($app['db']);
        });

        $app['wave.form.type.credentials'] = $app->share(function () use ($app) {
            $type = new SalesforceOAuthCredentialsType($app['wave.user']);
            $type->setName('wave_credentials');

            return $type;
        });

        $app['wave.form.credentials'] = function () use ($app) {
            return $app['form.factory']->create($app['wave.form.type.credentials']);
        };

        $app['wave.form.type.query'] = $app->share(function () use ($app) {
            $type = new SalesforceSoqlQueryType($app['wave.user']);
            $type->setName('wave_saql');

            return $type;
        });

        $app['wave.form.query'] = function () use ($app) {
            return $app['form.factory']->create($app['wave.form.type.query']);
        };

        $app['wave.oauth'] = $app->share(function () use ($app) {
            /**
             * @todo Remove the authenticator and do the authentication inside
             *       the service.
             */
            $instanceUrl = $app['wave.user']->getInstanceUrl();

            // The endpoint is not used at all.
            return new SalesforceOAuthService(
                null,
                array(
                    'provider' => 'Wave',
                    'endpoint' => $instanceUrl . '/services/oauth2',
                ),
                $app['wave.authenticator'],
                $app['wave.user'],
                $app['wave.user.dao']
            );
        });

        $app['wave.data'] = $app->share(function () use ($app) {
            $instanceUrl = $app['wave.user']->getInstanceUrl();

            return new WaveDataService(
                null,
                array(
                    'provider' => 'Wave',
                    'endpoint' => $instanceUrl . '/services/data/v34.0/wave'
                ),
                $app['http.client'],
                $app['wave.user'],
                $app['wave.oauth']
            );
        });

        $app['wave'] = $app->share(function () use ($app) {
            return new Facade(array(
                'oauth' => $app['wave.oauth'],
                'data' => $app['wave.data']
            ));
        });
    }

    /**
     * {@inheritdoc}
     *
     * Add the Wave facade to the current executing flow if there is one.
     */
    public function boot(Application $app)
    {
        $app->before(function (Request $request, Application $app) {
            if (isset($app['flow'])) {
                $app['flow']->addFacade('Wave', $app['wave']);
            }
        });
    }
}
