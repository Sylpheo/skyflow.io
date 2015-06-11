<?php

namespace exactSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use exactSilex\Domain\Users;
use exactSilex\Form\Type\UsersType;


class HomeController {

    /**
     * Home page controller.
     *
     *
     */
    public function indexAction(Application $app) {
        echo realpath('salesforceStrategy.php');
     return $app['twig']->render('index.html.twig');
    
    }

    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
    }

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
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
        }
        return $app['twig']->render('users-form.html.twig', array(
            'title' => 'New user',
            'userForm' => $userForm->createView()));
    }

}