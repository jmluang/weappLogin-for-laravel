<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\LoginService;

class WeappLogin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LoginService::class;
    }
}
