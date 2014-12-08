<?php

namespace App\Test;

$I = new WebPerson($scenario);
$I->wantTo('view an unexisting page');
$I->amOnPage('/an_unexisting_page');
$I->seePageNotFound();
// $I->see('Error');
// $I->see('Not Found');
// $I->see('The requested address <strong>\'/an_unexisting_page\'</strong> was not found on this server.');
