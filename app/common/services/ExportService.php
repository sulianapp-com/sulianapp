<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/25
 * Time: 上午11:31
 */

namespace app\common\services;



use app\common\helpers\Url;

class ExportService
{
    private $file_name;
    private $export_data;
    private $page_count;
    public $builder_model;
    private $export_page;
    private $page_size = 500;

    public function __construct($builder, $export_page = 1)
    {
        $this->export_page = $export_page;
        $builder_count = $builder->count();
        $this->page_count = ceil($builder_count / $this->page_size);
        $this->builder_model = $builder->skip(($export_page - 1) * $this->page_size)->take($this->page_size)->get();
    }

    private function swith()
    {
        switch ($this->page_count) {
            case '1':
                $this->smallExcel();
                break;
            default:
                $this->bigExcel();
                break;
        }
    }

    private function smallExcel()
    {
        $this->exportBuilder()->export('xls');
    }

    private function bigExcel()
    {
        $this->exportBuilder()->store('xls');
    }

    private function exportBuilder()
    {
        $export_data = $this->export_data;
        return \Excel::create($this->file_name, function ($excel) use ($export_data) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        });
    }

    public function export($file_name, $export_data, $route = null, $type = 'export')
    {
        $this->file_name = $file_name;
        $this->export_data = $export_data;
        $this->swith();
        if ($this->export_page == $this->page_count) {
            $filename = storage_path('logs/' . time() . 'down.zip');
            $time = time();
            $zip = new \ZipArchive(); // 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
            if ($zip->open ( $filename, \ZipArchive::CREATE ) !== TRUE) {
                exit ( '无法打开文件，或者文件创建失败' );
            }
            //$fileNameArr 就是一个存储文件路径的数组 比如 array('/a/1.jpg,/a/2.jpg....');
            $fileNameArr = file_tree(storage_path('exports'));
            foreach ($fileNameArr as $val ) {
                // 当你使用addFile添加到zip包时，必须确保你添加的文件是存在的，否则close时会返回FALSE，而且使用addFile时，即使文件不存在也会返回TRUE
                if(file_exists(storage_path('exports/' . basename($val)))){
                    $zip->addFile (storage_path('exports/') . basename($val), basename($val) ); // 第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                }
            }

            $zip->close (); // 关闭
            foreach ($fileNameArr as $val ) {
                file_delete(storage_path('exports/' . basename($val)));
            }
            //下面是输出下载;
            if (config('app.framework') == 'platform') {
                $url = "http://". $_SERVER['HTTP_HOST'].'/storage/logs/' . $time ."down.zip";
            } else {
                $url = "http://". $_SERVER['HTTP_HOST'].'/addons/yun_shop/storage/logs/' . $time ."down.zip";
            }
            $backurl = "http://". $_SERVER['HTTP_HOST']. config('app.isWeb') . "?c=site&a=entry&m=yun_shop&do=4302&route=" . $route;
            echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;"><a style="color:red;text-decorationnone;"  href="'.$url.'">点击获取下载文件</a><a style="color:#616161"  href="'.$backurl.'">返回</a><div>';
            exit;
        } else {
            echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;">共'.$this->page_count.'个excel文件, 已完成'.$this->export_page. '个。 <div>';
            $this->export_page += 1;

            $url = Url::absoluteWeb(\Request::query('route'), ['search' => \YunShop::request()->get()['search'], $type => 1, 'export_page' => $this->export_page]);
            echo '<meta http-equiv="Refresh" content="1; url='.$url.'" />';
            exit;
        }
    }
}