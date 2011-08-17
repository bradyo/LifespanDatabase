<?php

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'development');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../library/ZendFramework-1.10.8/library'),
    realpath(APPLICATION_PATH . '/../library/doctrine'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

