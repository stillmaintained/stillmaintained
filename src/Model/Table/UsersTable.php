<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class UsersTable extends Table {

	public function initialize(array $config) {
		$this->addBehavior('Timestamp');

		$this->hasMany('Credentials', ['dependent' => true]);

		$this->belongsToMany('Projects');
		$this->belongsToMany('Organizations');
	}

	public function touch($data) {
		$email = $data['info']['email'];
		$query = $this->find()->where(compact('email'));

		$user = $query->first();

		if (!$user) {
			$this->save($this->newEntity(compact('email') + ['username' => $data['name']]));
			$user = $query->first();
		}

		return $user;
	}

}
