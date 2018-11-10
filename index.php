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

$app = new App([
    'new_domain' => $_SERVER['HTTP_HOST'],
    'old_domain' => '',
    'schema' => 'http',
    'folders' => [
        'cache' => 'cache',
        'files' => 'files'
    ],
    'files' => [
        'categories' => 'categories',
        'products' => 'products',
    ]
]);


/**
 * Подключаем Modx
 */
// require_once './lib/modx.php';
// $uModx = new uModx();


/**
 * Подключаем Битрикс
 */
// require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


/**
 * Подключаем uCoz
 */
// require_once './lib/uAPImodule.php'; //Так же в подключаемом файле необходимо прописать адрес сайта, к которому будем обращаться
// $uAPI = new Request(array(
//     'oauth_consumer_key' => '',
//     'oauth_consumer_secret' => '.',
//     'oauth_token' => '',
//     'oauth_token_secret' => ''
// ));

/**
 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 
 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 
 */

function getDefault() {
    global $app;

    echo '<div>Введите необходимые GET-параметры!</div>';
}

if (isset($_GET['go'])) { $go = $_GET['go']; } else { $go = 'default'; }

switch($go) {
    case "getFUNCTION":
    getFUNCTION();
    break;

    default:
    getDefault();
    break;
}