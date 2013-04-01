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

$app = new \Slim\Slim(array('session.handler' => null, 'templates.path'=>$_SERVER['DOCUMENT_ROOT'].'views/templates/'));
$app->add(new \Slim\Middleware\SessionCookie());

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/a/login');
        }
    };
};


require $_SERVER['DOCUMENT_ROOT'].'views/AccountView.php';
$app->view(new AccountView());

/**
 * Settings Page - Dashboard
 *
 *
 *
 */
$app->get('/settings', $authenticate($app), function () use($app) {

    // Page Meta
    $meta = array();
    $data = array('meta'=>$meta);
    $app->render('page/account/settings.php', $data);


});

/**
 * Settings Page - Dashboard
 *
 *
 *
 */
$app->get('/billing', $authenticate($app), function () use($app) {

    // Page Meta
    $meta = array();
    $data = array('meta'=>$meta);
    $app->render('page/account/billing.php', $data);


});

/**
 * Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
