<?php

namespace App\Model\Table;

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

    public function touch($data)
    {
        $entity = ['username' => $data['name']];
        $query = $this->find()->where($entity);

        $user = $query->first();

        if (!$user) {
            if (!empty($data['info']['email'])) {
                $entity['email'] = $data['info']['email'];
            }

            $this->save($this->newEntity($entity));
            $user = $query->first();
        }

        return $user;
    }
}
