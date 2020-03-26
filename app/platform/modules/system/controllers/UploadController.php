<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/6
 * Time: 14:01
 */

namespace app\platform\modules\system\controllers;


use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\application\models\CoreAttach;
use app\platform\modules\application\models\WechatAttachment;
use app\common\services\Utils;
use app\platform\modules\application\models\AppUser;
use Ixudra\Curl\Facades\Curl;

class UploadController extends BaseController
{
    protected $global;
    protected $uniacid;
    protected $remote;
    protected $common;

    public function __construct()
    {
        $this->global = SystemSetting::settingLoad('global', 'system_global');
        $this->remote = SystemSetting::settingLoad('remote', 'system_remote');
        $this->uniacid = \config::get('app.global.uniacid') ?  : 0 ;
        $this->common = $this->common();
    }

    public function upload()
    {
        if (!$_FILES['file']['name']) {
            return $this->errorJson('上传失败, 请选择要上传的文件！');
        }
        if ($_FILES['file']['error'] != 0) {
            return $this->errorJson('上传失败, 请重试.');
        }

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $originname = $_FILES['file']['name'];
        $filename = file_random_name(base_path() . '/static/upload' . $this->common['folder'], $ext);

        $file = $this->file_upload($_FILES['file'], $this->common['type'], $this->common['folder'] . $filename, true);
        if (is_error($file)) {
            return $this->errorJson($file['message']);
        }

        $pathname = $file['path'];
        $fullname = base_path() . '/static/upload/' . $pathname;

        return $this->saveData($this->common['type'], $fullname, $originname, $ext, $filename, $this->common['module_upload_dir'], $pathname, $this->common['option']);
    }

    public function file_upload($file, $type = 'image', $name = '', $compress = false)
    {
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');

        if (!$file) {
            return error(-1, '没有上传内容');
        }
        if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
            return error(-2, '未知的上传类型');
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        switch ($type) {
            case 'image':
                $allowExt = $this->global['image_extentions'];
                $limit = $this->global['image_limit'];
                break;
            case 'thumb':
            case 'voice':
            case 'audio':
                $allowExt = $this->global['audio_extentions'];
                $limit = $this->global['audio_limit'];
                break;
            case 'video':
                $allowExt = $this->global['audio_extentions'];
                $limit = $this->global['audio_limit'];
                break;
        }
        $setting = $this->global[$type.'_extentions'];
        if ($setting) {
            $allowExt = array_merge($setting, $allowExt);
        }
        if (!in_array(strtolower($ext), $allowExt) || in_array(strtolower($ext), $harmtype)) {
            return error(-3, '不允许上传此类文件');
        }
        if ($limit && $limit * 1024 < filesize($file['tmp_name'])) {
            return error(-4, "上传的文件超过大小限制，请上传小于 {$limit}k 的文件");
        }

        $result = array();
        if (!$name || $name == 'auto') {
            $path = "/{$type}s/{$this->uniacid}" . '/'.date('Y/m/');
            Utils::mkdirs(base_path() . '/static/upload' . $path);
            $filename = file_random_name(base_path() . '/' . $path, $ext);
            $result['path'] = $path . $filename;
        } else {
            Utils::mkdirs(dirname(base_path() . '/static/upload/' . $name));
            if (!strexists($name, $ext)) {
                $name .= '.' . $ext;
            }
            $result['path'] = $name;
        }

        $save_path = base_path() . '/static/upload/' . $result['path'];
        if (!file_move($file['tmp_name'], $save_path)) {
            return error(-1, '保存上传文件失败');
        }

        if ($type == 'image' && $compress) {
            file_image_quality($save_path, $save_path, $ext, $this->global);
        }

        $result['success'] = true;
        return $result;
    }

    public function saveData($type, $fullname, $originname, $ext, $filename, $module_upload_dir, $pathname, $option)
    {
        if ($type == 'image') {
            $thumb = !$this->global['thumb'] ? 0 : 1;
            $width = intval($this->global['thumb_width']);
            if (isset($option['thumb'])) {
                $thumb = !$option['thumb'] ? 0 : 1;
            }
            if (isset($option['width']) && $option['width']) {
                $width = intval($option['width']);
            }
            if ($thumb == 1 && $width > 0) {
                $thumbnail = file_image_thumb($fullname, '', $width, $this->global);
                if ($thumbnail == 1) {
                    return $this->errorJson('创建目录失败');
                } elseif ($thumbnail == 2) {
                    return $this->errorJson('目录无法写入');
                }
                @unlink($fullname);
                if (is_error($thumbnail)) {
                    return $this->successJson($thumbnail['message']);
                } else {
                    $filename = pathinfo($thumbnail, PATHINFO_BASENAME);
                    $pathname = $thumbnail;
                    $fullname = base_path() . '/static/upload' . $pathname;
                }
            }
        }

        $info = array(
            'name' => $originname,
            'ext' => $ext,
            'filename' => $pathname,
            'attachment' => $pathname,
            'url' => yz_tomedia($pathname),
            'is_image' => $type == 'image' ? 1 : 0,
            'filesize' => filesize($fullname),
            'group_id' => intval(request()->group_id)
        );
        if ($type == 'image') {
            $size = getimagesize($fullname);
            $info['width'] = $size[0];
            $info['height'] = $size[1];
        } else {
            $size = filesize($fullname);
            $info['size'] = sizecount($size);
        }
        if ($this->remote['type']) {
            $remotestatus = file_remote_upload($pathname, true, $this->remote);
            if (is_error($remotestatus)) {
                file_delete($pathname);
                return $this->errorJson('远程附件上传失败，请检查配置并重新上传'.$remotestatus['message']);
            } else {
                file_delete($pathname);
                $info['url'] = yz_tomedia($pathname, false, $this->remote['type']);
            }
        }

        $core_attach = CoreAttach::create([
            'uniacid' => $this->uniacid,
            'uid' => \Auth::guard('admin')->user()->uid,
            'filename' => safe_gpc_html(htmlspecialchars_decode($originname, ENT_QUOTES)),
            'attachment' => $pathname ? : '',
            'type' => $type == 'image' ? 1 : ($type == 'audio'||$type == 'voice' ? 2 : 3),
            'module_upload_dir' => $module_upload_dir,
            'group_id' => intval(request()->group_id),
            'upload_type' => $this->remote['type']
        ]);

        \Log::info('----------上传附件----------', json_encode($info));
        if ($core_attach) {
            $info['state'] = 'SUCCESS';
            $info['state'] = 'SUCCESS';
            response()->json($info, 200, ['charset' => 'utf-8'])->send();
            exit;
        } else {
            return $this->errorJson('失败');
        }
    }

    public function image()
    {
        $year = request()->year;
        $month = intval(request()->month);
        $page = max(1, intval(request()->page));
        $groupid = intval(request()->groupid);
        $page_size = 24;
        $is_local_image = $this->common['islocal'] == 'local' ? true : false;
        if ($page<=1) {
            $page = 0;
            $offset = ($page)*$page_size;
        } else {
            $offset = ($page-1)*$page_size;
        }

        if(!$is_local_image) {
            $core_attach =  new WechatAttachment;
        } else {
            $core_attach = new CoreAttach;
        }
        $core_attach = $core_attach->where('uniacid', $this->uniacid)->where('module_upload_dir', $this->common['module_upload_dir']);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
        }
        if ($groupid > 0) {
            $core_attach = $core_attach->where('group_id', $groupid);
        }
        if ($groupid == 0) {
            $core_attach = $core_attach->where('group_id', -1);
        }
        if ($year || $month) {
            $start_time = $month ? strtotime("{$year}-{$month}-01") : strtotime("{$year}-1-01");
            $end_time = $month ? strtotime('+1 month', $start_time) : strtotime('+12 month', $start_time);
            $core_attach = $core_attach->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        }
        if ($this->common['islocal']) {
            $core_attach = $core_attach->where('type', 1);
        } else {
            $core_attach = $core_attach->where('type', 'image');
        }

        $core_attach = $core_attach->orderby('created_at', 'desc');
        $count = $core_attach->count();
        $core_attach = $core_attach->offset($offset)->limit($page_size)->get();

        foreach ($core_attach as &$meterial) {
            if ($this->common['islocal']) {
                $meterial['url'] = yz_tomedia($meterial['attachment']);
                unset($meterial['uid']);
            } else {
                $meterial['attach'] = yz_tomedia($meterial['attachment'], true);
                $meterial['url'] = $meterial['attach'];
            }
        }

        $pager = pagination($count, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => '1'));
        $result = array('items' => $core_attach, 'pager' => $pager);

        $array = [
            'message' => [
                'erron' => 0,
                'message' => $result
            ],
            'redirect' => '',
            'type' => 'ajax'
        ];

        return \GuzzleHttp\json_encode($array);
    }

    public function fetch()
    {
        $url = trim(request()->url);
        $size = intval($_FILES['file']['size']);
        $resp = ihttp_get($url);
        if (!$resp) {
            return $this->errorJson('提取文件失败');
        }
        if ($this->common['type'] == 'image') {
            switch ($resp['headers']['Content-Type']) {
                case 'application/x-jpg':
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
                default:
                    return $this->errorJson('提取资源失败, 资源文件类型错误.');
                    break;
            }
        } else {
            return $this->errorJson('提取资源失败, 仅支持图片提取.');
        }

        if (intval($resp['headers']['Content-Length']) > $this->global[$this->common['type'].'_limit'] * 1024) {
            return $this->errorJson('上传的媒体文件过大(' . sizecount($size) . ' > ' . sizecount($this->global[$this->common['type'].'_limit'] * 1024));
        }

        $originname = pathinfo($url, PATHINFO_BASENAME);
        $filename = file_random_name(base_path() . '/static/upload/' . $this->common['folder'], $ext);
        $pathname = $this->common['folder'] . $filename;
        $fullname = base_path() . '/static/upload/' . $pathname;

        if (file_put_contents($fullname, $resp['content']) == false) {
            return $this->errorJson('提取失败');
        }

        return $this->saveData($this->common['type'], $fullname, $originname, $ext, $filename, $this->common['module_upload_dir'], $pathname, $this->common['option']);
    }

    public function errorJson($message = '失败', $error = 1, $data = '')
    {
        return response()->json([
            'data' => $data,
            'error' => $error,
            'message' => $message
        ], 200, ['charset' => 'utf-8']);
    }

    public function common()
    {
        $dest_dir = request()->dest_dir;
        $type = in_array(request()->upload_type, array('image','audio','video')) ? request()->upload_type : 'image';
        $option = array_elements(array('uploadtype', 'global', 'dest_dir'), $_POST);
        $option['width'] = intval($option['width']);
        $option['global'] = request()->global;
        $islocal = request()->local == 'local';

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
            'islocal' => $islocal
        ];
    }

    public function delete()
    {
        $uid = \Auth::guard('admin')->user()->uid;
        $is_founder = $uid == '1' ? 1 : 0;
        $role = AppUser::where('uid', $uid)->first()['role'];
        if (!$is_founder && $role != 'manager' && $role != 'owner') {
            return $this->errorJson('您没有权限删除文件');
        }
        $id = request()->id;
        if (!is_array($id)) {
            $id = array(intval($id));
        }
        $id = safe_gpc_array($id);

        $core_attach = CoreAttach::where('id', $id);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', $uid);
        } else {
            $core_attach = $core_attach->where('uniacid', $this->uniacid);
        }
        $core_attach = $core_attach->first();

        if ($core_attach['upload_type']) {
            $status = file_remote_delete($core_attach['attachment'], $core_attach['upload_type'], $this->remote);
        } else {
            $status = file_delete($core_attach['attachment']);
        }
        if (is_error($status)) {
            return $this->errorJson($status['message']);
        }

        $core_attach->delete();
        if ($core_attach->trashed()) {
            return $this->successJson('删除成功');
        } else {
            return $this->errorJson('删除数据表数据失败');
        }
    }

    public function video()
    {
        $server = $this->common['islocal'] ? 'local' : 'perm';
        $page_index = max(1, request()->page);
        $page_size = 5;
        if ($page_index<=1) {
            $page_index = 0;
            $offset = ($page_index)*$page_size;
        } else {
            $offset = ($page_index-1)*$page_size;
        }

        $material_news_list = material_list('video', $server, array('page_index' => $page_index, 'page_size' => $page_size), $this->uniacid, $offset);
        $material_list = $material_news_list['material_list'];
        $pager = $material_news_list['page'];
        foreach ($material_list as &$item) {
            $item['createtime'] = $item['created_at']->timestamp;
            $item['url'] = yz_tomedia($item['attachment']);
            unset($item['uid']);
        }
        $result = array('items' => $material_list, 'pager' => $pager);
        $array = [
            'message' => [
                'erron' => 0,
                'message' => $result
            ],
            'redirect' => '',
            'type' => 'ajax'
        ];

        return \GuzzleHttp\json_encode($array);
    }
}