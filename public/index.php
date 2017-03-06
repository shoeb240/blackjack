<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Register namespace "My" which contains custom plugin
require_once 'Zend/Loader/Autoloader.php';
$autoLoader = Zend_Loader_Autoloader::getInstance();
$autoLoader->registerNamespace('My_');


/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


// front controller instance
$front = Zend_Controller_Front::getInstance();

// Disable output buffering
$front->setParam('disableOutputBuffering', true);

// Set application wide confguration
Zend_Registry::set('CARD_NUM', 52);


// Register custom plugins 
$front->registerPlugin(new My_Plugin_Route());

$application->bootstrap()->run();