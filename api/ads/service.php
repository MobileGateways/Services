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
    if ($app->request()->params('apiKey') !== "7406f3491660b7b35b85f5381e511712") {
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
 * get: /ads/
 *
 */
$app->get('/', function () use($app, $response)  {

    $response['message'] = 'Mobile Gateways Ads Services v1.0';
    echo json_encode($response);

});


/**
 * Fetch Ads - returns recent ads (next 30 days max??)
 *
 * get: /ads/{id}
 *
 */
$app->get("/:id", function ($id) use ($app, $response) {

    // get date
    $today = new DateTime('GMT');

    // query copy
    $copyData = Ads::find('all', array('order'=>'expire_date asc', 'conditions' => array('account = ? AND ? BETWEEN post_date AND expire_date', $id, $today->format("Y-m-d"))));
    // package the data
    $response['data'] = arrayMapPost($copyData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));

/**
 * Fetch Ads - returns copy for month
 *
 * get: /ads/{id}/{month}
 *
 */
$app->get("/:id/:month", function ($id, $month) use ($app, $response) {

    // query ads
    $copyData = Ads::find('all', array('order'=>'post_date asc', 'conditions' => array('account = ? AND MONTH(post_date) = ?', $id, $month)));
    // package the data
    $response['data'] = arrayMapPost($copyData);
    $response['count'] = count($response['data']);
    // send the data
    echo json_encode($response);

})->conditions(array('id' => '[0-9a-z]{32}'));



/* ADS FEEDS */


/**
 * Get Ads Event
 *
 * get: /ads/copy/{id}
 *
 */
$app->get("/copy/:id", function ($id) use ($app, $response) {

    // query copy
    $copyData = Ads::find($id);
    // package the data
    $response['data'] = $copyData->values_for(array('id','title','content','post_date','expire_date'));
    $response['count'] = 1;
    // send the data
    echo json_encode($response);

});

/**
 * Add Ads to Feed (id)
 *
 * post: /ads/copy/{id}
 *
 */
$app->post("/copy/:id", function ($id) use ($app, $response) {

    $request = json_decode($app->request()->getBody());

    // Validate feed id
    if($id == $request->account){

        // create the post
        $event = new Ads();
        $event->title = $request->title;
        $event->content = $request->content;
        $event->post_date = $request->post_date->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','content','post_date','expire_date'));
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
 * Update Ads to Feeds (id)
 *
 * put: /ads/copy/{id}
 *
 */
$app->put("/copy/:id", function ($id) use ($app) {

    $request = json_decode($app->request()->getBody());

    // Validate copy id
    if($id == $request->account){

        // find the copy
        $event = Ads::find($request->id); // TODO: Need Validation Here
        // Update and Save Values
        $event->title = $request->title;
        $event->content = $request->content;
        $event->post_date = $request->post_date->date;
        $event->account = $request->account;
        $event->save();
        // package the data
        $response['data'] = $event->values_for(array('id','title','content','post_date','expire_date'));
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
 * Delete Ad from Feeds (id)
 *
 * delete: /ads/copy/{id}
 *
 */
$app->delete("/copy/:id", function ($id) use ($app) {


});

/**
 * Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();



/**
 * Data conversion utilites for copy events
 *
 *
 */
 function arrayMapPost($events){

    return array_map(create_function('$m','return $m->values_for(array(\'id\',\'title\',\'content\',\'post_date\',\'expire_date\'));'),$events);

 }
