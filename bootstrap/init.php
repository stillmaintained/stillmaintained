<?php

use Cake\Core\App;
use Cake\Core\Configure;

/**
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 */
	date_default_timezone_set(Configure::read('App.timezone'));

/**
 * Configure the mbstring extension to use the correct encoding.
 */
	mb_internal_encoding(Configure::read('App.encoding'));

/**
 * Set the full base url.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
	if (!Configure::read('App.fullBaseUrl')) {
		$s = null;
		if (env('HTTPS')) {
			$s = 's';
		}

		$httpHost = env('HTTP_HOST');
		if (isset($httpHost)) {
			Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
		}
		unset($httpHost, $s);
	}
