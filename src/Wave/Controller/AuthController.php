<?php

/**
 * Controller for Wave OAuth2 authentication actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\DAO\UsersDAO;
use skyflow\Domain\Users;

use Wave\Service\AuthService;
use Wave\Service\WaveService;

/**
 * Controller for Wave OAuth2 authentication actions.
 */
class AuthController
{
    /**
     * The HTTP request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The Wave authentication service.
     *
     * @var AuthService
     */
    protected $auth;

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
     * The DAO object for User.
     *
     * @var UserDAO
     */
    protected $userDAO;

    /**
     * The Wave credentials form.
     *
     * @var FormInterface
     */
    protected $credentialsForm;

    /**
     * The Twig environment.
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * AuthController contructor.
     *
     * @param Request           $request         The HTTP request.
     * @param AuthService       $auth            The Wave authentication service.
     * @param WaveService       $wave            The Wave service.
     * @param Users             $user            The logged-in skyflow user.
     * @param UsersDAO          $userDAO         The DAO object for User.
     * @param FormInterface     $credentialsForm The Wave credentials form.
     * @param \Twig_Environment $twig            The Twig environment.
     */
    public function __construct(
        Request $request,
        AuthService $auth,
        WaveService $wave,
        Users $user,
        UsersDAO $userDAO,
        FormInterface $credentialsForm,
        \Twig_Environment $twig
    ) {
        $this->request = $request;
        $this->auth = $auth;
        $this->wave = $wave;
        $this->user = $user;
        $this->userDAO = $userDAO;
        $this->credentialsForm = $credentialsForm;
        $this->twig = $twig;
    }

    /**
     * Credentials action.
     *
     * Show client_id and client_secret credentials and allow
     * the skyflow user to change them.
     */
    public function credentialsAction()
    {
        // $app not injected
        //if ($this->user === null) {
        //    return $app->redirect('/login');
        //}

        $this->credentialsForm->handleRequest($this->request);

        if($this->credentialsForm->isSubmitted() && $this->credentialsForm->isValid()){
            $data = $this->credentialsForm->getData();

            // TODO: uncomment
            if (
                $this->user->getWaveClientId() !== $data['client_id']
                || $this->user->getWaveClientSecret() !== $data['client_secret']
                || $this->user->getWaveSandbox() !== $data['sandbox']
            ) {
                $this->user->setWaveClientId($data['client_id']);
                $this->user->setWaveClientSecret($data['client_secret']);
                $this->user->setWaveSandbox($data['sandbox']);
                $this->userDAO->save($this->user);
                //$this->flash->add('success', 'The user was succesfully updated.');

                $this->auth->authenticate();
            }
        }

        return $this->twig->render(
            'wave-credentials-form.html.twig',
            array('waveForm' => $this->credentialsForm->createView())
        );
    }

    /**
     * Authenticate against Wave.
     */
    public function authenticateAction()
    {
        $this->auth->authenticate();
    }

    /**
     * Handle authentication callback.
     */
    public function callbackAction()
    {
        $this->auth->callback($_GET['code']);

        return $this->twig->render(
            'wave-credentials-form.html.twig',
            array('waveForm' => $this->credentialsForm->createView())
        );
    }
}
