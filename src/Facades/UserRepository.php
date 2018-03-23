<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\database\UserRepository as User;

class UserRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return User::class;
    }
}
