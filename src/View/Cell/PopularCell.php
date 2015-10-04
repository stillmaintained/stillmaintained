<?php

namespace App\View\Cell;

use Cake\View\Cell;

class PopularCell extends Cell
{

    public function display()
    {
        $this->loadModel('Projects');
        $popular = $this->Projects->find('top')
            ->find('visible')
            ->find('popular')
            ->toArray();

        $this->set(compact('popular'));
    }
}
