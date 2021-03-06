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

header('X-Powered-By: Mobile Gateways');

require $_SERVER['DOCUMENT_ROOT'].'/system/ActiveRecord.php';
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory($_SERVER['DOCUMENT_ROOT'].'/models');
    $cfg->set_connections(array(
        'development' => 'mysql://root:acg100199@localhost/services'
    ));
});


class MasterView extends \Slim\View
{
    public $errors = array();


    public function __construct()
    {
        // Grab User Account info....
        $accountData = Account::find('first',array('conditions'=>array('user_id = ?', $this->userId())));
        // grab only what we need
        $this->setData('account', $accountData->values_for(array('id','name','calendar_id','gallery_id','feed_id','ads_id','isactive')));
    }

    /** Render Page
     *
     */
    public function render($template)
    {

        $this->setData('childView', $template);
        return parent::render('master.php');

    }
    /** Render Content Page
     *
     */
    public function partial($template, $data = array())
    {
        extract($data);

        require $this->getTemplatesDirectory().'/'.$template;

    }

    public function user()
    {
        return $_SESSION['user']['username'];
    }

    /** user
     *
     *
     *
     */
    public function userId()
    {
        return $_SESSION['user']['id'];
    }

}
