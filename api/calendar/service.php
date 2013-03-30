<?php
/**
 * Mobile Gateways
 *
 * An open source application framework for Mobile Gateways
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@theaustinconnergroup.info so we can send you a copy immediately.
 *
 * @package		Mobile Gateways
 * @author		Mobile Gateways Dev Team
 * @copyright   Copyright (c) 2013, The Austin Conner Group. (http://theaustinconnergroup.info/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://theaustinconnergroup.com
 * @since		Version 1.0
 * @filesource
 */

require_once $_SERVER['DOCUMENT_ROOT'].'system/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require $_SERVER['DOCUMENT_ROOT'].'/system/ActiveRecord.php';
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory($_SERVER['DOCUMENT_ROOT'].'api/models');
    $cfg->set_connections(array(
        'development' => 'mysql://root:acg100199@localhost/services'
    ));
});

$app = new \Slim\Slim();

/**
 * Authenticate all requests
 *
 */
$app->hook('slim.before.dispatch', function () use ($app) {

    // Provide a better validation here...
    if ($app->request()->params('apiKey') !== "B6EE6188709B") {
        #$app->halt(403, "Invalid or Missing Key");
    }
});

/**
 * Set the default content type
 *
 */
$app->hook('slim.after.router', function() use ($app) {

    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['X-Powered-By'] = 'Mobile Gateways';

});

// Standardize response
$response = array('status'=>'ok', 'message'=>'', 'count'=>0, 'data'=>array());

/**
 * Status Page
 *
 * /calendar/
 *
 */
$app->get('/', function () use($app, $response)  {

    $response['message'] = 'Mobile Gateways Events Services v1.0';
    echo json_encode($response);

});


/**
 * Fetch Calendar - returns calendar for next 30 days
 *
 * /calendar/{id}
 *
 */
$app->get("/:id", function ($id) use ($app, $response) {

    // get date
    $nextDate = new DateTime('GMT');
    $today = new DateTime('GMT');
    $nextDate->add(new DateInterval('P1M'));

    // query events
    $calendarData = Events::find('all', array('conditions' => array('calendar_id = ? AND start_time BETWEEN ? AND ?', $id, $today->format("Y-m-d"),$nextDate->format("Y-m-d"))));
    // package the data
    $response['data'] = arrayMap($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

});

/**
 * Fetch Calendar - returns calendar for month
 *
 * /calendar/{id}/{month}
 *
 */
$app->get("/:id/:month", function ($id, $month) use ($app, $response) {

    // query events
    $calendarData = Events::find('all', array('conditions' => array('calendar_id = ? AND MONTH(start_time) = ?', $id, $month)));
    // package the data
    $response['data'] = arrayMap($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);


});

/**
 * Fetch Calendar - returns calendar for date range
 *
 * /calendar/{id}/{startDate}/{endDate}
 *
 */
$app->get("/:id/:startDate/:endDate", function ($id, $startDate, $endDate) use ($app, $response) {

    // query events
    $calendarData = Events::find('all', array('conditions' => array('calendar_id = ? AND start_time BETWEEN ? AND ?', $id, $startDate, $endDate)));
    // package the data
    $response['data'] = arrayMap($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);
});


/**
 * Add New Calendar
 *
 *
 */
$app->post("/add", function ($id) use ($app) {

    $calendarData = new Calendar();



});

/* CALENDAR EVENTS */

/**
 * Add Event to Calendar
 *
 *
 *
 */
$app->post("/:id/add/event", function ($id) use ($app) {

    $calendarData = new Calendar();


});

/**
 * Update Event to Calendar
 *
 *
 *
 */
$app->post("/:id/update/event", function ($id) use ($app) {

    $calendarData = new Calendar();



});

/**
 * Delete Event to Calendar
 *
 *
 *
 */
$app->post("/:id/delete/event", function ($id) use ($app) {

    $calendarData = new Calendar();



});

/**
 * Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();



/**
 * Data conversion utilites for calendar events
 *
 *
 *
 *
 *
 */
 function arrayMap($events){

    return array_map(create_function('$m','return $m->values_for(array(\'id\',\'title\',\'description\',\'start_time\',\'end_time\'));'),$events);

 }
