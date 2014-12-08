<?php
// This is global bootstrap for autoloading
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'start.php';
require '_helpers' . DS . 'Cake3' . DS . 'FixtureInjector.php';
require '_helpers' . DS . 'Cake3' . DS . 'FixtureManager.php';

disableDebug();
