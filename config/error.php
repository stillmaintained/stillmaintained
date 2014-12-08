<?php

use Cake\Utility\Hash;

/**
 * Default error configuration.
 */
$errorConfig = consume('Error', []) + [
	'errorLevel' => E_ALL & ~E_DEPRECATED,
	'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
	'skipLog' => [],
	'log' => true,
	'trace' => true,
];

/**
 * Default error handler.
 */
$errorHandler = 'Cake\Error\ErrorHandler';

/**
 * Command-line interface error handler.
 */
if (php_sapi_name() === 'cli') {
	$errorHandler = 'Cake\Console\ConsoleErrorHandler';

/**
 * Whoops handler if available.
 */
} elseif (class_exists('Gourmet\Whoops\Error\WhoopsHandler')) {
	$errorHandler = 'Gourmet\Whoops\Error\WhoopsHandler';
}

/**
 * Register error and exception handlers.
 */
(new $errorHandler($errorConfig))->register();

/**
 * Clean up.
 */
unset($errorConfig, $errorHandler);
