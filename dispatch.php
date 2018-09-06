<?php
define('BASE_ROOT', __DIR__);
require_once BASE_ROOT . '/vendor/autoload.php'; // set up autoloading

date_default_timezone_set('UTC');
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING)); // We don't want E_STRICT or E_NOTICE. E_WARNING was in here on production, so I'm adding it here otherwise LAB_CHAN gets spammed..
putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1'); // make sure dns resolver doesnt take too much time for php

$app = \App\Dispatcher::app();

$di = $app->getContainer();

// this will take care of internal proxy for file_get_contents however it is recommended to use \Tentacles\Utilities\Curl
if($di['config']['proxy']) {
    stream_context_set_default([
        'http' => [
            'proxy' => $di['config']['proxy']
        ]
    ]);
}

\Tracy\Debugger::enable($di['config']['mode'] == 'development' ? \Tracy\Debugger::DEVELOPMENT : \Tracy\Debugger::PRODUCTION, BASE_ROOT . '/logs');
\Tracy\Debugger::$maxDepth = 5;
\Tracy\Debugger::$maxLength = 250;
\Tracy\Debugger::$logSeverity = E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING);

// add middleware
// note that the order is important; middleware gets executed as an onion, so
// the first middleware that gets added gets executed last as the request comes
// in and first as the response comes out.
$app->add(new \App\Middleware\UpdateSession($di));
$app->add(new \App\Middleware\Session($di));
$app->add(new \App\Middleware\RouteHelper($di));
$app->add(new \App\Middleware\Language($di));
$app->add(new \App\Middleware\AllowOrigin($di));
if ($di['config']['mode'] == 'development') {
    $app->add(new \RunTracy\Middlewares\TracyMiddleware($app));
}

$app->run();

//$di['handler']->write(session_id(), session_encode()); // TODO: Fix DB sessions?
//session_write_close();
bdump($di['db'], 'Database');
