<?php

/**
 * Service provider for the Wave addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Wave\Authenticator\WaveAuthenticator;
use Wave\Controller\AuthController;
use Wave\Controller\HelperController;
use Wave\Controller\WaveController;
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
        $app['wave.authenticator'] = $app->share(function() use ($app) {
            $user = $app['user'];

            $sandbox = $user->getWaveSandbox();
            $loginUrl = null;
            if ($sandbox) {
                $loginUrl = 'https://test.salesforce.com';
            } else {
                $loginUrl = 'https://login.salesforce.com';
            }

            // code must be defined later in the AuthController callback action
            return new WaveAuthenticator(array(
                'login_url'     => $loginUrl,
                'response_type' => 'code',
                'grant_type'    => 'code',
                'client_id'     => $user->getWaveClientId(),
                'client_secret' => $user->getWaveClientSecret(),
                'redirect_uri'  => 'https://' . $_SERVER['HTTP_HOST'] . '/wave/auth/callback',
                'code'          => null,
                'instance_url'  => $user->getWaveInstanceUrl(),
                'refresh_token' => $user->getWaveRefreshToken()
            ));
        });

        $app['wave.controller.auth'] = $app->share(function () use ($app) {
            return new AuthController(
                $app['request'],
                $app['wave.auth'],
                $app['wave'],
                $app['user'],
                $app['dao.user'],
                $app['wave.form.credentials'],
                $app['twig']
            );
        });

        $app['wave.controller.helper'] = $app->share(function () use ($app) {
            return new HelperController(
                $app['request'],
                $app['wave'],
                $app['user'],
                $app['dao.wave_request'],
                $app['form.factory'],
                $app['twig']
            );
        });

        $app['dao.wave_request'] = $app->share(function () use ($app) {
            return new WaveRequestDAO($app['db']);
        });

        $app['wave.form.type.credentials'] = $app->share(function () use ($app) {
            return new WaveCredentialsType($app['user']);
        });

        $app['wave.form.credentials'] = function () use ($app) {
            return $app['form.factory']->create($app['wave.form.type.credentials']);
        };

        $app['wave.auth'] = $app->share(function () use ($app) {
            return new AuthService(
                $app['wave.authenticator'],
                $app['user'],
                $app['dao.user']
            );
        });

        $app['wave'] = $app->share(function () use ($app) {
            return new WaveService(
                $app['user'],
                new \GuzzleHttp\Client()
            );
        });
    }

    public function boot(Application $app)
    {
    }
}