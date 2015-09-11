<?php

/**
 * Controller provider for the Skyflow application.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

/**
 * Controller provider for the Skyflow application.
 */
class SkyflowControllerProvider implements ControllerProviderInterface
{
    /**
     * Add api controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Restructure routes and move to its own class ApiControllerProvider.
     */
    private function apiControllers(ControllerCollection $controllers)
    {
        $controllers->post(
            '/api/event/{event}',
            'Skyflow\Controller\ApiController::flowAction'
        )->bind('flow');
    }

    /**
     * Add user controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Restructure routes and move to its own class UserControllerProvider.
     */
    private function userControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/gestionToken',
            'Skyflow\Controller\HomeController::gestionToken'
        )->bind('gestionToken');

        $controllers->get(
            '/regenerateToken',
            'Skyflow\Controller\HomeController::regenerateToken'
        )->bind('regenerateToken');
    }

    /**
     * Add event controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Restructure routes and move to its own class EventControllerProvider.
     */
    private function eventControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/events',
            'Skyflow\Controller\EventController::indexAction'
        )->bind('events');

        $controllers->match(
            '/createEvent',
            'Skyflow\Controller\EventController::createEventAction'
        )->bind('createEvent');

        $controllers->get(
            '/event/{id}/delete',
            'Skyflow\Controller\EventController::deleteEventAction'
        );

        $controllers->match(
            '/event/{id}/edit',
            'Skyflow\Controller\EventController::editEventAction'
        )->bind('editEvent');
    }

    /**
     * Add flow controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Restructure routes and move to its own class FlowControllerProvider.
     */
    private function flowController(ControllerCollection $controllers)
    {
        $controllers->get(
            '/flows',
            'Skyflow\Controller\FlowController::indexAction'
        )->bind('flows');

        $controllers->match(
            '/createFlow',
            'Skyflow\Controller\FlowController::createFlowAction'
        )->bind('createFlow');

        $controllers->get(
            '/flow/{id}/delete',
            'Skyflow\Controller\FlowController::deleteFlowAction'
        );

        $controllers->match(
            '/flow/{id}/edit',
            'Skyflow\Controller\FlowController::editFlowAction'
        )->bind('editFlow');
    }

    /**
     * Add mapping controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Restructure routes and move to its own class MappingControllerProvider.
     */
    private function mappingControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/mapping',
            'Skyflow\Controller\MappingController::indexAction'
        )->bind('mapping');

        $controllers->match(
            '/createMapping',
            'Skyflow\Controller\MappingController::createMappingAction'
        )->bind('createMapping');

        $controllers->get(
            '/mapping/{id}/delete',
            'Skyflow\Controller\MappingController::deleteMappingAction'
        );
    }

    /**
     * Add ExactTarget controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Move to src/ExactTarget/Provider/ExactTargetControllerProvider.
     */
    private function exactTargetHelperControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/et-helper',
            'Skyflow\Controller\ExactTargetController::exactTargetHelperAction'
        )->bind('et-helper');

        $controllers->match(
            '/ET_credentials',
            'Skyflow\Controller\ExactTargetController::setCredentialsETAction'
        )->bind('ET_credentials');
    }

    /**
     * Add ExactTarget Subscriber controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Move to src/ExactTarget/Provider/ExactTargetSubscriberControllerProvider.
     */
    private function exactTargetSubscriberControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/subscribers',
            'Skyflow\Controller\SubscriberController::subscribersAction'
        )->bind('subscribers');

        $controllers->match(
            '/addSub',
            'Skyflow\Controller\SubscriberController::addSubscriberAction'
        )->bind('addSub');

        $controllers->get(
            '/subscriber/{id}/delete',
            'Skyflow\Controller\SubscriberController::deleteSubscriberAction'
        );
    }

    /**
     * Add ExactTarget Email controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Move to src/ExactTarget/Provider/ExactTargetEmailControllerProvider.
     */
    private function exactTargetEmailControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/emails',
            'Skyflow\Controller\EmailController::emailsAction'
        )->bind('emails');

        $controllers->match(
            '/createEmail',
            'Skyflow\Controller\EmailController::createEmailAction'
        )->bind('createEmail');

        $controllers->get(
            '/email/{id}/delete',
            'Skyflow\Controller\EmailController::deleteEmailAction'
        );

        $controllers->match(
            '/email/{id}',
            'Skyflow\Controller\EmailController::infoEmailAction'
        );
    }

    /**
     * Add ExactTarget List controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Move to src/ExactTarget/Provider/ExactTargetListControllerProvider.
     */
    private function exactTargetListControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/lists',
            'Skyflow\Controller\ListController::listsAction'
        )->bind('lists');

        $controllers->get(
            '/lists_sub',
            'Skyflow\Controller\ListController::listSubscriberAction'
        )->bind('listsSub');

        $controllers->match(
            '/addSubToList',
            'Skyflow\Controller\ListController::addSubToListAction'
        )->bind('addSubToList');

        $controllers->match(
            '/addList',
            'Skyflow\Controller\ListController::addListAction'
        )->bind('addList');

        $controllers->get(
            '/list/{id}/delete',
            'Skyflow\Controller\ListController::deleteListAction'
        );
    }

    /**
     * Add ExactTarget Trigger controllers to the Skyflow controllers.
     *
     * @param ControllerCollection $controllers The Skyflow controllers collection.
     * @todo  Move to src/ExactTarget/Provider/ExactTargetTriggerControllerProvider.
     */
    private function exactTargetTriggerControllers(ControllerCollection $controllers)
    {
        $controllers->get(
            '/triggers',
            'Skyflow\Controller\TriggerController::triggersAction'
        )->bind('triggers');

        $controllers->match(
            '/createTrigger',
            'Skyflow\Controller\TriggerController::createTriggerAction'
        )->bind('createTrigger');

        $controllers->match(
            '/send',
            'Skyflow\Controller\TriggerController::sendTriggeredSendAction'
        )->bind('send');

        $controllers->match(
            '/trigger/{customerKey}',
            'Skyflow\Controller\TriggerController::infoTriggeredSendAction'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/',
            'Skyflow\Controller\HomeController::indexAction'
        )->bind('home');

        $controllers->get(
            '/login',
            'Skyflow\Controller\HomeController::loginAction'
        )->bind('login');

        $controllers->match(
            '/new_account',
            'Skyflow\Controller\HomeController::addUserAction'
        )->bind('account');

        /**
         * @todo This is horrible ! Refactor that.
         */
        $this->apiControllers($controllers);
        $this->userControllers($controllers);
        $this->eventControllers($controllers);
        $this->flowController($controllers);
        $this->mappingControllers($controllers);
        $this->exactTargetHelperControllers($controllers);
        $this->exactTargetSubscriberControllers($controllers);
        $this->exactTargetEmailControllers($controllers);
        $this->exactTargetListControllers($controllers);
        $this->exactTargetTriggerControllers($controllers);

        return $controllers;
    }
}
