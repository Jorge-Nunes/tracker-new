<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeviceReporting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('device_reportings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('all');
            $table->integer('online');
            $table->integer('offline');
            $table->integer('unknown');
            $table->integer('motion');

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
    }
}
