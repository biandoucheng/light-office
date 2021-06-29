<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/29
 * Time: 9:00
 */

namespace App\Exports;

use App\Model\Partner;
use Iterator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromIterator;
use Maatwebsite\Excel\Concerns\WithMapping;

class PartnerExport implements FromIterator,WithHeadings,WithMapping
{
    public function iterator(): Iterator
    {
        yield Partner::all();
    }

    public function headings():array
    {
        return ["ID","伙伴类型","用户名","公司名","成员数量","邀请人ID","状态","接入时间","修改时间"];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->part_type,
            $row->username,
            $row->company,
            $row->member_num,
            $row->invite_id,
            $row->status,
            $row->accept_time,
            $row->lastmod_time,
        ];
    }
}