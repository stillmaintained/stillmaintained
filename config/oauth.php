<?php

Cake\Core\Configure::write('Muffin/OAuth2', [
    'providers' => [
        'github' => [
            'className' => 'App\Provider\GithubProvider',
            'options' => [
                'clientId' => env('GITHUB_CLIENT_ID'),
                'clientSecret' => env('GITHUB_CLIENT_SECRET'),
            ],
            'mapFields' => [
                'username' => 'login',
            ],
        ],
    ],
]);

$eventManager = Cake\Event\EventManager::instance();
$eventManager->on('Muffin/OAuth2.newUser', [\Cake\ORM\TableRegistry::get('Users'), 'newUser']);
$eventManager->on('Muffin/OAuth2.afterIdentify', [\Cake\ORM\TableRegistry::get('Projects'), 'syncProjects']);
