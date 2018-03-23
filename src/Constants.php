<?php

namespace jmluang\weapp;

use \Exception;

class Constants
{

    /**
     * 登陆失败
     */
    const E_LOGIN_FAILED = "login failure";


    /**
     * 自定义 header
     */
    const WX_HEADER_CODE = 'x-wx-code';
    const WX_HEADER_ENCRYPTED_DATA = 'x-wx-encrypted-data';
    const WX_HEADER_IV = 'x-wx-iv';
    const WX_HEADER_SKEY = 'x-wx-skey';

    /**
     * 解密失败
     */
    const E_DECRYPT_FAILED = 'E_DECRYPT_FAILED';

    /**
     * 登录成功
     */
    const S_AUTH = 1;

    /**
     * 登录失败
     */
    const E_AUTH = 0;

    /**
     * 微信登录态有效期
     * @var int
     */
    private static $WxLoginExpires = 7200;

    /**
     * 网络请求超时时长（单位：毫秒）
     * @var int
     */
    private static $NetworkTimeout = 3000;

    /**
     * magic method
     * @param $name
     * @param $arguemnts
     * @return null|void
     * @throws Exception
     */
    public static function __callStatic($name, $arguemnts)
    {
        $class = get_class();

        if (strpos($name, 'get') === 0) {
            $key = preg_replace('/^get/', '', $name);

            if (property_exists($class, $key)) {
                $value = self::$$key;

                if (strpos($key, 'Log') === 0) {
                    return $value;
                }

                if (is_string($value) && !$value) {
                    throw new Exception("`{$key}`不能为空，请确保 SDK 配置已正确初始化", 1);
                }

                return $value;
            }
        }

        if (strpos($name, 'set') === 0) {
            $key = preg_replace('/^set/', '', $name);
            $value = isset($arguemnts[0]) ? $arguemnts[0] : NULL;

            if (property_exists($class, $key)) {
                if (gettype($value) === gettype(self::$$key)) {
                    self::$$key = $value;
                } else {
                    throw new Exception("Call to method {$class}::{$name}() with invalid arguements", 1);
                }
                return;
            }
        }

        throw new Exception("Call to undefined method {$class}::{$name}()", 1);
    }
}
