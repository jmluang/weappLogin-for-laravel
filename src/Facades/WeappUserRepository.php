<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\repositories\UserRepository;

class WeappUserRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserRepository::class;
    }
}
