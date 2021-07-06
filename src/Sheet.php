<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/7/5
 * Time: 11:36
 */

namespace LTOFFICE;

use LTOFFICE\IteratorCache;
use LTOFFICE\Help\AttrHelper;

/*
 * 电子表
 * */
class Sheet
{
    use AttrHelper;

    /*
     * @var string 电子表标题
     * */
    public $title = "";

    /*
     * @var bool 电子表是否是第一个展示
     * */
    public $first = false;

    /*
     * @var mixed 电子表数据源
     * */
    protected $cache;

    /*
     * @var array 表头 [field=>zh]
     * */
    public $header = [];

    /*
     * @var bool 表头是否输出到报表中
     * */
    public $headerOut = true;

    /*
     * @var array 表头对应的列 eg:['name'=>'A']
     * */
    public $columns;

    /*
     * @var array 表头样式 [field=>Style]
     * */
    public $headerStyle = [];

    /*
     * @var array 数据列样式 [field=>Style]
     * */
    public $columnStyle = [];

    /*
     * @var array 指定位置特殊值设置 ['field'=>'Total:','field'=>'=SUM(B2:B6)']
     * */
    public $specialVal = [];

    /*
     * @var array 汇总行
     * */
    public $summaryRow = [];

    /*
     * @var string 汇总位置 top | bottom
     * */
    protected $summaryPos = 'top';

    /*
     * @var int 数据行数,包含表头
     * */
    protected $rowIndex = 0;

    /**
     *@description 初始化
     *
     *@author biandou
     *@date 2021/7/6 9:22
     *@param mixed $source 数据源
     *@param array $callArray 回调函数列表
     *@param int $quantity 单次处理的数据条数
     */
    public function __construct($source,array $callArray = [],int $quantity=1000)
    {
        $cache = new IteratorCache($source,$callArray,$quantity);
        $this->cache = $cache;
    }

    /**
     *@description 获取数据源
     *
     *@author biandou
     *@date 2021/7/5 13:49
     *
     *@return IteratorCache
     */
    public function getCache():IteratorCache
    {
        return $this->cache;
    }

    /**
     *@description 是否设置了表头信息
     *
     *@author biandou
     *@date 2021/7/5 15:44
     *@param
     *
     *@return
     */
    public function isHeaderEnable()
    {
        return is_array($this->header) && !empty($this->header);
    }

    /**
     *@description 表头是否要输出
     *
     *@author biandou
     *@date 2021/7/5 15:43
     *
     *@return bool
     */
    public function isHeaderOutEnable():bool
    {
        return $this->isHeaderEnable() && $this->headerOut;
    }

    /**
     *@description 生成列名
     *
     *@author biandou
     *@date 2021/7/5 15:20
     */
    public function mkColumns()
    {
        $alphas = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","R","S","T","U","V","W","X","Y","Z"];

        $alphaNum = count($alphas);
        $index = 0;
        foreach ($this->header as $field=>$zh) {
            $index += 1;
            $times = floor($index / $alphaNum);

            #列太长丢掉
            if($times > 24) {
                break;
            }

            #列数大于24的处理
            $prefix = "";
            if($times > 0) {
                $prefix .= $alphas[$times - 1];
            }

            #拼接完整的列名
            $remain = $index % $alphaNum;
            $col = $prefix . $alphas[$remain - 1];

            $this->columns[$field] = $col;
        }
    }

    /**
     *@description 是否是需要汇总行
     *
     *@author biandou
     *@date 2021/7/5 15:37
     *
     *@return bool
     */
    public function isSummaryEnable():bool
    {
        return is_array($this->summaryRow) && !empty($this->summaryRow);
    }

    /**
     *@description 判断是否是在顶部汇总
     *
     *@author biandou
     *@date 2021/7/5 15:36
     *
     *@return bool
     */
    public function isTopSummary():bool
    {
        return $this->isSummaryEnable() && $this->summaryPos == 'top';
    }

    /**
     *@description 判断是否是在底部汇总
     *
     *@author biandou
     *@date 2021/7/5 16:06
     *
     *@return bool
     */
    public function isBottomSummary():bool
    {
        return $this->isSummaryEnable() && $this->summaryPos != 'top';
    }

    /**
     *@description 数据行数+1
     *
     *@author biandou
     *@date 2021/7/6 10:52
     *@param
     */
    public function addRowIndex()
    {
        $this->rowIndex += 1;
    }

    /**
     *@description 获取当前数据行数
     *
     *@author biandou
     *@date 2021/7/6 10:53
     *
     *@return int
     */
    public function getRowIndex():int
    {
        return $this->rowIndex;
    }
}