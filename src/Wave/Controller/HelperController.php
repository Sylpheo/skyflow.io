<?php

/**
 * Controller for Wave helper actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Controller;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Domain\Users;

use Wave\Domain\WaveRequest;
use Wave\DAO\WaveRequestDAO;
use Wave\Service\WaveService;

/**
 * Controller for Wave helper actions.
 */
class HelperController
{
    /**
     * The HTTP request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The Wave service.
     *
     * @var WaveService
     */
    protected $wave;

    /**
     * The skyflow logged-in user.
     *
     * @var Users
     */
    protected $user;

    /**
     * The DAO object for Request.
     *
     * @var WaveRequestDAO
     */
    protected $waveRequestDAO;

    /**
     * The form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * The Twig environment.
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * WaveController constructor.
     *
     * @param Request              $request     The HTTP request.
     * @param WaveService          $wave        The Wave service.
     * @param Users                $user        The logged-in skyflow user.
     * @param WaveRequestDAO       $userDAO     The DAO object for WaveRequest.
     * @param FormFactoryInterface $formFactory The Form factory.
     * @param \Twig_Environment    $twig        The Twig environment.
     */
    public function __construct(
        Request $request,
        WaveService $wave,
        Users $user,
        WaveRequestDAO $waveRequestDAO,
        FormFactoryInterface $formFactory,
        \Twig_Environment $twig
    ) {
        $this->request = $request;
        $this->wave = $wave;
        $this->user = $user;
        $this->waveRequestDAO = $waveRequestDAO;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * Send a Wave request.
     *
     * @return mixed
     */
    public function requestAction()
    {
        // $app not injected
        //if ($this->user === null) {
        //    return $app->redirect('/login');
        //}

        $userId = $this->user->getId();
        $history = $this->waveRequestDAO->findAllByUser($userId);

        $form = $this->formFactory->createBuilder('form')
            ->add('Request','textarea',array(
                'attr' => array('cols' => '100', 'rows' => '3'),
            ))
            ->getForm();

        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid()) {
            $array = $form->getData();
            $r = $array['Request'];

            $result = $this->waveRequestDAO->findByRequest($r, $userId);
            if($result == null){
                $waveRequest = new WaveRequest();
                $waveRequest->setIdUser($userId);
                $waveRequest->setRequest($r);
                $waveRequestDAO->save($waveRequest);
            }

            $data = $this->wave->request($r);

            return $this->twig->render(
                'results.html.twig',
                array('results'=>$data)
            );
        }

        return $this->twig->render('wave-apihelper.html.twig',
            array(
                'requestForm' => $form->createView(),
                'history' => $history,
            )
        );
    }

    /**
     * Resend a Wave request.
     *
     * @param string  $id The Wave request id to resend.
     * @return string The rendered results template.
     */
    public function resendAction($id)
    {
        //if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        $waveRequest = $this->waveRequestDAO->findById($id);

        $data = $this->wave->request($waveRequest->getRequest());

        return $this->twig->render(
            'results.html.twig',
            array('results'=>$data)
        );
        //}
    }
}
