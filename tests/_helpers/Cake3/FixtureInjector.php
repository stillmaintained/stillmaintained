<?php

// use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\Event\SuiteEvent;
use Codeception\Event\TestEvent;
use Codeception\Platform\Extension;

class FixtureInjector extends Extension {

    public static $events = [
        'suite.before' => 'startTestSuite',
        'suite.after' => 'endTestSuite',
        'test.start' => 'startTest',
        'test.end' => 'endTest',
    ];

    protected $_fixtureManager;

    protected $_first;

    public function __construct() {
        $this->_fixtureManager = new FixtureManager();
        $this->_fixtureManager->shutdown();
    }

    public function startTestSuite(SuiteEvent $event) {
        if (empty($this->_first)) {
            $this->_first = $event->getSuite();
        }
    }

    public function endTestSuite(SuiteEvent $event) {
        if ($this->_first === $event->getSuite()) {
            $this->_fixtureManager->shutdown();
        }
    }

    public function startTest(TestEvent $event) {
        $event->getTest()->fixtureManager = $this->_fixtureManager;
        if ($event->getTest() instanceof PHPUnit_Framework_TestCase) {
            $this->_fixtureManager->fixturize($event->getTest());
            $this->_fixtureManager->load($event->getTest());
        }
    }

    public function endTest(TestEvent $event, $time) {
        if ($event->getTest() instanceof PHPUnit_Framework_TestCase) {
            $this->_fixtureManager->unload($event->getTest());
        }
    }

    protected function _loadFixtures($test) {
        if (empty($test->fixtures)) {
            return;
        }

        $fixtures = $test->fixtures;
        if (empty($fixtures) || !$test->autoFixtures) {
            return;
        }

        $dbs = [];
        foreach ($fixtures as $f) {
            if (!empty($this->_loaded[$f])) {
                $fixture = $this->_loaded[$f];
                $dbs[$fixture->connection][$f] = $fixture;
            }
        }
        try {
            foreach ($dbs as $db => $fixtures) {
                $db = ConnectionManager::get($fixture->connection, false);
                $db->transactional(function($db) use ($fixtures, $test) {
                    foreach ($fixtures as $fixture) {
                        if (!in_array($db->configName(), (array)$fixture->created)) {
                            $this->_setupTable($fixture, $db, $test->dropTables);
                        }
                        if (!$test->dropTables) {
                            $fixture->truncate($db);
                        }
                        $fixture->insert($db);
                    }
                });
            }
        } catch (\PDOException $e) {
            $msg = sprintf('Unable to insert fixtures for "%s" test case. %s', get_class($test), $e->getMessage());
            throw new Error\Exception($msg);
        }
    }

}
