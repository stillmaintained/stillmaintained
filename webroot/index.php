<?php

// for built-in server
if (php_sapi_name() === 'cli-server') {
	$_SERVER['PHP_SELF'] = '/' . basename(__FILE__);

	$url = urldecode($_SERVER['REQUEST_URI']);
	$file = __DIR__ . $url;
	if (strpos($url, '..') === false && strpos($url, '.') !== false && is_file($file)) {
		return false;
	}
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'start.php';

use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\DispatcherFactory;

$dispatcher = DispatcherFactory::create();
$dispatcher->dispatch(
	Request::createFromGlobals(),
	new Response()
);
