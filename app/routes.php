<?php

/**
 * Application routes.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

// ========== Skyflow ==========

// ----- API -----

$app->post(
    '/api/event/{event}',
    'Skyflow\Controller\ApiController::flowAction'
)->bind('flow');

// ----- Login Form -----

$app->get(
    '/login',
    'Skyflow\Controller\HomeController::loginAction'
)->bind('login');

$app->match(
    '/new_account',
    'Skyflow\Controller\HomeController::addUserAction'
)->bind('account');

// ----- Home Page -----

$app->get(
    '/',
    'Skyflow\Controller\HomeController::indexAction'
)->bind('home');

$app->get(
    '/gestionToken',
    'Skyflow\Controller\HomeController::gestionToken'
)->bind('gestionToken');

$app->get(
    '/regenerateToken',
    'Skyflow\Controller\HomeController::regenerateToken'
)->bind('regenerateToken');


// ----- Events -----

$app->get(
    '/events',
    'Skyflow\Controller\EventController::indexAction'
)->bind('events');

$app->match(
    '/createEvent',
    'Skyflow\Controller\EventController::createEventAction'
)->bind('createEvent');

$app->get(
    '/event/{id}/delete',
    'Skyflow\Controller\EventController::deleteEventAction'
);

$app->match(
    '/event/{id}/edit',
    'Skyflow\Controller\EventController::editEventAction'
)->bind('editEvent');

// ----- Flows -----

$app->get(
    '/flows',
    'Skyflow\Controller\FlowController::indexAction'
)->bind('flows');

$app->match(
    '/createFlow',
    'Skyflow\Controller\FlowController::createFlowAction'
)->bind('createFlow');

$app->get(
    '/flow/{id}/delete',
    'Skyflow\Controller\FlowController::deleteFlowAction'
);

$app->match(
    '/flow/{id}/edit',
    'Skyflow\Controller\FlowController::editFlowAction'
)->bind('editFlow');

// ----- Mappings -----

$app->get(
    '/mapping',
    'Skyflow\Controller\MappingController::indexAction'
)->bind('mapping');

$app->match(
    '/createMapping',
    'Skyflow\Controller\MappingController::createMappingAction'
)->bind('createMapping');

$app->get(
    '/mapping/{id}/delete',
    'Skyflow\Controller\MappingController::deleteMappingAction'
);

// ========== ExactTarget ==========

// ----- Helper -----

$app->get(
    '/et-helper',
    'Skyflow\Controller\ExactTargetController::exactTargetHelperAction'
)->bind('et-helper');

// ----- Credentials -----

$app->match(
    '/ET_credentials',
    'Skyflow\Controller\ExactTargetController::setCredentialsETAction'
)->bind('ET_credentials');

// ----- Subscribers -----

$app->get(
    '/subscribers',
    'Skyflow\Controller\SubscriberController::subscribersAction'
)->bind('subscribers');

$app->match(
    '/addSub',
    'Skyflow\Controller\SubscriberController::addSubscriberAction'
)->bind('addSub');

$app->get(
    '/subscriber/{id}/delete',
    'Skyflow\Controller\SubscriberController::deleteSubscriberAction'
);

// ----- Emails -----

$app->get(
    '/emails',
    'Skyflow\Controller\EmailController::emailsAction'
)->bind('emails');

$app->match(
    '/createEmail',
    'Skyflow\Controller\EmailController::createEmailAction'
)->bind('createEmail');

$app->get(
    '/email/{id}/delete',
    'Skyflow\Controller\EmailController::deleteEmailAction'
);

$app->match(
    '/email/{id}',
    'Skyflow\Controller\EmailController::infoEmailAction'
);

// ----- Lists -----

$app->get(
    '/lists',
    'Skyflow\Controller\ListController::listsAction'
)->bind('lists');

$app->get(
    '/lists_sub',
    'Skyflow\Controller\ListController::listSubscriberAction'
)->bind('listsSub');

$app->match(
    '/addSubToList',
    'Skyflow\Controller\ListController::addSubToListAction'
)->bind('addSubToList');

$app->match(
    '/addList',
    'Skyflow\Controller\ListController::addListAction'
)->bind('addList');

$app->get(
    '/list/{id}/delete',
    'Skyflow\Controller\ListController::deleteListAction'
);

// ----- Triggers -----

$app->get(
    '/triggers',
    'Skyflow\Controller\TriggerController::triggersAction'
)->bind('triggers');

$app->match(
    '/createTrigger',
    'Skyflow\Controller\TriggerController::createTriggerAction'
)->bind('createTrigger');

$app->match(
    '/send',
    'Skyflow\Controller\TriggerController::sendTriggeredSendAction'
)->bind('send');

$app->match(
    '/trigger/{customerKey}',
    'Skyflow\Controller\TriggerController::infoTriggeredSendAction'
);
