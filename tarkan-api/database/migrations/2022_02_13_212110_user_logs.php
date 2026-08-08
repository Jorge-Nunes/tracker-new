<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Query\Expression;

class UserLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //id
        //sesId
        //serverIp
        //serverHost

        //data
        //username
        //userIp
        //useragent
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sesId',100);
            $table->string('serverIp',15);
            $table->string('serverHost',255);
            $table->timestamps();


            $table->string('username',100);
            $table->string('userIp',255);
            $table->string('userAgent',255);

            $table->json('log');

            $table->index(['serverIp','username','sesId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        Schema::drop('user_logs');
    }
}
