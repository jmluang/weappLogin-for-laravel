<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\repositories\LoginRepository;

class WeappLogin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LoginRepository::class;
    }
}
