<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;

Plugin::load('AssetCompress', ['bootstrap' => true]);
Plugin::load('Migrations');

if (Configure::read('debug')) {
    Plugin::load('Gourmet/Whoops');
}
