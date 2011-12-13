<?php

define('APPLICATION_ENV', 'development');
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('DATA_PATH', BASE_PATH . '/data');


set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH . '/library'),
    realpath(BASE_PATH . '/library/HtmlPurifier'),
    realpath(BASE_PATH . '/tests'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();




$em = Application_Registry::getEm();

$tester = new Test_Model_ObservationServiceTest($em);
$tester->test();
