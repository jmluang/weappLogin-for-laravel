<?php

namespace jmluang\weapp;

interface WeappUserInterface
{
    /** save user record
     * @param $userinfo
     * @param $skey
     * @param $session_key
     * @return mixed
     */
    public function storeUserInfo($userinfo, $skey, $session_key);

    /**
     * fetch user record
     * @param $skey
     * @return mixed
     */
    public function findUserBySKey($skey);
}
