<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class CredentialsTable extends Table {

    public function initialize(array $config) {
        $this->addBehavior('Timestamp');

		$this->belongsTo('Users');
    }

	public function touch($user, $data) {
		$email = $user->email;
		$provider = $data['provider'];
		$query = $this->find()->where(compact('email', 'provider'));

		$cred = $query->first();
		if (empty($cred)) {
			$cred = $this->newEntity([
				'user_id' => $user->id
			] + compact('email', 'provider'));
		}

		$cred->token = $data['credentials']['token'];
		$this->save($cred);
	}

}
