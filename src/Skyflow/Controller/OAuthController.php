<?php

/**
 * Abstract OAuth authentication controller for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Skyflow\Controller\OAuthControllerInterface;
use Skyflow\Form\OAuthCredentialsForm;
use Skyflow\Service\OAuthServiceInterface;

/**
 * Abstract OAuth authentication controller for use by the Skyflow addons.
 *
 * The OAuthController must delegates OAuth management to the OAuth service.
 * Using a thin OAuth controller and a fat OAuth service, we provide the ability
 * to perform authentication tasks from outside of the controllers.
 */
class OAuthController extends AbstractController implements OAuthControllerInterface
{
    /**
     * The OAuth authentication service to handle OAuth authentication details.
     *
     * @var OAuthServiceInterface
     */
    private $authService;

    /**
     * The url to redirect the user to after authentication.
     *
     * @var string
     */
    private $redirectUrl;

    /**
     * AbstractOAuthController contructor.
     *
     * @param Request               $request         The current HTTP request.
     * @param OAuthServiceInterface $authService     The OAuth authentication service.
     * @param string                $redirectUrl     The url to redirect to after authentication.
     */
    public function __construct(
        Request               $request,
        OAuthServiceInterface $authService,
        $redirectUrl = '/'
    ) {
        parent::__construct($request);
        $this->authService = $authService;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Get the OAuth authentication service.
     *
     * @return OAuthServiceInterface The OAuth authentication service.
     */
    protected function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Get the url to redirect the user to after authentication.
     *
     * This getter is public because the redirect url is a string so it is
     * passed by value : no risk to have provider prefix properties changed from
     * the outside.
     *
     * @return string The redirect url.
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateAction()
    {
        $this->getAuthService()->authenticate();
    }

    /**
     * {@inheritdoc}
     */
    public function callbackAction()
    {
        $code = $_GET['code'];
        $this->getAuthService()->callback($code);

        return new RedirectResponse($this->redirectUrl);
    }
}
