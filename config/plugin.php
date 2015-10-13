<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;

Plugin::load('AssetCompress', ['bootstrap' => true]);
Plugin::load('Migrations');
Plugin::load('Muffin/OAuth2');

if (Configure::read('debug')) {
    Plugin::load('Gourmet/Whoops');
}
