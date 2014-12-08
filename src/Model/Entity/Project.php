<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Project extends Entity {

	protected function _getFullNameLength() {
		return strlen($this->_getFullName());
	}

	protected function _getFullname() {
		return sprintf(
			'%s/%s',
			$this->_properties['username'],
			$this->_properties['name']
		);
	}

	protected function _getLink() {
		return '/' . $this->_getFullname();
	}

	protected function _getSource() {
		return sprintf(
			'https://%s.com%s',
			$this->_properties['provider'],
			$this->_getLink()
		);
	}

	protected function _setState($state) {
		$this->visible = $state != 'hide';
		return $state;
	}

}
