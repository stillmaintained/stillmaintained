<?php

/**
 * Start timer.
 */
	define('TIME_START', microtime(true));

/**
 * Define paths' constants.
 */
	require 'paths.php';

/**
 * Register autoloaders.
 */
	require 'autoload.php';

	try {
		$dotenv = (new josegonzalez\Dotenv\Loader(TMP . '.env'))
			->parse()
			->expect(
				'DB1_HOST',
				'DB1_USER',
				'DB1_PASS',
				'DB1_NAME',
				'GITHUB_CLIENT_ID',
				'GITHUB_CLIENT_SECRET'
			)
			->toEnv();
	} catch (RuntimeException $e) {
		die($e->getMessage());
	} catch (Exception $e) {
		// do nothing (env vars setup on server)
	}

/**
 * Define extra functions.
 */
	require CAKE . 'basics.php';
	require 'functions.php';

/**
 * Set the application's debug mode.
 */
	Cake\Core\Configure::write('debug', file_exists(ROOT . DS . '.debug'));

/**
 * Set the application's default configurations.
 */
	require CONFIG . 'application.php';
	require CONFIG . 'paths.php';
	require CONFIG . 'security.php';
	require CONFIG . 'acl.php';
	require CONFIG . 'session.php';
	require CONFIG . 'dispatcher.php';

/**
 * Engines configuration.
 */
	require CONFIG . 'database.php';
	require CONFIG . 'error.php';
	require CONFIG . 'cache.php';
	require CONFIG . 'email.php';
	require CONFIG . 'log.php';
	require CONFIG . 'opauth.php';
/**
 * Load plugins.
 */
	require CONFIG . 'plugin.php';

/**
 *
 */
	require 'init.php';
