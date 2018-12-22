<?php

namespace jmluang\weapp;

use Illuminate\Database\Eloquent\Model;

class WeappUser extends Model
{
    /**
     * defined table
     * @var string
     */
    protected $table = "weapp_user";

    /**
     * don't use created_at and update_at
     * @var bool
     */
    public $timestamps = false;
}
