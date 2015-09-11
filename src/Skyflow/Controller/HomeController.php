<?php

/**
 * Controller for Skyflow Home actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Skyflow\Domain\Users;
use Skyflow\Form\Type\UsersType;

/**
 * Controller for Skyflow Home actions.
 */
class HomeController {

    /**
     * Home page.
     *
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function indexAction(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app['twig']->render('index.html.twig');
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Login.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return mixed
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    /**
     * Add user.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return mixed
     */
    public function addUserAction(Request $request, Application $app) {
        $user = new Users();
        $userForm = $app['form.factory']->create(new UsersType(), $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder
            $encoder = $app['security.encoder.digest'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            //$token = uniqid();
            $token = $app['generatetoken']->generateToken();
            $user->setPassword($password);
            $user->setSkyflowtoken($token);
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
            $app['session']->getFlashBag()->add('skyflow-token', $token);
        }

        return $app['twig']->render('users-form.html.twig', array(
            'title' => 'New user',
            'userForm' => $userForm->createView()
        ));
    }

    /**
     * Token management.
     *
     * @param Application $app The Silex Application.
     * @return string The token management rendered template.
     */
    public function gestionToken(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
        }

        return $app['twig']->render('gestionToken.html.twig',array('user' => $user));
    }

    /**
     * Regenerate a new token.
     *
     * @param Application $app The Silex Application.
     * @return string The token management rendered template.
     */
    public function regenerateToken(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $token = $app['generatetoken']->generateToken();
            $user->setSkyflowToken($token);
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The Skyflow-Token was succesfully updated.');
        }

        return $app['twig']->render('generateToken.html.twig',array('user' => $user));
    }
}