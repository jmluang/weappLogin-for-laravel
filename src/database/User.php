<?php

namespace jmluang\weapp\database;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * defined table
     * @var string
     */
    protected $table = "cSessionInfo";

    /**
     * don't use created_at and update_at
     * @var bool
     */
    public $timestamps = false;
}
