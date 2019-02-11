<?php
/**
 * Подключаем Modx
 */
// require_once './lib/modx.php';
// $uModx = new uModx();

/**
 * Подключаем Битрикс
 */
// require_once './lib/bitrix.php';
// $uBitrix = new uBitrix();

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
 * Настройки
 */
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

function example() {
    global $app;
    
    
}