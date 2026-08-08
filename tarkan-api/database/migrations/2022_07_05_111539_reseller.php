<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reseller extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('resseler_domains', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('ownerId');
            $table->integer('userId');
            $table->integer('limitDomains');
            $table->string('domainName',255);
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
