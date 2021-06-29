<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger("part_type")->default(0);
            $table->string("username");
            $table->string("company");
            $table->integer("member_num")->default(0);
            $table->integer("invite_id");
            $table->tinyInteger("status")->default(1);
            $table->timestamp("accept_time");
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
        Schema::dropIfExists('partner');
    }
}
