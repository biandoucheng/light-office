<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30);
            $table->string('email',50);
            $table->string('phone',11);
            $table->tinyInteger('status')->default(0);
            $table->dateTime("accept_date");
            $table->timestamp("create_time");
            $table->timestamp("lastmod_time")->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team');
    }
}
