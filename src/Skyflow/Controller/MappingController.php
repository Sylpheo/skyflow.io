<?php

/**
 * Controller for Skyflow Mapping actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Skyflow\Domain\Mapping;

/**
 * Controller for Skyflow Mapping actions.
 */
class MappingController {

    /**
     * Retrieve all Mappings.
     *
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function indexAction(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id= $app['security']->getToken()->getUser()->getId();
            $mapping = $app['dao.mapping']->findAllByUser($id);

            return $app['twig']->render(
                "mapping.html.twig",
                array('mappings'=> $mapping)
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Create a Mapping.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return mixed
     */
    public function createMappingAction(Request $request, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $iduser = $app['security']->getToken()->getUser()->getId();
            $allEvents = $app['dao.event']->findAllByUser($iduser);
            $allFlows = $app['dao.flow']->findAllByUser($iduser);

            $events =array();
            $flows = array();
            foreach($allEvents as $event){
                $events[$event->getId()]=$event->getName();
            }

            foreach($allFlows as $flow){
                $flows[$flow->getId()]=$flow->getName();
            }

            $form = $app['form.factory']->createBuilder('form')
                ->add('event','choice',array(
                    'choices' => $events
                ))
                ->add('flow','choice',array(
                    'choices'=>$flows
                ))
                ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();

                $mapping = new Mapping();
                $mapping->setIdUser($iduser);
                $event = $app['dao.event']->findOneById($data['event']);
                $mapping->setEvent($event);
                $flow = $app['dao.flow']->findOneById($data['flow']);
                $mapping->setFlow($flow);
                $app['dao.mapping']->save($mapping);

                return $app->redirect('/mapping');
            }

            return $app['twig']->render(
                'mapping-form.html.twig',
                array('mappingForm'=>$form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Delete a Mapping.
     *
     * @param string      $id  The Mapping id.
     * @param Application $app The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMappingAction($id, Application $app) {
        $app['dao.mapping']->delete($id);

        return $app->redirect('/mapping');
    }
}