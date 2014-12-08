<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Credential extends Entity {

	protected function _setProvider($provider) {
		return strtolower($provider);
	}
	
}
