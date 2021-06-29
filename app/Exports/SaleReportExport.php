<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/29
 * Time: 11:29
 */

namespace App\Exports;

use App\Model\SaleReport;
use Illuminate\Support\Facades\DB;
use Iterator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromIterator;

class SaleReportExport implements FromIterator
{
    public function iterator(): Iterator
    {
        $skip = 0;
        $offset = 1000;
        do{
            $report = SaleReport::skip($skip)->limit($offset)->get();
            if($report) {
                $skip += $offset;
            }
            if($report->isEmpty()){
                $report = [];
            }else {
                $report = collect($report)->toArray();
            }
            yield $report;
        }while($report);
    }
}