<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppController extends Controller
{

    public $components = [
        'Auth' => [
            'loginAction' => '/',
            'logoutRedirect' => '/',
        ],
        'Flash',
    ];

    public $helpers = [
        'AssetCompress.AssetCompress',
        'Flash',
        'Form' => [
            'templates' => [
                'inputContainer' => '<div class="form-group {{type}}{{required}}">{{content}}</div>',
                'inputContainerError' => '<div class="form-group {{type}}{{required}} error">{{content}}{{error}}</div>',
                'radioWrapper' => '<div class="radio">{{input}}{{label}}</div>',
            ]
        ],
        'Html',
    ];

    public function beforeRender(Event $event)
    {
        $this->set('user', $this->Auth->user());
    }
}
