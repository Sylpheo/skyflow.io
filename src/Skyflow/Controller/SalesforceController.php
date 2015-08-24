<?php
namespace Skyflow\Controller;

use Silex\Application;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\EntityBody;
use Skyflow\Domain\Wave_request;



class SalesforceController
{
    public function salesforceAction(Application $app){
        /***
         * Si l'utilisateur s'est déjà authentifié sur salesforce -> redirigé vers la page de helper
         * sinon login() et redirection sur callback
         */
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $user_access_token = $user->getAccessTokenSalesforce();
            $user_refresh_token = $user->getRefreshTokenSalesforce();
            $user_instance_url = $user->getInstanceUrlSalesforce();

            if ($user_access_token == null || $user_refresh_token == null || $user_instance_url == null) {
               $login = $app['salesforce']->login($app);
                return $login;
            }else{
                return $app->redirect("/query");
            }
        }else{
            return $app->redirect('/login');
        }
    }

    public function callbackAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $code = $_GET['code'];
            /**
             * Get access_token, refresh_token & instance_url from code
             */
            $response = $app['salesforce']->callback($app,$code);
            $refresh_token = $response->refresh_token;
            $access_token = $response->access_token;
            $instance_url = $response->instance_url;

            /**
             * Update user
             */
            $user->setAccessTokenSalesforce($access_token);
            $user->setRefreshTokenSalesforce($refresh_token);
            $user->setInstanceUrlSalesforce($instance_url);
            $app['dao.user']->save($user);

            /**
             * Form to send request
             */
            $form = $app['form.factory']->createBuilder('form')
                ->add('Request','textarea',array(
                    'attr' => array('cols' => '100', 'rows' => '3'),
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $array = $form->getData();
                $query = $array['Request'];

                /**
                 * Send request
                 */
                $data = $app['salesforce']->request($app,$query);

                return $app['twig']->render('results.html.twig',
                    array(
                        'results' => $data,
                    ));
            }
            return $app['twig']->render('salesforce-apihelper.html.twig',
                array(
                    'requestForm' => $form->createView(),
                ));
        }else{
            return $app->redirect('/login');
        }
    }

    public function queryAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

            $form = $app['form.factory']->createBuilder('form')
                ->add('Request','textarea',array(
                    'attr' => array('cols' => '100', 'rows' => '3'),
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $array = $form->getData();
                $query = $array['Request'];

                $user = $app['security']->getToken()->getUser();
                $access_token = $user->getAccessTokenSalesforce();
                $instance_url = $user->getInstanceUrlSalesforce();

                $client = new Client();
                try {
                    $salesforceRequest = $client->createRequest(
                        'GET',
                        $instance_url . "/services/data/v20.0/query?q=" . urlencode($query)
                    );

                    $salesforceRequest->setHeader('Authorization', 'OAuth ' . $access_token);
                    $response = $client->send($salesforceRequest);
                    $statuscode = $response->getStatusCode();
                } catch (\Exception $e) {
                    $statuscode= $e->getCode();
                }

                if($statuscode == '401') {
                    /**
                     * Get new access_token
                     */
                    $respRefresh = $app['salesforce']->refreshToken($app);
                    $access_token = $respRefresh->access_token;
                    $user->setAccessTokenSalesforce($access_token);
                    $app['dao.user']->save($user);
                }

                /**
                 * Resend request
                 */
                $salesforceRequest->setHeader('Authorization', 'OAuth ' . $access_token);
                $response = $client->send($salesforceRequest);
                $data = $response->json();
                $data = json_encode($data);


                return $app['twig']->render('results.html.twig',
                    array(
                        'results' => $data,
                    ));
            }
            return $app['twig']->render('salesforce-apihelper.html.twig',
                array(
                    'requestForm' => $form->createView(),
                ));

       }else{
            return $app->redirect('/login');
        }
    }

    public function setCredentialsSalesforceAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

            $user = $app['security']->getToken()->getUser();

            $form = $app['form.factory']->createBuilder('form')
                ->add('salesforceid','text')
                ->add('salesforcesecret','text')
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                $user->setSalesforceid($data['salesforceid']);
                $user->setSalesforcesecret($data['salesforcesecret']);
                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');

                return $app->redirect('/salesforce');

            }
            return $app['twig']->render('salesforce-credentials.html.twig',
                array('salesforceForm' => $form->createView()));
        }else{
            return $app->redirect('/login');
        }

    }

}
