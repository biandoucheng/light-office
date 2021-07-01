<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/6/29
 * Time: 11:56
 */

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromIterator;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Iterator;
use App\Exports\Helper\ArrayHelper;

class LightExport implements FromIterator,WithHeadings,WithMapping,WithStrictNullComparison
{
    /*
     * @const 数据源类型 数组
     * */
    private const SOURCE_TYPE_ARRAY = "array";

    /*
     * @const 数据源类型 集合
     * */
    private const SOURCE_TYPE_COLLECTION = "collection";

    /*
     * @const 数据源类型 集合
     * */
    private const SOURCE_TYPE_DB = "db";

    /*
     * @const 数据源类型 模型
     * */
    private const SOURCE_TYPE_MODEL = "model";

    /*
     * @const 数据源类型 标准对象
     * */
    private const SOURCE_TYPE_STD_CLASS = "stdClass";

    /*
     * @var 数据源
     * */
    private $source;

    /*
     * @var string 数据源类型
     * */
    private $sourceType;

    /*
     * @var array 表头
     * */
    private $headers;

    /*
     * @var array 汇总行
     * */
    private $summary;

    /*
     * @var callable 回调,对数据集进行预处理
     * */
    private $callBack;

    /*
     * @var array 回调的额外参数
     * */
    private $params;


    /**
     *@description 构造方法初始化
     *
     *@author biandou
     *@date 2021/6/29 13:44
     *@param $source Collection,DB,stdClass,array
     *@param array $headers 表头
     *@param array $params 回调需要的额外参数
     */
    public function __construct(&$source,array $headers,?callable $callBack,array $params=[])
    {
        $this->load($source,$headers,$callBack,$params);
    }

    /**
     *@description 数据源加载
     *
     *@author biandou
     *@date 2021/6/29 12:00
     *@param $source Collection,DB,stdClass,array
     *@param array $headers 表头
     *@param array $params 回调需要的额外参数
     */
    public function load(&$source,array $headers,?callable $callBack,array $params=[])
    {
        #加载数据源，表头，回调函数
        $this->source   = $source;
        $this->headers  = $headers;
        $this->callBack = $callBack;
        $this->params   = $params;

        #数据类型检测
        if(is_array($source)) {
            $this->sourceType = self::SOURCE_TYPE_ARRAY;
        }else if($source instanceof Model) {
            $this->sourceType = self::SOURCE_TYPE_MODEL;
        }else if($source instanceof DB) {
            $this->sourceType = self::SOURCE_TYPE_DB;
        }else if($source instanceof Collection) {
            $this->sourceType = self::SOURCE_TYPE_COLLECTION;
        }else {
            $this->sourceType = self::SOURCE_TYPE_STD_CLASS;
        }
    }

    /**
     *@description 重置数据源
     *
     *@author biandou
     *@date 2021/7/1 9:49
     *@param $source Collection,DB,stdClass,array
     */
    public function resetSource(&$source)
    {
        if($source) {
            $this->source = $source;
        }
    }

    /**
     *@description 重置表头
     *
     *@author biandou
     *@date 2021/7/1 9:50
     *@param array $headers 表头
     */
    public function resetHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     *@description 重置回调
     *
     *@author biandou
     *@date 2021/7/1 9:51
     *@param callable $callBack 回调方法
     */
    public function resetCallBack(callable $callBack)
    {
        $this->callBack = $callBack;
    }

    /**
     *@description 重置回调参数
     *
     *@author biandou
     *@date 2021/7/1 9:52
     *@param array $params 回调额外参数
     */
    public function resetCallBackParams(array $params)
    {
        $this->params = $params;
    }

    /**
     *@description 表头处理
     *
     *@author biandou
     *@date 2021/6/29 14:14
     *
     *@return array
     */
    public function headings(): array
    {
        if($this->headers) {
            return array_values($this->headers);
        }
        return ["-","-","-"];
    }

    /**
     *@description 数据预处理,在map之前会调用 一般用于额外计算或者附加信息补充
     *
     *@author biandou
     *@date 2021/6/29 14:23
     *@param array $rows 数据源
     *
     *@return array 处理后的数据
     */
    public function prepareRows($rows)
    {
        if(is_callable($this->callBack)) {
            $rows = ($this->callBack)($rows,...$this->params);
        }

        return $rows;
    }

    /**
     *@description 列顺序处理
     *
     *@author biandou
     *@date 2021/6/29 14:15
     *
     *@return array
     */
    public function map($row): array
    {
        if(!$this->headers) {
           return $row;
        }

        $out = [];
        foreach ($this->headers as $field=>$zh) {
            $out[] = $row[$field] ?? "---";
        }

        return $out;
    }


    /**
     *@description 数据转迭代器
     *
     *@author biandou
     *@date 2021/6/29 11:59
     *@param
     *
     *@return
     */
    public function iterator(): Iterator
    {
        #根据数据类型进行数据迭代输出
        switch ($this->sourceType){
            case self::SOURCE_TYPE_ARRAY:
                yield $this->source;
                break;
            case self::SOURCE_TYPE_COLLECTION:
                yield collect($this->source)->toArray();
                break;
            case self::SOURCE_TYPE_STD_CLASS:
                yield ArrayHelper::turnStdClassToArray((array)$this->source);
                break;
            case self::SOURCE_TYPE_MODEL:
                foreach ($this->iterate() as $item) {
                    yield $item;
                }
                break;
            case self::SOURCE_TYPE_DB:
                foreach ($this->iterate() as $item) {
                    yield $item;
                }
                break;
            default:
                yield (array)$this->source;
        }
    }

    /**
     *@description DB|Model 迭代
     *
     *@author biandou
     *@date 2021/6/29 14:35
     *
     *@return
     */
    private function iterate()
    {
        #数据库分片取出数据，并转化成数组返回
        do{
            #初始化
            if(empty($skip)) {
                $skip = 0;
                $offset = 1000;
            }

            #数据查询
            $items = $this->source->skip($skip)->limit($offset)->get();

            #数据整理
            if($items) {
                if($this->sourceType == self::SOURCE_TYPE_MODEL) {
                    #模型结果转数组
                    $items = $items->toArray();
                }else {
                    #DB结果转数组
                    $items = ArrayHelper::turnStdClassToArray($items);
                }
                #步长加一倍
                $skip += $offset;

                #数据输出
                foreach ($items as $item) {
                    yield $item;
                }
            }
        }while($items);
    }
}