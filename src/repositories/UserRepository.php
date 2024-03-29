<?php

namespace jmluang\weapp\repositories;

use jmluang\weapp\WeappUserInterface;

use WeappUser;

class UserRepository implements WeappUserInterface
{
    public function storeUserInfo($userinfo, $skey, $session_key)
    {
        $uuid = bin2hex(openssl_random_pseudo_bytes(16));
        $create_time = date('Y-m-d H:i:s');
        $last_visit_time = $create_time;
        $open_id = $userinfo['openid'];
        $user_info = json_encode($userinfo);

        $res = WeappUser::where('open_id', $open_id)->get();

        if (empty($res->toArray())) {
            WeappUser::insert(
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
            WeappUser::where('open_id', $open_id)->update(
                compact('uuid', 'skey', 'last_visit_time', 'session_key', 'user_info')
            );
        }
    }

    public function findUserBySKey($skey)
    {
        return WeappUser::where('skey', $skey)->first();
    }
}
