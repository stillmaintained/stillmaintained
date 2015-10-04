<?php

namespace App\Test\Codeception\Module;

use Cake\Controller\Error\MissingControllerException;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Codeception\Lib\Framework;
use Codeception\Lib\Interfaces\ActiveRecord;
use Codeception\TestCase;

class Cake3 extends Framework// implements ActiveRecord
{

    protected $configure;

    public function _initialize()
    {

    }

    public function _before(TestCase $test)
    {
        $this->client = new Cake3\Client();
        if (empty($this->configure)) {
            $this->configure = Configure::read();
        }
        if (class_exists('Cake\Routing\Router', false)) {
            Router::reload();
        }
    }

    public function _after(TestCase $test)
    {
        if (!empty($this->configure)) {
            Configure::clear();
            Configure::write($this->configure);
        }
    }

    public function expectAnExceptionOnPage($uri, $exceptionClass = '\Exception')
    {
        try {
            $this->amOnPage($uri);
        } catch (\Exception $e) {
            if (!($e instanceof $exceptionClass)) {
                throw $e;
            }
            return;
        }
        $this->fail("A $exceptionClass was expected to be thrown");
    }
}
