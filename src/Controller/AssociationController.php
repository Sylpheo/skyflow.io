<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Association;




class AssociationController {

    /**
     * Retrieve all associations
     * @param Application $app
     * @return mixed
     */
    public function indexAction(Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id= $app['security']->getToken()->getUser()->getId();
            $associations = $app['dao.association']->findAllByUser($id);

            return $app['twig']->render("associations.html.twig",
                array('associations'=> $associations));
        }else{
            return $app->redirect('/login');
        }
    }

    /**
     * Create an association
     * @param Request $request
     * @param Application $app
     * @return form or redirect to login
     */
    public function createAssociationAction(Request $request,Application $app){
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

               // var_dump($data);exit;
                $association = new Association();
                $association->setIdUser($iduser);
                $event = $app['dao.event']->findOneById($data['event']);
                $association->setEvent($event);
                $flow = $app['dao.flow']->findOneById($data['flow']);
                $association->setFlow($flow);
                $app['dao.association']->save($association);

                return $app->redirect('/associations');
            }

            return $app['twig']->render('association-form.html.twig', array(
                'associationForm'=>$form->createView()
            ));
        }else{
            return $app->redirect('/login');
        }
    }

    /**
     * Delete Association
     * @param $id
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAssociationAction($id, Application $app){

        $app['dao.association']->delete($id);

        return $app->redirect('/associations');

    }

}