<?php

namespace jmluang\weapp\repositories;

use \Exception as Exception;
use \GuzzleHttp\Client;
use jmluang\weapp\Constants;

use WeappUserRepository;

class LoginService
{

    /**
     * 用户登录接口
     * @param  string $code wx.login 颁发的 code
     * @param  string $encryptData 加密过的用户信息
     * @param  string $iv 解密用户信息的向量
     * @return array  [loginState, userinfo]
     * @throws \Exception
     */
    public static function login($code, $encryptData, $iv)
    {
        // 1. 获取 session key
        $sessionKey = self::getSessionKey($code);

        // 2. 生成 3rd key (skey)
        $skey = sha1($sessionKey . mt_rand());

        /**
         * 3. 解密数据
         * 由于官方的解密方法不兼容 PHP 7.1+ 的版本
         * 这里弃用微信官方的解密方法
         * 采用推荐的 openssl_decrypt 方法（支持 >= 5.3.0 的 PHP）
         * @see http://php.net/manual/zh/function.openssl-decrypt.php
         */
        $decryptData = \openssl_decrypt(
            base64_decode($encryptData),
            'AES-128-CBC',
            base64_decode($sessionKey),
            OPENSSL_RAW_DATA,
            base64_decode($iv)
        );
        $userinfo = json_decode($decryptData);
        // 4. 储存到数据库中
        WeappUserRepository::storeUserInfo($userinfo, $skey, $sessionKey);

        return [
            'loginState' => Constants::S_AUTH,
            'userinfo' => compact('userinfo', 'skey')
        ];
    }

    /**
     * 检查用户的登陆记录，若存在登陆记录则不需要再调用微信的接口
     * @param $skey
     * @return array
     */
    public static function checkLogin($skey)
    {
        $userinfo = WeappUserRepository::findUserBySKey($skey);
        if ($userinfo === NULL) {
            return [
                'loginState' => Constants::E_AUTH,
                'userinfo' => []
            ];
        }

        $wxLoginExpires = Constants::getWxLoginExpires();
        $timeDifference = time() - strtotime($userinfo->last_visit_time);

        if ($timeDifference > $wxLoginExpires) {
            return [
                'loginState' => Constants::E_AUTH,
                'userinfo' => []
            ];
        } else {
            return [
                'loginState' => Constants::S_AUTH,
                'userinfo' => json_decode($userinfo->user_info, true)
            ];
        }
    }

    /**
     * 通过 code 换取 session key
     * @param string $code
     * @return string $session_key
     * @throws \Exception
     */
    public static function getSessionKey($code)
    {
        // 使用小程序的 AppID 和 AppSecret 获取 session key
        $appId = config("weapp.appid");
        $appSecret = config("weapp.secret");

        list($session_key, $openid) = array_values(self::getSessionKeyDirectly($appId, $appSecret, $code));
        return $session_key;
    }

    /**
     * 直接请求微信获取 session key
     * @param string $appId
     * @param string $appSecret
     * @param string $code
     * @return array { $session_key, $openid }
     * @throws \Exception
     */
    private static function getSessionKeyDirectly($appId, $appSecret, $code)
    {
        $requestParams = [
            'appid' => $appId,
            'secret' => $appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $client = new Client();
        $url = config('weapp.code2session_url') . http_build_query($requestParams);
        $res = $client->request("GET", $url, [
            'timeout' => Constants::getNetworkTimeout()
        ]);
        $status = $res->getStatusCode();
        $body = json_decode($res->getBody(), true);

        if ($status !== 200 || !$body || isset($body['errcode'])) {
            throw new Exception(Constants::E_LOGIN_FAILED . ': ' . json_encode($body));
        }

        return $body;
    }
}
