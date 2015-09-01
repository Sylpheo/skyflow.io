<?php

/**
 * OAuth user controller.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Controller\OAuthController;
use skyflow\DAO\OAuthUserDAO;
use skyflow\Domain\OAuthUser;
use skyflow\Service\OAuthServiceInterface;

/**
 * OAuth user controller.
 *
 * This controller manages an OAuth user settings. This controller is not
 * abstract because it can be used "as is" for Credentials forms that just ask
 * for a client_id and a client_secret. You just have to inject your user and
 * user DAO.
 */
class OAuthUserController extends OAuthController
{
    /**
     * The OAuth user.
     *
     * @var OAuthUser
     */
    private $user;

    /**
     * The OAuth user DAO object.
     *
     * @var OAuthUserDAO
     */
    private $userDAO;

    /**
     * The OAuth credentials form for user to change the connected application
     * credentials.
     *
     * An addon must have a credentials form for the user to setup the credentials
     * for the associated connected application (e.g. client_id, client_secret).
     *
     * Beware !!! The FormType used to generate this form must be a subclass of
     * Skyflow\Form\Type\OAuthCredentialsType or it must have "client_id" and
     * "client_secret" named fields or the form data won't be saved to the user.
     *
     * @var FormInterface
     */
    private $credentialsForm;

    /**
     * OAuth user controller contructor.
     *
     * @param Request               $request         The current HTTP request.
     * @param OAuthServiceInterface $authService     The authentication service.
     * @param OAuthUser             $user            The OAuth user for this addon.
     * @param OAuthUserDAO          $userDAO         The DAO object for the user.
     * @param FormInterface         $credentialsForm The credentials form.
     */
    public function __construct(
        Request $request,
        OAuthServiceInterface $authService,
        OAuthUser $user,
        OAuthUserDAO $userDAO,
        FormInterface $credentialsForm
    ) {
        parent::__construct($request, $authService);
        $this->user = $user;
        $this->userDAO = $userDAO;
        $this->credentialsForm = $credentialsForm;
    }

    /**
     * Get the OAuth user.
     *
     * @return OAuthUser The OAuth user.
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Get the OAuth user DAO object.
     *
     * @return OAuthUserDAO The OAuth user DAO object.
     */
    protected function getUserDAO()
    {
        return $this->userDAO;
    }

    /**
     * Get the OAuth user credentials form.
     *
     * @return FormInterface The OAuth credentials form.
     */
    protected function getCredentialsForm()
    {
        return $this->credentialsForm;
    }

    /**
     * Credentials action.
     *
     * Show client_id and client_secret credentials and allow
     * the skyflow user to change them.
     */
    public function credentialsAction()
    {
        if (!$this->getCredentialsForm()->isSubmitted()) {
            $this->getCredentialsForm()->handleRequest($this->getRequest());
        }

        if ($this->getCredentialsForm()->isSubmitted()
            && $this->getCredentialsForm()->isValid()
        ) {
            $data = $this->getCredentialsForm()->getData();

            if ($this->getUser()->getClientId() !== $data['client_id']
                || $this->getUser()->getClientSecret() !== $data['client_secret']
            ) {
                $this->getUser()->setClientId($data['client_id']);
                $this->getUser()->setClientSecret($data['client_secret']);
                $this->getUserDAO()->save($this->getUser());

                $this->getAuthService()->authenticate();
            }
        }

        return $this->twig->render(
            'oauth-credentials.html.twig',
            array('credentials-form' => $this->getCredentialsForm()->createView())
        );
    }
}
