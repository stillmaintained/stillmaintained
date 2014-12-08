<?php

namespace App\Build\Command;

trait Cleaner {

/**
 * @desc Runs all the `clean:*` commands.
 */
	public function clean()	 {
		foreach (get_class_methods($this) as $method) {
			if ('clean' == $method || strpos($method, 'clean') !== 0) {
				continue;
			}
			$this->{$method}();
		}
	}

/**
 * @desc Cleans all temporary directories.
 */
	public function cleanTmp() {
		$this->say('Cleaning temporary directories...');
		$this->taskCleanDir(read('Path.tmpdirs', [
			LOGS,
			CACHE . 'models',
			CACHE . 'persistent',
			CACHE . 'views',
			TMP . 'tests',
		]))->run();
	}

}
