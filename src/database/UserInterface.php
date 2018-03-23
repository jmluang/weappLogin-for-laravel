<?php

namespace jmluang\weapp\database;


interface UserInterface
{
    /** save user record
     * @param $userinfo
     * @param $skey
     * @param $session_key
     * @return mixed
     */
    public static function storeUserInfo($userinfo, $skey, $session_key);

    /**
     * fetch user record
     * @param $skey
     * @return mixed
     */
    public static function findUserBySKey($skey);
}
