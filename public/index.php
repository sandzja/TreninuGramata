<?php

// Set the initial include_path. You may need to change this to ensure that
// Zend Framework is in the include_path; additionally, for performance
// reasons, it's best to move this to your web server configuration or php.ini
// for production.

$path = '/var/www/blog.broka.lv/zend/library/';
set_include_path('.'  . PATH_SEPARATOR .$path);

$path = '/var/www/blog.broka.lv/doctirne/DoctrineORM-2.1.6';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../library'),
    realpath(dirname(__FILE__) . '/../..'),
    get_include_path(),
)));

require_once 'location.php';
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
$application->run();
