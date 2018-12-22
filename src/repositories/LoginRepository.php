<?php

namespace jmluang\weapp\repositories;

use \Exception as Exception;
use jmluang\weapp\Constants;
use jmluang\weapp\WeappLoginInterface as LoginInterface;

class LoginRepository implements LoginInterface
{
    public function __construct()
    {
        Constants::setWxLoginExpires(config("weapp.WxLoginExpires", 7200));
        Constants::setNetworkTimeout(config("weapp.NetworkTimeout", 3000));
    }

    public static function login()
    {
        try {
            $code = self::getHttpHeader(Constants::WX_HEADER_CODE);
            $encryptedData = self::getHttpHeader(Constants::WX_HEADER_ENCRYPTED_DATA);
            $iv = self::getHttpHeader(Constants::WX_HEADER_IV);

            return LoginService::login($code, $encryptedData, $iv);
        } catch (Exception $e) {
            return [
                'loginState' => Constants::E_AUTH,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function check()
    {
        try {
            $skey = self::getHttpHeader(Constants::WX_HEADER_SKEY);

            return LoginService::checkLogin($skey);
        } catch (Exception $e) {
            return [
                'loginState' => Constants::E_AUTH,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 获取 header 中的字段值
     * @param $headerKey
     * @return string
     * @throws Exception
     */
    private static function getHttpHeader($headerKey)
    {
        $headerKey = strtoupper($headerKey);
        $headerKey = str_replace('-', '_', $headerKey);
        $headerKey = 'HTTP_' . $headerKey;
        $headerValue = isset($_SERVER[$headerKey]) ? $_SERVER[$headerKey] : '';

        if (!$headerValue) {
            throw new Exception("请求头未包含 {$headerKey}，请配合客户端 SDK 登录后再进行请求");
        }

        return $headerValue;
    }
}
