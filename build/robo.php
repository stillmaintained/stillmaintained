<?php

/**
 * @todo remove after fix is merged
 * @see https://github.com/Codegyre/Robo/pull/25
 */
error_reporting(0);

/**
 * Hack-ish way of getting Robo\Task\ExecTask autoloaded.
 * @see https://github.com/Codegyre/Robo/pull/21#issuecomment-42258799
 */
trait_exists('Robo\Task\Exec', true);

use App\Build\Command\Cleaner;
use Cake\Error\NotImplementedException;
use Robo\Output;
use Robo\Result;
use Robo\Task\Codeception;
use Robo\Task\Composer;
use Robo\Task\Exec;
use Robo\Task\FileSystem;
use Robo\Task\Watch;
use Robot\Command\Bumper;
use Robot\Command\Watcher;
use Robot\Task\Git;
use Robot\Task\SemVer;

class RoboFile {

	use Output;

/**
 * Tasks.
 */
	use Codeception;
	use Composer;
	use Exec;
	use FileSystem;
	use Git;
	use SemVer;
	use Watch;

/**
 * Commands.
 */
	use Bumper;
	use Cleaner;
	use Watcher;

	protected $server;

/**
 * @desc Creates all required directories.
 */
	public function platformTmp() {
		$paths = read('Path.tmpdirs', [
			LOGS,
			CACHE . 'models',
			CACHE . 'persistent',
			CACHE . 'views',
			TMP . 'tests',
		]);

		foreach ($paths as $path) {
			$command = 'mkdir -p ' . $path;
			$message = 'Failed to create ' . $path;
			if ($this->taskExec($command)->run()->wasSuccessful()) {
				$message = 'Created ' . $path;
			}
			$this->say($message);
		}
	}

/**
 * @desc Reset's platform application skeleton.
 */
	public function platformReset() {
		$this->cleanTmp();

		$this->taskReplaceInFile('composer.json')
			->regex('/"version": "[^\"]*"/')
			->to('"version": "0.0.0"')
			->run();

		$this->taskWriteToFile('VERSION')
			->line('v0.0.0')
			->run();

		$semVer = $this->taskSemVer();
		$this->taskWriteToFile('.semver')
			->line(sprintf($semVer::SEMVER, 0, 0, 0, null, null))
			->run();
	}

/**
 * @desc Configure the platform application.
 * @todo app configuration
 * @todo db configuration
 */
	public function platformConfig() {
		throw new NotImplementedException('To be implemented for (or before) 0.3');
	}

/**
 * @desc Starts development environment.
 */
	public function platformStart() {
		return $this->taskServer(read('Server.port'), 8765)->run();
	}

/**
 * @desc Runs all acceptance, functional and unit tests.
 */
	public function test() {
		$this->codeceptRunner();
	}

/**
 * @desc Runs all acceptance tests.
 */
	public function testAcceptance() {
		return $this->codeceptRunner('acceptance');
	}

/**
 * @desc Runs all functional tests.
 */
	public function testFunctional() {
		return $this->codeceptRunner('functional');
	}

/**
 * @desc Runs all unit tests.
 */
	public function testUnit() {
		return $this->codeceptRunner('unit');
	}

/**
 * Replaces `Robo\Task\PhpServer::taskServer` to allow for extra configuration.
 *
 * @return Robo\Task\ExecTask
 */
	protected function taskServer($port) {
		$command = sprintf(
			'php -S %s:%s -t %s',
			read('Server.domain', 'localhost'),
			$port,
			read('Server.webroot', 'webroot')
		);

		$task = new Robo\Task\ExecTask($command);
		$this->runningCommands[$port] = $task;
		return $task;
	}

	protected function codeceptRunner($suite = null) {
		if (in_array($suite, [null, 'acceptance']) && empty($this->runningCommands[8378])) {
			$this->taskServer(8378)->background()->run();
		}

		$task = $this->taskCodecept('vendor/bin/codecept');

		if ($suite) {
			$task->suite($suite);
		}

		return $task->option('steps')->run();
	}

}
