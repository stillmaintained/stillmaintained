<?php

use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

ConnectionManager::config(Hash::merge([
	'default' => [
		'className' => 'Cake\Database\Connection',
		'driver' => 'Cake\Database\Driver\Mysql',
		'persistent' => false,
		'host' => $_ENV['DB1_HOST'],
		'username' => $_ENV['DB1_USER'],
		'password' => $_ENV['DB1_PASS'],
		'database' => $_ENV['DB1_NAME'],
		'prefix' => false,
		'encoding' => strtolower(str_replace('-', '', read('App.encoding'))),
		'timezone' => read('App.timezone'),
		'quoteIdentifiers' => false
	],
	'test' => [
		'className' => 'Cake\Database\Connection',
		'driver' => 'Cake\Database\Driver\Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'username' => 'app',
		'password' => 'app',
		'database' => 'test_app',
		'prefix' => false,
		'encoding' => strtolower(str_replace('-', '', read('App.encoding'))),
		'timezone' => read('App.timezone'),
		'quoteIdentifiers' => false
	]
], consume('Datasources')));
