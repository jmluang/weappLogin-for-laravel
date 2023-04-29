<?php

namespace jmluang\weapp\repositories;

use jmluang\weapp\WeappUser as User;
use jmluang\weapp\WeappUserInterface as UserInterface;

class UserRepository implements UserInterface
{
    public static function storeUserInfo($userinfo, $skey, $session_key)
    {
        $uuid = bin2hex(openssl_random_pseudo_bytes(16));
        $create_time = date('Y-m-d H:i:s');
        $last_visit_time = $create_time;
        $open_id = $userinfo['openid'];
        $user_info = json_encode($userinfo);

        $res = User::where('open_id', $open_id)->get();

        if (empty($res->toArray())) {
            User::insert(
                compact(
                    'uuid',
                    'skey',
                    'create_time',
                    'last_visit_time',
                    'open_id',
                    'session_key',
                    'user_info'
                )
            );
        } else {
            User::where('open_id', $open_id)->update(
                compact('uuid', 'skey', 'last_visit_time', 'session_key', 'user_info')
            );
        }
    }

    public static function findUserBySKey($skey)
    {
        return User::where('skey', $skey)->first();
    }
}
