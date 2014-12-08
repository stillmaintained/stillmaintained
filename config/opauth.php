<?php

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Opauth configuration.
 */
Configure::write('Opauth', Hash::merge([
	'debug' => (Configure::read('debug') !== 0),
	'redirect' => Router::url(['controller' => 'Users', 'action' => 'login']),
	'Strategy' => [
		'Github' => [
			'client_id' => $_ENV['GITHUB_CLIENT_ID'],
			'client_secret' => $_ENV['GITHUB_CLIENT_SECRET']
		],
	]
], Configure::consume('Opauth')));

/**
 * Opauth routes.
 */
Router::scope('/auth', ['controller' => 'Opauth'], function($routes) {
	$routes->connect('/*', ['action' => 'index']);
});
