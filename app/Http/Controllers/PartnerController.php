<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/29
 * Time: 9:02
 */

namespace App\Http\Controllers;

use App\Exports\PartnerExport;
use Maatwebsite\Excel\Facades\Excel;


class PartnerController extends Controller
{
    public function export()
    {
        return Excel::download(new PartnerExport(),"partner.xlsx");
    }
}