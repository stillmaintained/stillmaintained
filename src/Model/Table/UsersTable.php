<?php

namespace App\Model\Table;

use App\Provider\GithubProvider as Provider;
use Cake\Event\Event;
use Cake\ORM\Table;

class UsersTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');

        $this->hasMany('Credentials', ['dependent' => true]);

        $this->belongsToMany('Projects');
        $this->belongsToMany('Organizations');
    }

    public function newUser(Event $event, Provider $provider, $data)
    {
        $entity = $this->newEntity($data);
        $this->save($entity);
        return $entity->toArray();
    }
}
