<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("yyyyMMdd")->comment("日期 20210601");
            $table->integer("partner_id")->comment("合作伙伴ID");
            $table->integer("member_id")->comment("合作伙伴下的成员ID");
            $table->tinyInteger("stat")->default(0)->comment("审核状态 0 未审核,1 已审核");
            $table->integer("sale_volume")->default(0)->comment("销售量");
            $table->integer("back_volume")->default(0)->comment("退款量");
            $table->integer("broken_volume")->default(0)->comment("损坏量");
            $table->decimal("earn")->default(0)->comment("收入");
            $table->unique(["yyyyMMdd","partner_id","member_id"],"idxo-date-part-mem");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_report');
    }
}
