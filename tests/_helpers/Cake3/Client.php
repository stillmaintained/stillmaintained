<?php

namespace App\Test\Codeception\Module\Cake3;

use Cake\Controller\Error\MissingControllerException;
use Cake\Core\Configure;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Codeception\TestCase;
use Symfony\Component\BrowserKit\Client as BKClient;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

class_exists('Cake\TestSuite\ControllerTestCase', true);
class_exists('Cake\TestSuite\StubControllerFilter', true);

class Client extends BKClient
{

	protected $mockObjectGenerator;

	protected function getMock($class, $methods = [], $args = [])
	{
		if (null === $this->mockObjectGenerator) {
			$this->mockObjectGenerator = new \PHPUnit_Framework_MockObject_Generator;
		}

		return $this->mockObjectGenerator->getMock($class, $methods, $args);
	}

	protected function filterRequest(Request $request)
	{
		$requestData = [
			'url' => preg_replace('/^https?:\/\/[a-z0-9\-\.]+/', '', $request->getUri()),
			'cookies' => $request->getCookies(),
			'query' => $request->getParameters()
		];

		return $this->getMock('Cake\Network\Request', null, [$requestData]);
	}

	protected function filterResponse($response)
	{
		return new Response($response->body(), $response->statusCode(), $response->header());
	}

	protected function doRequest($request)
	{
		Router::reload();
		$request->addParams(Router::parse($request->url));

		$plugin = empty($request->params['plugin']) ? '' : Inflector::camelize($request->params['plugin']) . '.';

		$StubFilter = new \Cake\TestSuite\StubControllerFilter();
		$Dispatcher = DispatcherFactory::create();
		$Dispatcher->addFilter($StubFilter);

		$StubFilter->response = $this->getMock('Cake\Network\Response', array('send', 'stop'));
		$StubFilter->testController = $this->generate($plugin . Inflector::camelize($request->params['controller']));

		$Dispatcher->dispatch($request, $StubFilter->response);
		return $StubFilter->testController->response;
	}

	protected function generate($controller)
	{
		$classname = App::classname($controller, 'Controller', 'Controller');
		if (!$classname) {
			list($plugin, $controller) = pluginSplit($controller);
			throw new MissingControllerException(array(
				'class' => $controller . 'Controller',
				'plugin' => $plugin
			));
		}

		list(, $controllerName) = namespaceSplit($classname);
		$name = substr($controllerName, 0, -10);

		$request = $this->getMock('Cake\Network\Request');
		$response = $this->getMock('Cake\Network\Response', array('_sendHeader', 'stop'));
		$controller = $this->getMock($classname, null, array($request, $response, $name));

		$controller->constructClasses();

		return $controller;
	}

}
