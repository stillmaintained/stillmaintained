<?php

use Cake\Core\Configure;
use Cake\Utility\Text;

if (!function_exists('consume')) {
    function consume($key, $default = null)
    {
        $ret = $default;
        if (Configure::check($key)) {
            $ret = Configure::consume($key);
        }
        return $ret;
    }
}

if (!function_exists('read')) {
    function read($key, $default = null)
    {
        $ret = $default;
        if (Configure::check($key)) {
            $ret = Configure::read($key);
        }
        return $ret;
    }
}

if (!function_exists('enableDebug')) {
    function enableDebug()
    {
        $file = ROOT . DS . '.debug';
        if (!file_exists($file)) {
            exec('touch ' . $file);
        }
        Configure::write('debug', true);
    }
}

if (!function_exists('disableDebug')) {
    function disableDebug()
    {
        $file = ROOT . DS . '.debug';
        if (file_exists($file)) {
            exec('rm ' . $file);
        }
        Configure::write('debug', false);
    }
}

if (!function_exists('pathname')) {
    function pathname($command)
    {
        $ret = exec('which ' . $command);
        if (empty($ret)) {
            throw new \Exception($command . ': command not installed');
        }

        return $ret;
    }
}

if (!function_exists('___')) {
    function ___($str, $data, $options = [])
    {
        return Text::insert(__($str), $data, $options);
    }
}
