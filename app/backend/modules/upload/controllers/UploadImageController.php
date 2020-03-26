<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/10
 * Time: 下午2:25
 */

namespace app\backend\modules\upload\controllers;

use app\common\components\BaseController;
use app\backend\modules\upload\models\CoreAttach;


class UploadImageController extends BaseController
{
    protected $uniacid;
    protected $common;

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid ?  : 0 ;
        $this->common = $this->common();
    }

    public function getImage(){
        $core_attach = new CoreAttach();

        if (request()->year != '不限') {
            $search['year'] = request()->year;
        }

        if(request()->month != '不限') {
            $search['month'] = request()->month;
        }

        $core_attach = $core_attach->search($search);

        $core_attach = $core_attach->where('uniacid', $this->uniacid)->where('module_upload_dir', $this->common['module_upload_dir']);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
        }

        //type = 1 图片
        $core_attach = $core_attach->where('type', CoreAttach::IMAGE_TYPE);

        $core_attach = $core_attach->orderby('createtime', 'desc')->paginate(CoreAttach::PAGE_SIZE);

        foreach ($core_attach as &$meterial) {
            $meterial['url'] = yz_tomedia($meterial['attachment']);
            unset($meterial['uid']);
        }


        return $this->successJson('ok',$core_attach);
    }


    public function common()
    {
        $dest_dir = request()->dest_dir;
        $type = in_array(request()->upload_type, array('image','audio','video')) ? request()->upload_type : 'image';
        $option = array_elements(array('uploadtype', 'global', 'dest_dir'), $_POST);
        $option['width'] = intval($option['width']);
        $option['global'] = request()->global;


        if (preg_match('/^[a-zA-Z0-9_\/]{0,50}$/', $dest_dir, $out)) {
            $dest_dir = trim($dest_dir, '/');
            $pieces = explode('/', $dest_dir);
            if(count($pieces) > 3){
                $dest_dir = '';
            }
        } else {
            $dest_dir = '';
        }

        $module_upload_dir = '';
        if($dest_dir != '') {
            $module_upload_dir = sha1($dest_dir);
        }

        if ($option['global']) {
            $folder = "{$type}s/global/";
            if ($dest_dir) {
                $folder .= '' . $dest_dir . '/';
            }
        } else {
            $folder = "{$type}s/{$this->uniacid}";
            if (!$dest_dir) {
                $folder .= '/' . date('Y/m/');
            } else {
                $folder .= '/' . $dest_dir . '/';
            }
        }

        return [
            'dest_dir' => $dest_dir,
            'module_upload_dir' => $module_upload_dir,
            'type' => $type,
            'options' => $option,
            'folder' => $folder,
        ];
    }
}