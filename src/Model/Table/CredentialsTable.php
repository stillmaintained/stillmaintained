<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class CredentialsTable extends Table {

	public function initialize(array $config) {
		$this->addBehavior('Timestamp');

		$this->belongsTo('Users');
	}

	public function touch($user, $data) {
		$entity = ['user_id' => $user->id, 'provider' => $data['provider']];
		$query = $this->find()->where($entity);

		$cred = $query->first();

		if (empty($cred)) {
			$cred = $this->newEntity($entity);
		}

		$cred->token = $data['credentials']['token'];
		$this->save($cred);
	}

}
