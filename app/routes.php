<?php

// Login form
$app->get('/login', "skyflow\Controller\HomeController::loginAction")
->bind('login');
$app->match('/new_account', "skyflow\Controller\HomeController::addUserAction")
->bind('account');

// Home page
$app->get('/', "skyflow\Controller\HomeController::indexAction");
$app->get('/gestionToken',"skyflow\Controller\HomeController::gestionToken")
    ->bind('gestionToken');
$app->get('/regenerateToken',"skyflow\Controller\HomeController::regenerateToken")
    ->bind('regenerateToken');

// Subscribers
$app->get('/subscribers',"skyflow\Controller\SubscriberController::subscribersAction")
->bind('subscribers');
$app->match('/addSub',"skyflow\Controller\SubscriberController::addSubscriberAction")
->bind('addSub');
$app->get('/subscriber/{id}/delete', "skyflow\Controller\SubscriberController::deleteSubscriberAction");

// Emails
$app->get('/emails', "skyflow\Controller\EmailController::emailsAction")
->bind('emails');
$app->match('/createEmail', "skyflow\Controller\EmailController::createEmailAction")
->bind('createEmail');
$app->get('/email/{id}/delete',"skyflow\Controller\EmailController::deleteEmailAction");
$app->match('/email/{id}',"skyflow\Controller\EmailController::infoEmailAction");
// Lists
$app->get('/lists',"skyflow\Controller\ListController::listsAction")
->bind('lists');
$app->get('/lists_sub',"skyflow\Controller\ListController::listSubscriberAction")
->bind('listsSub');
$app->match('/addSubToList',"skyflow\Controller\ListController::addSubToListAction")
->bind('addSubToList');
$app->match('/addList',"skyflow\Controller\ListController::addListAction")
->bind('addList');
$app->get('/list/{id}/delete', "skyflow\Controller\ListController::deleteListAction");

// Triggers
$app->get('/triggers',"skyflow\Controller\TriggerController::triggersAction")
->bind('triggers');
$app->match('/createTrigger', "skyflow\Controller\TriggerController::createTriggerAction")
->bind('createTrigger');
$app->match('/send', "skyflow\Controller\TriggerController::sendTriggeredSendAction")
->bind('send');
$app->match('/trigger/{customerKey}',"skyflow\Controller\TriggerController::infoTriggeredSendAction");



//Events
$app->get('/events',"skyflow\Controller\EventController::indexAction")
->bind('events');
$app->match('/createEvent',"skyflow\Controller\EventController::createEventAction")
->bind('createEvent');
$app->get('/event/{id}/delete',"skyflow\Controller\EventController::deleteEventAction");
$app->match('/event/{id}/edit',"skyflow\Controller\EventController::editEventAction")
    ->bind('editEvent');

//Flows
$app->get('/flows',"skyflow\Controller\FlowController::indexAction")
    ->bind('flows');
$app->match('/createFlow',"skyflow\Controller\FlowController::createFlowAction")
    ->bind('createFlow');
$app->get('/flow/{id}/delete',"skyflow\Controller\FlowController::deleteFlowAction");
$app->match('/flow/{id}/edit',"skyflow\Controller\FlowController::editFlowAction")
    ->bind('editFlow');

//Mapping
$app->get('/mapping',"skyflow\Controller\MappingController::indexAction")
    ->bind('mapping');
$app->match('/createMapping',"skyflow\Controller\MappingController::createMappingAction")
    ->bind('createMapping');
$app->get('/mapping/{id}/delete',"skyflow\Controller\MappingController::deleteMappingAction");

//API
//$app->post('api/event/{event}',"skyflow\Controller\ApiController::eventAction");
$app->post('/api/event/{event}',"skyflow\Controller\ApiController::flowAction");


//ExactTarget
$app->get('/et-helper',"skyflow\Controller\ExactTargetController::exactTargetHelperAction")
->bind('et-helper');
$app->match('/ET_credentials', "skyflow\Controller\ExactTargetController::setCredentialsETAction")
    ->bind('ET_credentials');

//Wave
$app->match('/wave-helper',"skyflow\Controller\WaveController::requestWaveAction")
    ->bind('wave-helper');
$app->get('/resend/{id}',"skyflow\Controller\WaveController::resendAction")
    ->bind('/resend/{id}');
$app->match('/wave_credentials',"skyflow\Controller\WaveController::setCredentialsWaveAction")
    ->bind('wave_credentials');