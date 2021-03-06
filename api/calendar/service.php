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
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");

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
 * Fetch Calendar - returns calendar for next 90 days
 *
 * /calendar/{id}
 *
 */
$app->get("/:id", function ($id) use ($app, $response) {

    // get date
    $nextDate = new DateTime('GMT');
    $today = new DateTime('GMT');
    $nextDate->add(new DateInterval('P3M'));

    // query events
    $calendarData = Events::find('all', array('order'=>'start_time asc', 'conditions' => array('account = ? AND start_time BETWEEN ? AND ?', $id, $today->format("Y-m-d"),$nextDate->format("Y-m-d"))));
    // package the data
    $response['data'] = arrayMapEvent($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Fetch Calendar - returns calendar for month
 *
 * /calendar/{id}/{month}-{year}
 *
 */
$app->get("/:id/:month-:year", function ($id, $month, $year) use ($app, $response) {

    // query events
    $calendarData = Events::find('all', array('order'=>'start_time asc', 'conditions' => array('account = ? AND MONTH(start_time) = ? AND YEAR(start_time) = ?', $id, $month, $year)));
    // package the data
    $response['data'] = arrayMapEvent($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);


})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Fetch Calendar - returns calendar for date range
 *
 * /calendar/{id}/{startDate}/{endDate}
 *
 */
$app->get("/:id/:startDate/:endDate", function ($id, $startDate, $endDate) use ($app, $response) {

    // query events
    $calendarData = Events::find('all', array('order'=>'start_time asc', 'conditions' => array('account = ? AND start_time BETWEEN ? AND ?', $id, $startDate, $endDate)));
    // package the data
    $response['data'] = arrayMapEvent($calendarData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);
})->conditions(array('id' => '[0-9a-z]{32}'));


/**
 * Add New Calendar
 *
 *
 */
//$app->post("/add", function () use ($app) {
//
//    $calendarData = new Calendar();
//
//
//
//});

/* CALENDAR EVENTS */


/**
 * Get Calendar Event
 *
 * get: /calendar/event/{id}
 *
 */
$app->get("/event/:id", function ($id) use ($app, $response) {

    // query events
    $calendarData = Events::find($id);
    // package the data
    $response['data'] = $calendarData->values_for(array('id','title','description','start_time','end_time','place'));
    $response['count'] = 1;
    // send the data
    echo json_encode($response);

});

/**
 * Add Event to Calendar (id)
 *
 *
 *
 */
$app->post("/event/:id", function ($id) use ($app, $response) {

    $request = json_decode($app->request()->getBody());

    // Validate calendar id
    if($id == $request->account){

        // create the event
        $event = new Events();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start_time = $request->start_time->date;
        $event->end_time = $request->end_time->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','description','start_time','end_time','place'));
        $response['message'] = "event saved";
    }
    else{
        $response['status'] = "error";
        $response['message'] = "cancled";
    }

    // confirmation
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Update Event to Calendar (id)
 *
 *
 *
 */
$app->put("/event/:id", function ($id) use ($app) {

    $request = json_decode($app->request()->getBody());

    // Validate calendar id
    if($id == $request->account){

        // find the event
        $event = Events::find($request->id);
        // Need Validation Here
        // Update and Save Values
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start_time = $request->start_time->date;
        $event->end_time = $request->end_time->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','description','start_time','end_time','place'));
        $response['message'] = "event saved";
    }
    else{
        $response['status'] = "error";
        $response['message'] = "cancled";
    }

    // confirmation
    echo json_encode($response);



});

/**
 * Delete Event to Calendar (id)
 *
 *
 *
 */
$app->delete("/event/:id", function ($id) use ($app) {


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
 function arrayMapEvent($events){

    return array_map(create_function('$m','return $m->values_for(array(\'id\',\'title\',\'description\',\'start_time\',\'end_time\',\'place\'));'),$events);

 }
