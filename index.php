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

switch (mb_strtolower($config['cms'], 'utf-8')) {
    case 'bitrix':
        require_once(dirname(__FILE__) . '/lib/bitrix.php');
        /** @var uBitrix $app */
        $app = new uBitrix($config);
        break;
    case 'modx':
        require_once(dirname(__FILE__) . '/lib/modx.php');
        /** @var uModx $app */
        $app = new uModx($config);
        break;
    default:
        /** @var App $app */
        $app = new App($config);
};

(!isset($_GET['action'])) ? $app->message('Укажите GET-параметр "action"!') : (isset($_GET['action']) && !file_exists(dirname(__FILE__) . '/' . $_GET['action'] . '.php')) ? $app->message('PHP-файл соответсвующий GET-параметру "action=' . $_GET['action'] . '" отсутствует!') : include(dirname(__FILE__) . '/' . $_GET['action'] . '.php');