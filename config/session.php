<?php

use Cake\Core\Configure;

/**
 *
 * Session configuration.
 *
 * Contains an array of settings to use for session configuration. The defaults key is
 * used to define a default preset to use for sessions, any settings declared here will override
 * the settings of the default config.
 *
 * ## Options
 *
 * - `cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `checkAgent` - Do you want the user agent to be checked when starting sessions? You might want to set the
 *    value to false, when dealing with older versions of IE, Chrome Frame or certain web-browsing devices and AJAX
 * - `defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `handler` - Can be used to enable a custom session handler.  Expects an array of of callables,
 *    that can be used with `session_save_handler`.  Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `autoRegenerate` - Enabling this setting, turns on automatic renewal of sessions, and
 *    sessionids that change frequently.
 * - `requestCountdown` - Number of requests that can occur during a session time
 *    without the session being renewed. Only used when config value `autoRegenerate`
 *    is set to true. Default to 10.
 * - `ini` - An associative array of additional ini values to set.
 *
 * The built in defaults are:
 *
 * - 'php' - Uses settings defined in your php.ini.
 * - 'cake' - Saves session files in CakePHP's /tmp directory.
 * - 'database' - Uses CakePHP's database sessions.
 * - 'cache' - Use the Cache class to save sessions.
 *
 * To define a custom session handler, save it at /app/Network/Session/<name>.php.
 * Make sure the class implements PHP's `SessionHandlerInterface` and se
 * Session.handler to <name>
 *
 * To use database sessions, run the App/Config/Schema/sessions.php schema using
 * the cake shell command: cake schema create Sessions
 */

Configure::write('Session', [
    'defaults' => 'cache',
    'timeout' => 60,
]);
