<?php

use Assetic\Filter\LessFilter;
use \Cake\Core\Configure;

Configure::write('Assetic', [
    'cssFilters' => [
        'less' => new LessFilter(pathname('node'), read('App.paths.node_modules')),
    ],
]);
