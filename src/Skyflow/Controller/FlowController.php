<?php

/**
 * Controller for Flow actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Flow;

/**
 * Controller for Flow actions.
 */
class FlowController
{
    /**
     * Retrieve all flows.
     *
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function indexAction(Application $app)
    {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id= $app['security']->getToken()->getUser()->getId();
            $flows = $app['dao.flow']->findAllByUserId($id);

            return $app['twig']->render(
                "flows.html.twig",
                array('flows'=> $flows)
            );
        }
    }

    /**
     * Create a flow.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return mixed
     */
    public function createFlowAction(Request $request, Application $app)
    {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $iduser = $app['security']->getToken()->getUser()->getId();

            $form = $app['form.factory']->createBuilder('form')
                ->add('name', 'text')
                ->add('class', 'text')
                ->add('documentation', 'textarea', array('attr' => array('class' => 'ckeditor')))
                ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $flow = new Flow();
                $flow->setName($data['name']);
                $flow->setClass($data['class']);
                $flow->setDocumentation($data['documentation']);
                $flow->setUserId($iduser);

                $app['dao.flow']->save($flow);

                return $app->redirect('/flows');
            }

            return $app['twig']->render(
                'flow-form.html.twig',
                array('flowForm'=>$form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Edit a flow.
     *
     * @param string      $id      The Flow id.
     * @param Request     $request The HTTP Request.
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function editFlowAction($id, Request $request, Application $app)
    {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $iduser = $app['security']->getToken()->getUser()->getId();

            $flow = $app['dao.flow']->findOneById($id);
            $form = $app['form.factory']->createBuilder('form', $flow)
                ->add('name', 'text')
                ->add('class', 'text')
                ->add('documentation', 'textarea', array('attr' => array('class' => 'ckeditor')))
                ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $app['dao.flow']->save($flow);

                return $app->redirect('/mapping');
            }

            return $app['twig']->render(
                'flow-edit.html.twig',
                array('flowForm'=>$form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Delete a flow.
     *
     * @param string      $id  The Flow id.
     * @param Application $app The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFlowAction($id, Application $app)
    {
        $app['dao.flow']->delete($id);

        return $app->redirect('/flows');
    }
}
