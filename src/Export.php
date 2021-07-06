<?php
/**
 * Created by PhpStorm.
 * User: 86182
 * Date: 2021/7/3
 * Time: 16:59
 */

namespace LTOFFICE;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\Response;

/*
 * 导出类
 * */
class Export
{
    /*
     * @var string 导出名称
     * */
    protected $name = "download.csv";

    /*
     * @var string 导出文件类型
     * */
    protected $type;

    /*
     * @var Property 属性实例
     * */
    protected $property;

    /*
     * @var array sheet 列表
     * */
    protected $sheets = [];

    /*
     * @var Sheet 在处理的电子表
     * */
    protected $activeSheet;

    /*
     * @var SpreadSheet
     * */
    protected $spreadSheet;

    /**
     *@description 初始化
     *
     *@author biandou
     *@date 2021/7/5 13:52
     *@param array $sheets Sheet数组
     */
    public function __construct(array $sheets,array $property=[],string $name = 'download.csv')
    {
        #电子表
        $this->sheets = [];
        foreach ($sheets as $sheet) {
            if($sheet instanceof Sheet) {
                $this->sheets[] = $sheet;
            }
        }

        #文件属性
        $this->property = new Property();
        $this->property->assignmentFromArray($property);

        #SpreadSheet
        $this->spreadSheet = new Spreadsheet();

        #设置文件属性
        $this->spreadSheet->getProperties()
            ->setCreator($this->property->creator)
            ->setTitle($this->property->title)
            ->setSubject($this->property->subject)
            ->setDescription($this->property->description)
            ->setCategory($this->property->category);

        #导出文件类型判断
        $this->type = $this->getSuffix($name);
        $this->name = $name;
    }

    /**
     *@description 获取文件后缀名，并纠正文件名
     *
     *@author biandou
     *@date 2021/7/5 14:31
     *@param string $name 文件名
     *
     *@return string
     */
    protected function getSuffix(string &$name):string
    {
        $names = explode(".",$name);
        $type = end($names);
        if(empty($type)) {
            $type = "csv";
            $name .= ".csv";
        }

        $type = strtolower($type);
        switch ($type) {
            case "csv":
                return "Csv";
            case "xls":
                return "Xls";
            case "xlsx":
                return "Xlsx";
            default:
                $name .= ".csv";
                return "Csv";
        }
    }

    /**
     *@description 电子表迭代器
     *
     *@author biandou
     *@date 2021/7/5 13:56
     *
     *@return iterable
     */
    public function sheetIterator():iterable
    {
        while ($this->sheets) {
            yield array_pop($this->sheets);
        }
    }

    /**
     *@description 添加一张电子表
     *
     *@author biandou
     *@date 2021/7/5 14:41
     *@param Sheet $sheet 电子表
     */
    public function addSheet(Sheet &$sheet)
    {
        #标记为在处理状态
        $this->activeSheet = $sheet;

        #生成列名
        $sheet->mkColumns();

        #实例化工作表
        $workSheet = new Worksheet();
        $workSheet->setTitle($sheet->title);

        #添加工作表至工作台
        $this->spreadSheet->addSheet($workSheet);

        #设置工作表为首页
        if($sheet->first) {
            $this->spreadSheet->setActiveSheetIndex(0);
        }

        #填充表头
        if($sheet->isHeaderOutEnable()) {
            foreach ($sheet->header as $field=>$zh) {
                #获取表头位置
                $column = $sheet->columns[$field];
                $pos = $column."1";

                #填充表头
                $this->spreadSheet->getActiveSheet()
                    ->setCellValue($pos,$zh);
            }
            #sheet数据记录+1
            $this->activeSheet->addRowIndex();
        }
    }

    /**
     *@description 写入数据
     *
     *@author biandou
     *@date 2021/7/5 19:20
     *@param array $rows 数据行
     */
    public function setCellValue(array $rows)
    {
        $rowIndex = $this->activeSheet->getRowIndex() + 1;
        foreach ($rows as $row) {
            foreach ($row as $field=>$val) {
                $column = $this->activeSheet->columns[$field];
                $pos = $column."$rowIndex";
                $this->spreadSheet->getActiveSheet()
                    ->setCellValue($pos,$val);
            }
            $rowIndex += 1;
            $this->activeSheet->addRowIndex();
        }
    }

    /**
     *@description 在sheet处理完毕后的操作
     *
     *@author biandou
     *@date 2021/7/5 16:04
     */
    public function writeSummaryRow()
    {
        #汇总行位置计算
        if($this->activeSheet->isBottomSummary()) {
            $row = $this->spreadSheet->getActiveSheet()->getHighestRow();
        }else if($this->activeSheet->isHeaderOutEnable()){
            $row = 2;
        }else{
            $row = 1;
        }

        #写入汇总行数据
        foreach ($this->activeSheet->summaryRow as $field=>$val) {
            $column = $this->activeSheet->columns[$field];
            $pos = $column."$row";
            $this->spreadSheet->getActiveSheet()
                ->setCellValue($pos,$val);
        }
    }

    /**
     *@description 下载
     *
     *@author biandou
     *@date 2021/7/5 16:31
     *
     *@return Response
     */
    public function download()
    {
        $writer = IOFactory::createWriter($this->spreadSheet,$this->type);
        $file   = $this->getTemporaryFilename($this->type);
        $writer->save($file);
        return response()->download($file)->deleteFileAfterSend();
    }

    /**
     *@description 保存到本地
     *
     *@author biandou
     *@date 2021/7/5 18:14
     *@param string $file 文件名
     *
     *@return bool
     */
    public function save (string $file = ""):bool
    {
        if(empty($file) || file_exists($file)) {
            return false;
        }

        $dirName = dirname($file);
        if(empty($dirName) || !is_dir($dirName)) {
            return false;
        }

        $writer = IOFactory::createWriter($this->spreadSheet,$this->type);
        $writer->save($file);

        return true;
    }

    /**
     *@description 获取临时文件
     *
     *@author biandou
     *@date 2021/7/5 16:45
     *@param
     *
     *@return string
     */
    private function getTemporaryFilename($extension = 'xlsx')
    {
        $temporaryFilename = tempnam($this->getTemporaryFolder(), 'phpspreadsheet-');
        unlink($temporaryFilename);

        return $temporaryFilename . '.' . $extension;
    }

    /**
     *@description 获取临时文件夹
     *
     *@author biandou
     *@date 2021/7/5 16:44
     *@param
     *
     *@return string
     */
    private function getTemporaryFolder()
    {
        $tempFolder = sys_get_temp_dir() . '/phpspreadsheet';
        if (!is_dir($tempFolder)) {
            if (!mkdir($tempFolder) && !is_dir($tempFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $tempFolder));
            }
        }

        return $tempFolder;
    }
}