<?php

define('APPLICATION_PATH', '/home/kaeblab/projects/LifespanDatabase/application');
define('APPLICATION_ENV', 'production');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('/home/kaeblab/projects/LifespanDatabase/library/ZendFramework-1.11.6-minimal/library'),
    realpath('/home/kaeblab/projects/LifespanDatabase/library/Doctrine-1.2.4'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

