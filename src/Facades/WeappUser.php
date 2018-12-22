<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\repositories\UserRepository;

class WeappUser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserRepository::class;
    }
}
