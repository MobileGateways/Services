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
 * get: /gallery/
 *
 */
$app->get('/', function () use($app, $response)  {

    $response['message'] = 'Mobile Gateways Gallery Services v1.0';
    echo json_encode($response);

});


/**
 * Fetch Gallery - returns recent photo (last 10 max??)
 *
 * get: /gallery/{id}
 *
 */
$app->get("/:id", function ($id) use ($app, $response) {

    // get date
    $today = new DateTime('GMT');

    // query photo
    $photoData = Media::find('all', array('limit'=>'10', 'order'=>'post_date desc', 'conditions' => array('account = ? AND post_date <= ?', $id, $today->format("Y-m-d"))));
    // package the data
    $response['data'] = arrayMapMedia($photoData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Fetch Gallery - returns media for month
 *
 * get: /gallery/{id}/{month}-{year}
 *
 */
$app->get("/:id/:month-:year", function ($id, $month, $year) use ($app, $response) {
    // query photo
    $photoData = Media::find('all', array('order'=>'post_date asc', 'conditions' => array('account = ? AND MONTH(post_date) = ? AND YEAR(post_date) = ?', $id, $month, $year)));
    // package the data
    $response['data'] = arrayMapMedia($photoData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));



/* NEWS FEEDS */


/**
 * Get Gallery Event
 *
 * get: /gallery/photo/{id}
 *
 */
$app->get("/photo/:id", function ($id) use ($app, $response) {

    // query photo
    $photoData = Media::find($id);
    // package the data
    $response['data'] = $photoData->values_for(array('id','title','resource','post_date'));
    $response['count'] = 1;
    // send the data
    echo json_encode($response);

});

/**
 * Add Gallery to Feed (id)
 *
 * post: /gallery/photo/{id}
 *
 */
$app->post("/photo/:id", function ($id) use ($app, $response) {

    $request = json_decode($app->request()->getBody());

    // Validate feed id
    if($id == $request->account){

        // create the post
        $event = new Gallery();
        $event->title = $request->title;
        $event->content = $request->content;
        $event->post_date = $request->post_date->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','resource','post_date'));
        $response['message'] = "post saved";
    }
    else{
        $response['status'] = "error";
        $response['message'] = "cancled";
    }

    // confirmation
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Update Gallery to Feeds (id)
 *
 * put: /gallery/photo/{id}
 *
 */
$app->put("/photo/:id", function ($id) use ($app) {

    $request = json_decode($app->request()->getBody());

    // Validate photo id
    if($id == $request->account){

        // find the photo
        $event = Media::find($request->id); // TODO: Need Validation Here
        // Update and Save Values
        $event->title = $request->title;
        $event->content = $request->content;
        $event->post_date = $request->post_date->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','resource','post_date'));
        $response['message'] = "post saved";
    }
    else{
        $response['status'] = "error";
        $response['message'] = "cancled";
    }

    // confirmation
    echo json_encode($response);



});

/**
 * Delete Gallery from Feeds (id)
 *
 * delete: /gallery/photo/{id}
 *
 */
$app->delete("/photo/:id", function ($id) use ($app) {


});

/**
 * Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();



/**
 * Data conversion utilites for photo events
 *
 *
 *
 */
 function arrayMapMedia($events){

    return array_map(create_function('$m','return $m->values_for(array(\'id\',\'title\',\'resource\',\'post_date\'));'),$events);

 }
