<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use ET_Subscriber;
use ET_TriggeredSend;
use ET_Email;

use GuzzleHttp\Client;
use CommerceGuys\Guzzle\Oauth2\GrantType\RefreshToken;
use CommerceGuys\Guzzle\Oauth2\GrantType\PasswordCredentials;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
/*use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;*/


class ApiController
{
    public function flowAction($event, Request $request, Application $app)
    {
        if ($request->headers->has('Skyflow-Token')) {
            $token = $request->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }

            $idUser = $user->getId();

            $oneEvent = $app['dao.event']->findOne($event, $idUser);
            $idEvent = $oneEvent['id'];

            $mapping = $app['dao.mapping']->findByEventUser($idEvent, $idUser);
            $idFlow = $mapping['id_flow'];

            $flow = $app['dao.flow']->findOneById($idFlow);
            $class = $flow->getClass();

            $result = $app['flow_' . $class]->event($user, $request->request, $app);
            return $app->json($result);
        }
    }

}