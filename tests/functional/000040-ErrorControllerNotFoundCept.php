<?php

namespace App\Test;

$I = new TestPerson($scenario);
$I->wantTo('view an unexisting page');
$I->expectAnExceptionOnPage('/an_unexisting_page', '\Cake\Controller\Error\MissingControllerException');
