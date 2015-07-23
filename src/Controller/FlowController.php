<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Flow;




class FlowController {

    /**
     * Retrieve all flows
     * @param Application $app
     * @return mixed
     */
    public function indexAction(Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id= $app['security']->getToken()->getUser()->getId();
            $flows = $app['dao.flow']->findAllByUser($id);
            return $app['twig']->render("flows.html.twig",
                array('flows'=> $flows));
        }
    }

    /**
     * Create a flow
     * @param Request $request
     * @param Application $app
     * @return form or redirect to login
     */
    public function createFlowAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $iduser = $app['security']->getToken()->getUser()->getId();

            $form = $app['form.factory']->createBuilder('form')
                ->add('name','text')
                ->add('class','text')
                ->add('documentation','textarea',array('attr' => array('class' => 'ckeditor')))
                ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();

                $flow = new Flow();
                $flow->setName($data['name']);
                $flow->setClass($data['class']);
                $flow->setDocumentation($data['documentation']);
                $flow->setIdUser($iduser);

                $app['dao.flow']->save($flow);

                return $app->redirect('/flows');
            }

            return $app['twig']->render('flow-form.html.twig', array(
                'flowForm'=>$form->createView()
            ));
        }else{
            return $app->redirect('/login');
        }
    }

    /**
     * Delete flow
     * @param $id
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFlowAction($id, Application $app){

        $app['dao.flow']->delete($id);

        return $app->redirect('/flows');

    }

}