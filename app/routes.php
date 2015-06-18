<?php

// Login form
$app->get('/login', "exactSilex\Controller\HomeController::loginAction")
->bind('login');
$app->match('/new_account', "exactSilex\Controller\HomeController::addUserAction")
->bind('account');
$app->match('/ET_credentials', "exactSilex\Controller\HomeController::setCredentialsETAction")
->bind('ET_credentials');
$app->match('/wave_credentials',"exactSilex\Controller\HomeController::setCredentialsWaveAction")
->bind('wave_credentials');
// Home page
$app->get('/', "exactSilex\Controller\HomeController::indexAction");

// Subscribers
$app->get('/subscribers',"exactSilex\Controller\SubscriberController::subscribersAction")
->bind('subscribers');
$app->match('/addSub',"exactSilex\Controller\SubscriberController::addSubscriberAction")
->bind('addSub');
$app->get('/subscriber/{id}/delete', "exactSilex\Controller\SubscriberController::deleteSubscriberAction");

// Emails
$app->get('/emails', "exactSilex\Controller\EmailController::emailsAction")
->bind('emails');
$app->match('/createEmail', "exactSilex\Controller\EmailController::createEmailAction")
->bind('createEmail');
$app->get('/email/{id}/delete',"exactSilex\Controller\EmailController::deleteEmailAction");
$app->match('/email/{id}',"exactSilex\Controller\EmailController::infoEmailAction");
// Lists
$app->get('/lists',"exactSilex\Controller\ListController::listsAction")
->bind('lists');
$app->get('/lists_sub',"exactSilex\Controller\ListController::listSubscriberAction")
->bind('listsSub');
$app->match('/addSubToList',"exactSilex\Controller\ListController::addSubToListAction")
->bind('addSubToList');
$app->match('/addList',"exactSilex\Controller\ListController::addListAction")
->bind('addList');
$app->get('/list/{id}/delete', "exactSilex\Controller\ListController::deleteListAction");

// Triggers
$app->get('/triggers',"exactSilex\Controller\TriggerController::triggersAction")
->bind('triggers');
$app->match('/createTrigger', "exactSilex\Controller\TriggerController::createTriggerAction")
->bind('createTrigger');
$app->match('/send', "exactSilex\Controller\TriggerController::sendTriggeredSendAction")
->bind('send');
$app->match('/trigger/{customerKey}',"exactSilex\Controller\TriggerController::infoTriggeredSendAction");

//Events
$app->get('/events',"exactSilex\Controller\EventController::indexAction")
->bind('events');
$app->match('/createEvent',"exactSilex\Controller\EventController::createEventAction")
->bind('createEvent');
$app->get('/event/{id}/delete',"exactSilex\Controller\EventController::deleteEventAction");

//API
$app->post('api/event/{event}',"exactSilex\Controller\ApiController::eventAction");

$app->get('/wave',"exactSilex\Controller\ApiController::waveAction");

$app->match('/test',"exactSilex\Controller\ApiController::testAction");


//ExactTarget API Helper
$app->get('/et-helper',"exactSilex\Controller\ExactTargetController::exactTargetHelperAction")
->bind('et-helper');