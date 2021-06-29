<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/19
 * Time: 16:04
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = "partner";
    protected $guarded = [];
    public $timestamps = false;
}