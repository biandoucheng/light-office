<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/19
 * Time: 17:33
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SaleReport extends Model
{
    protected $table = "sale_report";
    protected $guarded = [];
    public $timestamps = false;
}