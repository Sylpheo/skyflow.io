<?php

/**
 * Service provider for the Salesforce addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use skyflow\Controller\OAuthController;

use Salesforce\Authenticator\SalesforceOAuthAuthenticator;
use Salesforce\Controller\SalesforceHelperController;
use Salesforce\Controller\SalesforceOAuthUserController;
use Salesforce\DAO\SalesforceUserDAO;
use Salesforce\Domain\SalesforceUser;
use Salesforce\Form\Type\SalesforceOAuthCredentialsType;
use Salesforce\Service\SalesforceOAuthService;

/**
 * Service provider for the Wave addon.
 */
class SalesforceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // salesforce.authenticator is not shared because it is used
        // by wave.authenticator (must provide another instance).
        $app['salesforce.authenticator'] = function () use ($app) {
            $user = $app['salesforce.user'];

            $sandbox = $user->getIsSandbox();
            $loginUrl = null;
            if ($sandbox) {
                $loginUrl = 'https://test.salesforce.com';
            } else {
                $loginUrl = 'https://login.salesforce.com';
            }

            // "code" parameter is not defined in the array
            // it must be defined later in the AuthController callback action
            $authenticator = new SalesforceOAuthAuthenticator(array(
                'login_url'     => $loginUrl,
                'response_type' => 'code',
                'grant_type'    => 'code',
                'client_id'     => $user->getClientId(),
                'client_secret' => $user->getClientSecret(),
                'redirect_uri'  => 'https://' . $_SERVER['HTTP_HOST'] . '/salesforce/auth/callback',
                'code'          => null,
                'instance_url'  => $user->getInstanceUrl(),
                'refresh_token' => $user->getRefreshToken()
            ));

            $authenticator->setHttpClient($app['http.client']);

            return $authenticator;
        };

        $app['salesforce.controller.helper'] = $app->share(function () use ($app) {
            return new SalesforceHelperController(
                $app['request'],
                $app['salesforce'],
                $app['salesforce.form.query']
            );
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
            return new SalesforceUserDAO($app['db'], 'users');
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

        $app['salesforce.oauth'] = $app->share(function () use ($app) {
            return new SalesforceOAuthService(
                $app['salesforce.authenticator'],
                $app['salesforce.user'],
                $app['salesforce.user.dao']
            );
        });

        $app['salesforce'] = $app->share(function () use ($app) {
            return new SalesforceFacade(array(
                'oauth' => $app['salesforce.oauth']
            ));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
