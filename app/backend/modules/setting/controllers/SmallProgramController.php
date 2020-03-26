<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/8
 * Time: 下午4:20
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use app\backend\modules\setting\controllers\SmallProgramDataController;
use app\backend\modules\setting\controllers\DiyTempController;
use app\common\models\notice\MinAppTemplateMessage;
use app\common\services\notice\SmallProgramNotice;
use app\common\models\MemberMiniAppModel;

class SmallProgramController extends BaseController
{
    private $temp_model;
    protected $SmallProgramNotice;
    public function __construct(){
        $this->SmallProgramNotice = new SmallProgramNotice();
    }
//    public function index()
//    {
//        $kwd = request()->keyword;
//        $list = MinAppTemplateMessage::getList();
//        return view('setting.small-program.list', [
//            'list' => $list->toArray(),
//            'kwd' => $kwd,
//            'url'=>'setting.small-program.save'
//        ])->render();
//    }

    public function index()
    {

        $mini = new MinAppTemplateMessage();
        if (empty($mini->getList()->toArray())) {
            $this->initialTemplate();
        }
        $list = $this->SmallProgramNotice->getExistTemplateList();
        if ($list['errcode'] != 0 || !isset($list['errcode'])){
            return $this->message('获取模板失败'.$list,'', 'error');
        }
        return view('setting.small-program.detail', [
                'list'=>$list['list'],
                'url'=>'setting.small-program.save'
            ])->render();
    }
    private function initialTemplate(){
        $title_list = [
            'AT0009' => ['52','5','3','7','10','12','6'],
            'AT0036' => ['36','3','5','4','14'],
            'AT0007' => ['51','7','77','104','96','3','2','23'],
            'AT0024' => ['41','7','2','4','75','6','16'],
            'AT1168' => ['8','2','3','6','4','1'],
            'AT0257' => ['15','14','17','18'],
            'AT0210' => ['5','1','10','12','3'],
            'AT0241' => ['7','5','2','12','10','8'],
            'AT0637' => ['7','20','35','24','4','25'],
            'AT0787' => ['82','26','15','28','17','78'],
            'AT1983' => ['9','2','13','7','16','4'],
            'AT0686' => ['15','2','7','20','21','11','12'],
            'AT0157' => ['17','8','7','6','9'],
            'AT0002' => ['59','40','5','55','19','4']
            ];
        foreach ($title_list as $key=>$keyword){
            $Result = $this->SmallProgramNotice->getAddTemplate($key,$keyword);
        }
        $tempList = $this->SmallProgramNotice->getExistTemplateList();
         foreach ($tempList['list'] as $temp){
             if ($tempList['errcode'] == 0) {
                 $ret = MinAppTemplateMessage::create([
                     'uniacid' => \YunShop::app()->uniacid,
                     'title' => $temp['title'],
                     'template_id' => $temp['template_id'],
                     'is_default' => 1,
                     'data' => $temp['content'],
                 ]);
             }
         }
    }

    public function setNotice(){
        $tempId = request()->id;
        $tempOpen = request()->open;
        $is = MinAppTemplateMessage::isOpen($tempId,$tempOpen);
        $is_open = $is ? 1 : 0;
        echo json_encode([
            'result' => $is_open,
        ]);
    }
//    public function addTmp()
//    {
//        if (!request()->templateidshort) {
//            return $this->errorJson('请填写模板编码');
//        }
//        $ret = $this->WechatApiModel->getTmpByTemplateIdShort(request()->templateidshort);
//        if ($ret['status'] == 0) {
//            return $this->errorJson($ret['msg']);
//        } else {
//            return $this->successJson($ret['msg'], []);
//        }
//    }
//
//    public function getTemplateKey(){
//        if (isset(request()->key_val)){
//            $ret = $this->save(request()->all());
//            if (!$ret){
//                return $this->message('添加模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
//            }
//            return $this->message('添加模板成功', Url::absoluteWeb('setting.small-program.index'));
//        }
//        $page = request()->page;
//        if (isset(request()->id)){
//            $key_list = $this->SmallProgramNotice->getTemplateKey(request()->id);
//            if ($key_list['errcode'] != 0 || !isset($key_list['errcode'])){
//                return $this->message('获取模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
//            }
//            $keyWord = $key_list['keyword_list'];
//        }
//        return view('setting.small-program.detail', [
//            'keyWord'=>$keyWord,
//            'list'=>$this->SmallProgramNotice->getAllTemplateList($page)['list'],
//            'page'=>$page,
//            'title'=>request()->title,
//            'url'=>'setting.small-program.save'
//        ])->render();
//   }
//   public function save($list){
//       $strip = 0;
//        $date['data'] = [];
//        foreach ($list['key_val'] as $value){
//            $key_list[] = explode(":",$value)[0];
//        }
//        $template_date = $this->SmallProgramNotice->getAddTemplate($list['id'],$key_list);
//       if ($template_date['errcode'] != 0 || !isset($template_date['errcode'])){
//           return $this->message('添加模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
//       }
//        if ($template_date['errcode'] == 0){
//            $ret = MinAppTemplateMessage::create([
//                'uniacid' => \YunShop::app()->uniacid,
//                'title' =>  $list['title'],
//                'template_id' => $template_date['template_id'],
//                'keyword_id'=>implode(",", $list['key_val']),
//                'title_id'=>$list['id'],
//                'offset'=>$list['offset']
//            ]);
//           return $ret;
//        }
//   }

//    public function edit()
//    {
//        if (request()->id) {
//            $min_small = new MinAppTemplateMessage;
//            $temp_date = $min_small::getTemp(request()->id);//获取数据表中的数据
//            $key_list = $this->SmallProgramNotice->getTemplateKey($temp_date->title_id);
//        }
//        if (request()->key_val) {
//            foreach (request()->key_val as $value){
//                $keyWord_list[] = explode(":",$value)[0];
//            }
//            $template_date = $this->SmallProgramNotice->getAddTemplate($temp_date->title_id, $keyWord_list);
//            if ($template_date['errcode'] != 0 || !isset($template_date['errcode'])){
//                return $this->message('修改模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
//            }
//            $del_temp = $this->SmallProgramNotice->deleteTemplate($temp_date->template_id);//删除原来的模板
//            $temp_date->keyword_id = implode(",", request()->key_val);
//            $temp_date->template_id =$template_date['template_id'];
//            $ret = $temp_date->save();
//            if (!$ret) {
//                return $this->message('修改模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
//            }
//            return $this->message('修改模板成功', Url::absoluteWeb('setting.small-program.index'));
//        }
//
//        if ($key_list['errcode']==0){
//            $keyWord = $key_list['keyword_list'];
//        }
//        return view('setting.small-program.detail', [
//            'keyWord'=>$keyWord,
//            'is_edit'=>0,
//            'title'=>$temp_date->title,
//            'id'=>$temp_date->title_id,
//            'list'=>$this->SmallProgramNotice->getAllTemplateList($temp_date->offset)['list'],
//            'page'=>$temp_date->offset,
//            'url'=>'setting.small-program.save'
//        ])->render();
//        }

        public function notice()
    {
        $notice = \Setting::get('mini_app.notice');
        $requestModel = \YunShop::request()->yz_notice;
        $temp_list = MinAppTemplateMessage::getList();
        if (!empty($requestModel)) {
            if (\Setting::set('mini_app.notice', $requestModel)) {
                return $this->message(' 消息提醒设置成功', Url::absoluteWeb('setting.small-program.notice'));
            } else {
                $this->error('消息提醒设置失败');
            }
        }
        return view('setting.small-program.notice', [
            'set' => $notice,
            'temp_list' => $temp_list
        ])->render();
    }

    public function see()
    {
        $list = $this->SmallProgramNotice->getExistTemplateList();
        $tmp = '';
        foreach ($list['list'] as $temp) {
            while ($temp['template_id'] == request()->tmp_id)
            {
                $tmp = $temp;
                break;
            }
        }
        return view('setting.wechat-notice.see', [
            'template' => $tmp,
            'notice_type'=>2
        ])->render();
    }

//     public function del()
//     {
//            if (request()->template_id) {
//                $min_small = new MinAppTemplateMessage;
//                $temp = $min_small::getTemp(request()->template_id);
//                if (empty($temp)) {
//                    return $this->message('找不到该模板', Url::absoluteWeb('setting.small-program.index'), 'error');
//                }
//                $ret = $list = $this->SmallProgramNotice->deleteTemplate(request()->template_id);
//                if ($ret['errcode'] == 0) {
//                    $min_small->delTempDataByTempId($temp->id);
////                    $kwd = request()->keyword;
//                    $list = MinAppTemplateMessage::getList();
//                    return view('setting.small-program.list', [
//                        'list' => $list->toArray(),
////                        'kwd' => $kwd,
//                        'url' => 'setting.small-program.save'
//                    ])->render();
//                }
//            }
//     }

    }