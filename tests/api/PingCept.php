<?php

$I = new ApiTester($scenario);
$I->wantTo('ensure that service is up and running');
$I->sendGET('/ping');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseContains('pong');
