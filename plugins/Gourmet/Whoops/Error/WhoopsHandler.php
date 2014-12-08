<?php

namespace Gourmet\Whoops\Error;

use Cake\Core\Configure;
use Cake\Error\ErrorHandler;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class WhoopsHandler extends ErrorHandler
{
	protected $_whoops;

	public function getWhoopsInstance()
	{
		if (empty($this->_whoops)) {
			$this->_whoops = new Run();
		}
		return $this->_whoops;
	}

	protected function _displayError($error, $debug)
	{
		if ($debug) {
			$whoops = $this->getWhoopsInstance();
			$whoops->pushHandler(new PrettyPageHandler());
			$whoops->handleError($error['level'], $error['description'], $error['file'], $error['line']);
		} else {
			parent::_displayError($error, $debug);
		}
	}

	protected function _displayException($exception)
	{
		if (Configure::read('debug')) {
			$whoops = $this->getWhoopsInstance();
			$whoops->pushHandler(new PrettyPageHandler());
			$whoops->handleException($exception);
		} else {
			parent::_displayException($exception);
		}
	}
}
