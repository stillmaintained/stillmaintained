<?php

namespace App\View\Cell;

use Cake\View\Cell;

class RecentCell extends Cell
{

    public function display()
    {
        $this->loadModel('Projects');
        $recent = $this->Projects->find('top')
            ->find('visible')
            ->find('recent')
            ->toArray();

        $this->set(compact('recent'));
    }
}
