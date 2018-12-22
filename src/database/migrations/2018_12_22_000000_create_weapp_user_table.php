<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeappUserTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weapp_user', function (Blueprint $table) {
            $table->string('open_id', 100)->index('openid');
            $table->string('uuid', 100);
            $table->string('skey', 100)->index('skey');
            $table->string('session_key', 100);
            $table->string('user_info', 2048);
            $table->timestamp('create_time')->useCurrent();
            $table->timestamp('last_visit_time')->useCurrent();
            $table->primary('open_id');
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('weapp_user');
    }
}