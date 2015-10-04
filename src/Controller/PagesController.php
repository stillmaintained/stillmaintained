<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Error;
use Cake\Event\Event;
use Cake\Utility\Inflector;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 */
class PagesController extends AppController
{

    public function beforeFilter(Event $event)
    {
        $this->Auth->allow('display');
    }

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws Cake\Error\NotFoundException When the view file could not be found
 *  or Cake\Error\MissingViewException in debug mode.
 */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title_for_layout'));

        try {
            $this->render(implode('/', $path));
        } catch (Error\MissingViewException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new Error\NotFoundException();
        }
    }
}
