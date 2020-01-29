<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('max_execution_time ', 300);
ini_set('memory_limit', '1G');
set_time_limit(300);

ob_implicit_flush(1);

define('TIME', microtime(true));
define('MEMORY', memory_get_usage());

header('Content-Type: text/html; charset=utf-8');

/** @var array $config */
(file_exists(dirname(__FILE__) . '/config.php')) ?: exit('Could not load config file!');
$config = require_once(dirname(__FILE__) . '/config.php');

(file_exists(dirname(__FILE__) . '/lib/app.php')) ?: exit('Could not load App file!');
require_once(dirname(__FILE__) . '/lib/app.php');
(file_exists(dirname(__FILE__) . '/lib/simple_html_dom.php')) ?: exit('Could not load Simple HTML DOM library!');
require_once(dirname(__FILE__) . '/lib/simple_html_dom.php');

/** @var App $app */
$app = new App($config);

require_once(dirname(__FILE__) . '/functions.php');

switch(isset($_GET['action'])) {
    case isset($_GET['action']):
    	(isset($_GET['action']) && function_exists($_GET['action'])) ? $_GET['action']($app) : $app->message('Введите необходимые GET-параметры!');
    	break;
}