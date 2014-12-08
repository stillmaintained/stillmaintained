<?php

/**
 * Composer's vendors' path.
 */
	if (!defined('VENDOR')) {
		define('VENDOR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
	}

/**
* Composer's autoloader.
*/
	require VENDOR . 'autoload.php';
