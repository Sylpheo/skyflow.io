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

use skyflow\Controller\OAuthController;

use Salesforce\Authenticator\SalesforceOAuthAuthenticator;
use Salesforce\DAO\SalesforceUserDAO;
use Salesforce\Domain\SalesforceUser;
use Salesforce\Service\SalesforceOAuthService;

use Wave\Controller\WaveOAuthUserController;
use Wave\DAO\WaveRequestDAO;
use Wave\Domain\WaveRequest;
use Wave\Form\Type\WaveCredentialsType;
use Wave\Service\AuthService;
use Wave\Service\WaveService;

/**
 * Service provider for the Wave addon.
 */
class WaveServiceProvider implements ServiceProviderInterface
{
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
            return new WaveHelperController(
                $app['request'],
                $app['wave'],
                $app['wave.form.query']
            );
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
                'users',
                'Wave'
            );
        });

        $app['wave.wave_request.dao'] = $app->share(function () use ($app) {
            return new WaveRequestDAO($app['db']);
        });

        $app['wave.form.type.credentials'] = $app->share(function () use ($app) {
            return new WaveCredentialsType($app['wave.user']);
        });

        $app['wave.form.credentials'] = function () use ($app) {
            return $app['form.factory']->create($app['wave.form.type.credentials']);
        };

        $app['wave.oauth'] = $app->share(function () use ($app) {
            return new SalesforceOAuthService(
                $app['wave.authenticator'],
                $app['wave.user'],
                $app['wave.user.dao']
            );
        });

        $app['wave'] = $app->share(function () use ($app) {
            return new WaveService(
                $app['user'],
                new HttpClient()
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
