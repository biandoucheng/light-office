<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class teamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table("team")->insertGetId([
            "name" => str_random(15),
            "email" => str_random(10).'gmail.com',
            "phone" => '1'.((string)random_int(3,9)).((string)random_int(1001,9999)).((string)random_int(10001,99999)),
            "accept_date" => '20210601',
            "create_time" => date("Y-m-d H:i:s")
        ]);

        $this->acceptPartner($id);
    }

    /*
     * 对接合作伙伴
     * */
    public function acceptPartner(int $id)
    {
        $partId = DB::table("partner")->insertGetId([
            "part_type" => random_int(1,2),
            "username" => str_random(15),
            "company" => "MingRen ".ucfirst(str_random("10"))." Company",
            "member_num" => random_int(50,1000),
            "invite_id" => $id,
            "accept_time" => date("Y-m-d H:i:s"),
            "lastmod_time" => date("Y-m-d H:i:s")
        ]);

        $this->partnerReport($partId);
    }

    /*
     * 销售量
     * */
    public function partnerReport(int $id)
    {
        $start = 20210401;
        $end   = 20210430;

        for (;$start <= $end;$start++) {
            $sale = random_int(10,100);
            $back = floor($sale * random_int(0,5) * 0.1);
            $broken = random_int(0,8);

            for ($memberId = 1; $memberId <= 100; $memberId++) {
                DB::table("sale_report")->insert([
                    "yyyyMMdd" => $start,
                    "partner_id" => $id,
                    "member_id" => $memberId * 3,
                    "stat" => random_int(0,1),
                    "sale_volume" => $sale,
                    "back_volume" => $back,
                    "broken_volume" => $broken,
                    "earn" => $sale * random_int(780,1000)
                ]);
            }
        }
    }
}
