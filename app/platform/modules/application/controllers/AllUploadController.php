<?php 

namespace app\platform\modules\Application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\application\models\CoreAttach;
use app\common\services\qcloud\Api;
use app\common\services\aliyunoss\OssClient;
use app\common\services\aliyunoss\OSS\Core\OssException;
use app\common\services\ImageZip;

class AllUploadController extends BaseController
{
    protected $path;
    protected $pattern;

    public function __construct()
    {
        $this->path = config('filesystems.disks.syst')['url'].'/'; //本地图片实际存放路径

        $this->pattern =  '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
    }

	public function upload()
    {
        $file = request()->file('file');
            \Log::info('upload_get_file', $file);

        if (count($file) > 6) {
                \Log::info('upload_file_num_out');
            return $this->errorJson('文件数量过多, 请选择低于6个文件');
        }

        if (count($file) > 1 && count($file) < 7) {
            //多文件上传
                \Log::info('upload_more_files');
            
            foreach ($file as $k => $v) {
               
                if ($v) {
                    
                    $url = $this->doUpload($v);

                    $success[] = $url;
                    //检验返回的是否是 正确合法链接
                    if (!preg_match($this->pattern, $url)) {

                        \Log::info('more_upload_fail');

                        $fail[] = $url;

                        unset($success[$k]);
                    }
                }
            }
        } else {

            $success = $this->doUpload($file);
                \Log::info('upload_one_files');

            //检验参数是否为正确的url
            if (!preg_match($this->pattern, $success)) {
                    \Log::info('upload_file_fail');
                $fail = $success;
                unset($success);
            }
        }
        return $this->successJson('ok', ['success'=>$success, 'fail'=>$fail]);
    }
    
    public function doUpload($file)
    {
    	if (!$file->isValid()) {
                    \Log::info('no_upload_file');
            return false;
        }

        if ($file->getClientSize() > 30*1024*1024) {
                \Log::info('file_size_out');
        }
         //默认支持的文件格式类型
        $defaultImgType = [
            'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
            'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
        ];

        $defaultAudioType = ['AVI', 'ASF', 'WMV', 'AVS', 'FLV', 'MKV', 'MOV', '3GP', 'MP4',
            'MPG', 'MPEG', 'DAT', 'OGM', 'VOB', 'RM', 'RMVB', 'TS', 'TP', 'IFO', 'NSV'
        ];

        $defaultVideoType = [
            'MP3', 'AAC', 'WAV', 'WMA', 'CDA', 'FLAC', 'M4A', 'MID', 'MKA', 'MP2',
                'MPA', 'MPC', 'APE', 'OFR', 'OGG', 'RA', 'WV', 'TTA', 'AC3', 'DTS'
        ];

        $ext = $file->getClientOriginalExtension();

        $originalName = $file->getClientOriginalName();

        $realPath = $file->getRealPath();

        if (in_array($ext, $defaultImgType)) {
            $file_type = 'images';
        } elseif (in_array($ext, $defaultAudioType)) {
            $file_type = 'audios';
        } elseif (in_array($ext, $defaultVideoType)) {
            $file_type = 'videos'; 
        }

        $newFileName = $this->getNewFileName($originalName, $ext, $file_type); 
        	\Log::info('up_newFileName', $newFileName);

        $setting = SystemSetting::settingLoad('global', 'system_global');

        $remote = SystemSetting::settingLoad('remote', 'system_remote');
                    \Log::info('system_setting', [$setting, $remote]);
            
        if (in_array($ext, $defaultImgType)) {

            if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions'])) ) {
                    \Log::info('local_file_type_is_not_set_type');
                return '非规定类型的文件格式';
            }

            $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024*1024*5; //默认大小为5M

            if ($file->getClientSize() > $defaultImgSize) {
                    \Log::info('local_file_size_is_not_set_size');
                return '文件大小超出规定值';
            }
        }

        if (in_array($ext, $defaultAudioType) || in_array($ext, $defaultVideoType)) {

            if ($setting['audio_extentions'] && !in_array($ext, array_filter($setting['audio_extentions'])) ) {
                    \Log::info('local_audio_video_file_type_is_not_set_type');

                return '非规定类型的文件格式';
            }
            $defaultAudioSize = $setting['audio_limit'] ? $setting['audio_limit'] * 1024 : 1024*1024*30; //音视频最大 30M

            if ($file->getClientSize() > $defaultAudioSize) {
                    \Log::info('local_audio_video_file_size_is_not_set_size');
                return '文件大小超出规定值';
            }
        }
        $file_type = $file_type == 'images' ? 'syst_images' : $file_type;

        \Log::info('disk and url', [\Storage::disk($file_type), \Storage::disk($file_type)->url('')]);
       
        //执行本地上传
        $local_res = \Storage::disk($file_type)->put(substr($newFileName, 17), file_get_contents($realPath));
        	
        	\Log::info('local_upload', $local_res);

        if (!$local_res) {
            	
            \Log::info('新框架本地上传记录失败', [$originalName, $newFileName]);
            return '本地上传失败';
        }

        if ($setting['image']['zip_percentage']) {
            //执行图片压缩
            $imagezip = new ImageZip();
            $imagezip->makeThumb(
                yz_tomedia($newFileName),
                yz_tomedia($newFileName),
                $setting['image']['zip_percentage']
            );
        }
        
        if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
        	$imagezip = new ImageZip();
        	$imagezip->makeThumb(
        		yz_tomedia($newFileName),
        		yz_tomedia($newFileName),
        		$setting['thumb_width']
        	);
        }

        if ($remote['type'] != 0) { //远程上传
                \Log::info('newFileName', $newFileName);
       		$res = file_remote_upload($newFileName, true, $remote);
        }
           \Log::info('do_upload_done', $res);
       	
        if (!$res || $local_res) {
        	//数据添加
        	$this->getData($originalName, $file_type, $newFileName, $remote['type']);
       		return yz_tomedia($newFileName);
        }
        return $res;
    }

    /**
     * 获取新文件名
     * @param  string $originalName 原文件名
     * @param  string $ext          文件扩展名
     * @return string               新文件名
     */
    public function getNewFileName($originalName, $ext, $file_type)
    {
        return $file_type.'/'.$this->getUniacid().'/'.date('Y').'/'.date('m').'/'.date('Ymd').md5($originalName . str_random(6)) . '.' . $ext;
    }

    public function getUniacid()
    {
        return \YunShop::app()->uniacid ? : 0;
    }

	//获取本地已上传图片的列表
    public function getLocalList()
    {
        $core = new CoreAttach();

        if (request()->year != '不限') {
            $search['year'] = request()->year;
        }

        if(request()->month != '不限') {
            $search['month'] = request()->month;
        }

        $core = $core->where('uniacid', 0)->where('type', 1)->orderBy('id', 'desc');

        if ($search) {
            $core = $core->search($search);
        }

        $list = $core->paginate()->toArray();

        foreach ($list['data'] as $k => $v) {

            if ($v['attachment'] && $v['id']) {

                $data['data'][$k]['id'] = $v['id'];
                $data['data'][$k]['url'] = yz_tomedia($v['attachment']);
            }
        }
        
        $data['total'] = $list['total'];
        $data['per_page'] = $list['per_page'];
        $data['last_page'] = $list['last_page'];
        $data['prev_page_url'] = $list['prev_page_url'];
        $data['next_page_url'] = $list['next_page_url'];
        $data['current_page'] = $list['current_page'];
        $data['from'] = $list['from'];
        $data['to'] = $list['to'];

        if (!$data['data']) {
            $data['data'] = [];
        }

        return $this->successJson('获取成功', $data);
    }

    public function delLocalImg()
    {
        $id = request()->id;
        
        $core = CoreAttach::find($id);

        if (!$core) {
            return $this->errorJson('请重新选择');
        }
        
        $setting = SystemSetting::settingLoad('remote', 'system_remote');

        if ($core['upload_type']== 2) { //oss
            try {
                $oss = new OssClient($setting['alioss']['key'], $setting['alioss']['secret'], $setting['alioss']['ossurl']);
            } catch (OssException $e) {
                return $this->errorJson($e->getErrorMessage());
            }

            $ossbucket = rtrim(substr($setting['alioss']['bucket'], 0, strrpos($setting['alioss']['bucket'],'@')), '@');
            $res = $oss->deleteObject($ossbucket, $core['attachment']); //info['url'] 

            if (!$res['info']['url']) {
                \Log::info('删除阿里云图片失败', [$core['id'], $res]);
                return $res;
            }

        } elseif ($core['upload_type'] == 4) { //cos
            try {

	            $cos = new Api([
	                'app_id' => $setting['cos']['appid'],
	                'secret_id' => $setting['cos']['secretid'],
	                'secret_key' => $setting['cos']['secretkey'],
	                'region' => $setting['cos']['url']
	            ]);
            	
            	$res = $cos->delFile($setting['cos']['bucket'], $core['attachment']); //[code =0  'message'='SUCCESS']
            	\Log::info('delFile_in_cos and res', [$core['attachment'], $res]);

            } catch (\Exception $e) {
            	return $this->errorJson('腾讯云配置错误');
            }

            if ($res['code'] != 0 || $res['message'] != 'SUCCESS') {
                //删除失败
                \Log::info('删除腾讯云图片失败', [$core['id'], $res]);
                return $res;
            }

        } else {
            //删除文件
            $res = \app\common\services\Storage::remove(yz_tomedia($core['attachment']));
            if ($res !== true) {
                \Log::info('本地图片删除失败', $core['attachment']);
            }
        }

        if ($core->delete()) {
            return $this->successJson('删除成功');
        }
        return $this->errorJson('删除失败');
    }

    //上传记录表
    public function getData($originalName, $file_type, $newFileName, $save_type)
    {
        //存储至数据表中
        $core = new CoreAttach;
        \Log::info('get_data_file_type', $file_type);
        switch ($file_type) {
        	case 'syst_images':
        		$type = 1;
        		break;
        	case 'audios':
        		$type = 2;
        		break;
        	default:
        		$type = 3;
        		break;
        }

        $d = [
            'uniacid' => $this->getUniacid(),
            'uid' => \Auth::guard('admin')->user()->uid,
            'filename' => $originalName,
            'type' => $type, //类型1.图片; 2.音乐
            'attachment' => $newFileName,
            'upload_type' => $save_type
        ];

        $core->fill($d);
        $validate = $core->validator();

        if (!$validate->fails()) {
            
            if ($core->save()) {
                return 1;
            }
        } 
        return $validate->messages();
    }
}