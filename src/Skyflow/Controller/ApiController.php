<?php

namespace Skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use GuzzleHttp\Client;


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

            $result = $app['flow_' . $class]->event($request);
            return $app->json($result);
        }
    }

}