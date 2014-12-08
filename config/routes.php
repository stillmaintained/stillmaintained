<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Network\Request;
use Cake\Routing\Router;

Router::scope('/', function($routes) {

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	$routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

	$routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
	$routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);


	$extensions = (array) Configure::read('App.extensions');
	$regex = '[a-zA-Z0-9\-_]{1,20}';
	$routes->connect('/:username/edit', ['controller' => 'Projects', 'action' => 'edit'], ['username' => $regex]);

	$routes->extensions($extensions);
	foreach ($extensions as $ext) {
		Request::addDetector($ext, function($request) use ($ext) {
			return (!empty($request->params['_ext']) && $ext == $request->params['_ext']);
		});
	}
	$routes->connect('/search', ['controller' => 'Projects', 'action' => 'index']);
	$routes->connect('/:username/:project', ['controller' => 'Projects', 'action' => 'show'], ['username' => $regex, 'project' => $regex]);
	$routes->connect('/:username', ['controller' => 'Projects'], ['username' => $regex]);
/**
 * Connect a route for the index action of any controller.
 * And a more general catch all route for any action.
 *
 * You can remove these routes once you've connected the
 * routes you want in your application.
 */
	$routes->fallbacks();

});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
