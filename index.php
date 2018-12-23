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

require_once './lib/app.php';
require_once './lib/simple_html_dom.php';

require_once './functions.php';

switch(isset($_GET['action'])) {
    case isset($_GET['action']):
    if (isset($_GET['action']) && function_exists($_GET['action'])) {
        $_GET['action']();
    } else {
        echo '<div>Введите необходимые GET-параметры!</div>';
    }
    break;
}