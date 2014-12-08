<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Opauth\Opauth\Opauth;

class OpauthController extends AppController {

    public function beforeFilter(Event $event) {

        if (is_object($this->Auth) && method_exists($this->Auth, 'allow')) {
            $this->Auth->allow();
        }

        if (is_object($this->Security)) {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }

    }

    public function index() {
        $redirect = read('Opauth.redirect', '/');

        if ($this->Auth->user()) {
            return $this->redirect($redirect);
        }

        $opauth = new Opauth(read('Opauth'));

        try {
            $response = $opauth->run();
        } catch (OpauthException $e) {
            echo 'Authentication error: ' . $e->getMessage();
        }

        $data = (array) $response;

        $this->loadModel('Users');
        $user = $this->Users->touch($data);
        $this->Users->Credentials->touch($user, $data);
        $this->Users->Projects->sync($user, $data);
        $this->Auth->setUser($user->toArray());

        $this->redirect([
            'controller' => 'Projects',
            'action' => 'edit',
            'username' => $this->Auth->user('username')
        ]);
    }

}
