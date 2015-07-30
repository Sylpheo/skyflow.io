<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Users;
use skyflow\Form\Type\UsersType;


class HomeController {

    /**
     * Home page
     */
    public function indexAction(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app['twig']->render('index.html.twig');
        }else{
            return $app->redirect('/login');
        }

    
    }

    /**
     * Login
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
    }

    /**
     * Add user
     * @param Request $request
     * @param Application $app
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
            'userForm' => $userForm->createView()));
    }

    /**
     * Set ExactTarget credentials
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
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
                $user->setClientid($data['clientid']);
                $user->setClientsecret($data['clientsecret']);
                //var_dump($user);
                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');

            }
                return $app['twig']->render('et-credentials-form.html.twig',
                    array('etForm' => $form->createView()));
        }else{
            return $app->redirect('/login');
        }
    }

    /**
     * Set Wave credentials
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function setCredentialsWaveAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            $user = $app['security']->getToken()->getUser();

             $form = $app['form.factory']->createBuilder('form')
                ->add('waveid','text')
                ->add('wavesecret','text')
                ->add('wavelogin','text')
                ->add('wavepassword','password')
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                $user->setWaveid($data['waveid']);
                $user->setWavesecret($data['wavesecret']);
                $user->setWavelogin($data['wavelogin']);
                $user->setWavepassword($data['wavepassword']);
                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');

            }
                return $app['twig']->render('wave-credentials-form.html.twig',
                    array('waveForm' => $form->createView()));
        }else{
            return $app->redirect('/login');
        }

    }

    public function gestionToken(Application $app)
    {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();


        }

        return $app['twig']->render('gestionToken.html.twig',array('user' => $user));
    }

    public function regenerateToken(Application $app)
    {
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