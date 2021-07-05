<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/7/3
 * Time: 16:46
 */

namespace LTOFFICE;

use LTOFFICE\Help\AttrHelper;

/*
 * 导出文件属性
 * */
class Property
{
    use AttrHelper;

    /*
     * @var string 作者
     * */
    public $creator = "Unknown Creator";

    /*
     * @var string 创建时间
     * */
    public $created;

    /*
     * @var title 标题
     * */
    public $title = "Untitled Spreadsheet";

    /*
     * @var string 描述
     * */
    public $description = "";

    /*
     * @var string 主题
     * */
    public $subject = "";

    /*
     * @var string 分类
     * */
    public $category = "";


    public function __construct()
    {
        $this->created = time();
    }
}