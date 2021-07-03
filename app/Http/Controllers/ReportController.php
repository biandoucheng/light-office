<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/29
 * Time: 11:39
 */

namespace App\Http\Controllers;

use App\Exports\SaleReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\SaleReport;
use App\Model\Partner;
use App\Exports\LightExport;

class ReportController extends Controller
{
    public function export()
    {
        #数据源
        $model   = new SaleReport();

        #表头
        $headers = [
            "yyyyMMdd" => "日期",
            "partner_id" => "伙伴ID",
            "partner" => "伙伴",
            "member_id" => "成员ID",
            "stat" => "数据状态",
            "sale_volume" => "销售量",
            "back_volume" => "回退量",
            "broken_volume" => "损坏量",
            "earn" => "收入",
        ];

        return Excel::download(new LightExport($model,$headers,[['call'=>$this->callBack(),'params'=>[true,false]]]),'report.xlsx');
    }

    public function callBack()
    {
        $partner = new Partner();
        return function (&$rows,?bool $aa,?bool $bb) use ($partner) {
            $partnerIds = [];
            $out = [];

            foreach ($rows as $row) {
                $partnerIds[] = $row['partner_id'];
                $row['partner'] = "---";
                $out[] = $row;
            }

            $partners = $partner->whereIn("id",$partnerIds)->pluck("username","id")->all();
            foreach ($out as &$it) {
                $it['partner'] = $partners[$it['partner_id']] ?? "---";
            }

            return $out;
        };
    }
}