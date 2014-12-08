<?php

use Cake\Core\Configure;

/**
 * The classname and database used in CakePHP's
 * access control lists.
 */

Configure::write('Acl', [
	'database' => 'default',
	'classname' => 'DbAcl',
]);
