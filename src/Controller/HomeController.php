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
            $token = uniqid();
            $user->setPassword($password); 
            $user->setSkyflowtoken($token);
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
        }
        return $app['twig']->render('users-form.html.twig', array(
            'title' => 'New user',
            'userForm' => $userForm->createView()));
    }

    public function setCredentialsETAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $idUser = $app['security']->getToken()->getUser()->getId();

            $user = $app['security']->getToken()->getUser();
           // var_dump($user);

            $form = $app['form.factory']->createBuilder('form')
                ->add('clientid','text')
                ->add('clientsecret','text')
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                $user->setclientid($data['clientid']);
                $user->setClientsecret($data['clientsecret']);
                var_dump($user);
                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');

            }
                return $app['twig']->render('et-credentials-form.html.twig',
                    array('etForm' => $form->createView()));


        }
    }

}