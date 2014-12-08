<?php

use Cake\Core\Configure;

/**
 * Configure basic information about the application.
 *
 * - namespace - The namespace to find app classes under.
 * - encoding - The encoding used for HTML + database connections.
 * - timezone -
 * - base - The base directory the app resides in. If false this
 *   will be auto detected.
 * - dir - Name of app directory.
 * - webroot - The webroot directory.
 * - www_root - The file path to webroot.
 * - baseUrl - To configure CakePHP *not* to use mod_rewrite and to
 *   use CakePHP pretty URLs, remove these .htaccess
 *   files:
 *      /.htaccess
 *      /webroot/.htaccess
 *   And uncomment the baseUrl key below.
 * - imageBaseUrl - Web path to the public images directory under webroot.
 * - cssBaseUrl - Web path to the public css directory under webroot.
 * - jsBaseUrl - Web path to the public js directory under webroot.
 * - paths - Configure paths for non class based resources. Supports the `plugins` and `templates`
 *   subkeys, which allow the definition of paths for plugins and view templates respectively.
 */

Configure::write('App', [
	'title' => 'Still Maintained',
	'author' => 'Still Maintained',
	'namespace' => 'App',
	'encoding' => 'UTF-8',
	'timezone' => 'UTC',
	'base' => false,
	'dir' => APP_DIR,
	'webroot' => 'webroot',
	'www_root' => WWW_ROOT,
	// 'baseUrl' => env('SCRIPT_NAME'),
	'fullBaseUrl' => false,
	'imageBaseUrl' => 'img' . DS,
	'cssBaseUrl' => 'css' . DS,
	'jsBaseUrl' => 'js' . DS,
	'paths' => [
		'plugins' => [ROOT . DS . 'plugins' . DS],
		'templates' => [APP . 'Template' . DS],
		'node_modules' => ['/usr/local/lib/node_modules/', ROOT . DS . 'node_modules' . DS],
	],
	'extensions' => ['json', 'svg'],
]);
