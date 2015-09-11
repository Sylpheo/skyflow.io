<?php

/**
 * Abstract helper controller for use by Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Symfony\Component\HttpFoundation\Request;

use Skyflow\Controller\AbstractController;
use Skyflow\DAO\OAuthUserDAO;
use Skyflow\Domain\OAuthUser;
use Skyflow\Facade;
use Skyflow\Service\OAuthServiceInterface;

/**
 * Abstract helper controller for use by Skyflow addons.
 *
 * This controller is abstract because it has no actions. At this point we don't
 * know which kind of helper the controller provides. Child classes must add
 * actions.
 */
abstract class AbstractHelperController extends AbstractController
{
    /**
     * The addon Facade to access utility methods and addon services.
     *
     * @var Facade
     */
    private $addon;

    /**
     * AbstractHelperController constructor.
     *
     * @param Request $request The HTTP request.
     * @param Facade  $addon   The addon facade.
     */
    public function __construct(
        Request $request,
        Facade $addon
    ) {
        parent::__construct($request);
        $this->addon = $addon;
    }

    /**
     * Get the addon facade.
     *
     * @return Facade The addon facade.
     */
    protected function getAddon()
    {
        return $this->addon;
    }
}
