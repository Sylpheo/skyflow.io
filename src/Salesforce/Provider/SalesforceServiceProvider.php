<?php

/**
 * Service provider for the Salesforce addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Controller\OAuthController;
use skyflow\Facade;

use Salesforce\Authenticator\SalesforceOAuthAuthenticator;
use Salesforce\Controller\SalesforceHelperController;
use Salesforce\Controller\SalesforceOAuthUserController;
use Salesforce\DAO\SalesforceUserDAO;
use Salesforce\Domain\SalesforceUser;
use Salesforce\Form\Type\SalesforceOAuthCredentialsType;
use Salesforce\Form\Type\SalesforceSoqlQueryType;
use Salesforce\Service\Data\SalesforceSObjectsService;
use Salesforce\Service\SalesforceDataService;
use Salesforce\Service\SalesforceOAuthService;

/**
 * Service provider for the Salesforce addon.
 */
class SalesforceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['salesforce.authenticator'] = $app->share(function () use ($app) {
            $user = $app['salesforce.user'];

            $sandbox = $user->getIsSandbox();
            $loginUrl = null;
            if ($sandbox) {
                $loginUrl = 'https://test.salesforce.com';
            } else {
                $loginUrl = 'https://login.salesforce.com';
            }

            $server = $_SERVER['SERVER_NAME'];
            $host = $_SERVER['HTTP_HOST'];

            // "code" parameter is not defined in the array
            // it must be defined later in the AuthController callback action
            $authenticator = new SalesforceOAuthAuthenticator(array(
                'login_url'     => $loginUrl,
                'response_type' => 'code',
                'grant_type'    => 'code',
                'client_id'     => $user->getClientId(),
                'client_secret' => $user->getClientSecret(),
                'redirect_uri'  => ($server === 'localhost' ? 'http' : 'https')
                    . '://' . $host . '/salesforce/auth/callback',
                'code'          => null,
                'instance_url'  => $user->getInstanceUrl(),
                'refresh_token' => $user->getRefreshToken()
            ));

            $authenticator->setHttpClient($app['http.client']);

            return $authenticator;
        });

        $app['salesforce.controller.helper'] = $app->share(function () use ($app) {
            $controller = new SalesforceHelperController(
                $app['request'],
                $app['salesforce'],
                $app['salesforce.form.query']
            );
            $controller->setTwig($app['twig']);

            return $controller;
        });

        $app['salesforce.controller.oauth'] = $app->share(function () use ($app) {
            return new OAuthController(
                $app['request'],
                $app['salesforce.oauth'],
                '/salesforce/auth'
            );
        });

        $app['salesforce.controller.user'] = $app->share(function () use ($app) {
            $controller = new SalesforceOAuthUserController(
                $app['request'],
                $app['salesforce.oauth'],
                $app['salesforce.user'],
                $app['salesforce.user.dao'],
                $app['salesforce.form.credentials']
            );
            $controller->setTwig($app['twig']);

            return $controller;
        });

        $app['salesforce.user.dao'] = $app->share(function () use ($app) {
            $dao = new SalesforceUserDAO($app['db'], 'users');
            $dao->setEncryption($app['skyflow.security.encryption']);

            return $dao;
        });

        $app['salesforce.user'] = $app->share(function () use ($app) {
            return $app['salesforce.user.dao']->findOneById($app['user']->getId());
        });

        $app['salesforce.form.type.credentials'] = $app->share(function () use ($app) {
            return new SalesforceOAuthCredentialsType($app['salesforce.user']);
        });

        $app['salesforce.form.credentials'] = function () use ($app) {
            return $app['form.factory']->create($app['salesforce.form.type.credentials']);
        };

        $app['salesforce.form.type.query'] = $app->share(function () use ($app) {
            return new SalesforceSoqlQueryType();
        });

        $app['salesforce.form.query'] = function () use ($app) {
            return $app['form.factory']->create($app['salesforce.form.type.query']);
        };

        $app['salesforce.data'] = $app->share(function () use ($app) {
            $instanceUrl = $app['salesforce.user']->getInstanceUrl();

            return new SalesforceDataService(
                null,
                array(
                    'provider' => 'Salesforce',
                    'endpoint' => $instanceUrl . '/services/data',
                    'version' => 'v20.0'
                ),
                $app['http.client'],
                $app['salesforce.user'],
                $app['salesforce.oauth']
            );

            return $service;
        });

        $app['salesforce.data.sobjects'] = $app->share(function () use ($app) {
            return new SalesforceSObjectsService(
                $app['salesforce.data'],
                array(
                    'extension' => '/sobjects',
                ),
                $app['http.client'],
                $app['salesforce.user'],
                $app['salesforce.oauth']
            );
        });

        $app['salesforce.oauth'] = $app->share(function () use ($app) {
            /**
             * @todo Remove the authenticator and do the authentication inside
             *       the service.
             */
            $instanceUrl = $app['salesforce.user']->getInstanceUrl();

            // The endpoint is not used at all.
            return new SalesforceOAuthService(
                null,
                array(
                    'provider' => 'Salesforce',
                    'endpoint' => $instanceUrl . '/services/oauth2',
                ),
                $app['salesforce.authenticator'],
                $app['salesforce.user'],
                $app['salesforce.user.dao']
            );
        });

        /**
         * Chaining Salesforce services.
         *
         * Chaining services cannot be done on service declaration or we fall
         * into an unresolvable circular dependency scenario e.g.:
         * declaring $app['salesforce.data'] we add it the service
         * $app['salesforce.data.sobjects'] which needs $app['salesforce.data']
         * which we are currently defining ==> circular dependency scenario.
         *
         * We must take care of the Skyflow user $app['user'] because we need
         * a valid Salesforce user in order to get the Salesforce instance url
         * needed on Salesforce services declaration.
         *
         * Plus the Skyflow user is only available right before the request
         * is processed by the controllers. That's why we have to put this
         * service chaining in a $app->before() hook.
         */
        $app->before(function (Request $request, Application $app) {
            if ($app['user'] !== null) {
                $app['salesforce.data']->addService('sobjects', $app['salesforce.data.sobjects']);
            }
        });

        $app['salesforce'] = $app->share(function () use ($app) {
            /**
             * @todo Remove the notion of Facade and only use services.
             */
            return new Facade(array(
                'data' => $app['salesforce.data'],
                'oauth' => $app['salesforce.oauth']
            ));
        });
    }

    /**
     * {@inheritdoc}
     *
     * Add the Salesforce facade to the current executing flow if there is one.
     */
    public function boot(Application $app)
    {
        $app->before(function (Request $request, Application $app) {
            if (isset($app['flow'])) {
                $app['flow']->addFacade('Salesforce', $app['salesforce']);
            }
        });
    }
}
