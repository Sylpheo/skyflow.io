<?php

/**
 * Abstract controller class for the Skyflow application.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Abstract controller class for the Skyflow application.
 *
 * The responsibility of a controller is to handle a Request and return a response.
 * The response may be a string, a Response object or a rendered Twig template.
 * The response may include Forms and we may use a FlashBag to notify informations
 * to the user.
 *
 * This controller is abstract because it has no actions. Child classes must add
 * actions.
 */
abstract class AbstractController
{
    /**
     * The HTTP request handled by the controller.
     *
     * @var Request
     */
    private $request;

    /**
     * The Form factory to construct form.
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * The FlashBag to notify informations to the user.
     *
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * The Twig environment to render Twig templates.
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Controller constructor.
     *
     * The main responsibility of a controller is to handle a request. That's
     * why the Request object is injected from the constructor.
     *
     * @param Request $request The current HTTP request.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the current HTTP request.
     *
     * @return Request The current HTTP request.
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the form factory to allow the controller to construct custom forms.
     *
     * The controller may or may not need the form factory. That's why we inject
     * it from a setter method.
     *
     * @param FormFactoryInterface $formFactory The form factory.
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Get the form factory.
     *
     * @return FormFactoryInterface The form factory.
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Set the flash bag to allow the controller to notify informations to the user.
     *
     * The controller may or may not need the flash bag. That's why we inject it
     * from a setter method.
     *
     * @param FlashBagInterface $flashBag The flash bag.
     */
    public function setFlashBag(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * Get the flash bag.
     *
     * @return FlashBagInterface The flash bag.
     */
    protected function getFlashBag()
    {
        return $this->flashBag;
    }

    /**
     * Set the Twig environment.
     *
     * @param $twig The Twig environment.
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Get the Twig environment.
     *
     * @return \Twig_Environment The Twig environment.
     */
    protected function getTwig()
    {
        return $this->twig;
    }
}
