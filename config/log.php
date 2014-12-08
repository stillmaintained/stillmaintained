<?php

use Cake\Log\Log;
use Cake\Utility\Hash;

$logDefaultConfig = [
	'className' => 'Cake\Log\Engine\FileLog',
	'file' => php_sapi_name() . '-debug',
	'levels' => []
];

$logConfigs = Hash::merge([
	'debug' => ['levels' => ['notice', 'info', 'debug']] + $logDefaultConfig,
	'error' => [
		'file' => php_sapi_name() . '-error',
		'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
	] + $logDefaultConfig
], consume('Log', []));

Log::config($logConfigs);

unset($logDefaultConfig, $logConfigs);
