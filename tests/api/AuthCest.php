<?php

class AuthCest
{
    public function _before(ApiTester $I)
    {
    }

    public function tryToTest(ApiTester $I)
    {
        $user_id = 33610634;
        $I->sendGET('users/auth', ['user_id' => $user_id]);
        $I->seeResponseCodeIs('200');
        $I->seeResponseIsJson();
        $I->canSeeResponseJsonMatchesXpath('//user/user_id');
        $I->canSeeResponseJsonMatchesXpath('//user/access_token');
    }
}
