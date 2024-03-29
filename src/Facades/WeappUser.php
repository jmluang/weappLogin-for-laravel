<?php

namespace jmluang\weapp\Facades;

use Illuminate\Support\Facades\Facade;
use jmluang\weapp\Models\WeappUser;

class WeappUser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return WeappUser::class;
    }
}
