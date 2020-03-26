<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/21
 * Time: 下午4:01
 */

namespace app\backend\modules\order\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\order\Express;

class BatchSendController extends BaseController
{
    private $originalName;
    private $reader;
    private $success_num = 0;
    private $err_array = [];
    private $error_msg;
    private $uid =array();

    public function preAction()
    {
        parent::preAction();
        // 生成目录
        if (!is_dir(storage_path('app/public/orderexcel'))) {
            mkdir(storage_path('app/public/orderexcel'), 0777);
        }
    }

    public function index()
    {
        $send_data = request()->send;
        if (\Request::isMethod('post')) {
            if ($send_data['express_company_name'] == "顺丰" && $send_data['express_code'] != "SF") {
                return $this->message('上传失败，请重新上传', Url::absoluteWeb('order.batch-send.index'), 'error');
            }

            if (!$send_data['excelfile']) {
                return $this->message('请上传文件', Url::absoluteWeb('order.batch-send.index'), 'error');
            }

            if ($send_data['excelfile']->isValid()) {
                $this->uploadExcel($send_data['excelfile']);
                $this->readExcel();
                $this->handleOrders($this->getRow(), $send_data);
                $this->sendMessage($this->uid);
                $msg = $this->success_num . '个订单发货成功。';
                return $this->message($msg . $this->error_msg, Url::absoluteWeb('order.batch-send.index'));
            }
        }

        return view('order.batch_send', [])->render();
    }

    /**
     * @name 保存excel文件
     * @param $file
     * @throws ShopException
     * @author
     */
    private function uploadExcel($file)
    {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        if (!in_array($ext, ['xls', 'xlsx'])) {
            throw new ShopException('不是xls、xlsx文件格式！');
        }

        $newOriginalName = md5($originalName . str_random(6)) . $ext;
        \Storage::disk('orderexcel')->put($newOriginalName, file_get_contents($realPath));

        $this->originalName = $newOriginalName;
    }

    /**
     * @name 读取文件
     * @author
     */
    private function readExcel()
    {
        $this->reader = \Excel::load(storage_path('app/public/orderexcel') . '/' . $this->originalName);
    }

    /**
     * @name 获取表格内容
     * @return array
     * @author
     */
    private function getRow()
    {
        $values = [];
        $sheet = $this->reader->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $row = 2;
        while ($row <= $highestRow) {
            $rowValue = array();
            $col = 0;
            while ($col < $highestColumnCount) {
                $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                ++$col;
            }
            $values[] = $rowValue;
            ++$row;
        }

        return $values;
    }

    /**
     * @name 订单发货
     * @param $values
     * @param $send_data
     * @author
     */
    private function handleOrders($values, $send_data)
    {
        foreach ($values as $rownum => $col) {
            $order_sn = trim($col[0]);
            $express_sn = trim($col[1]);
            if (empty($order_sn)) {
                continue;
            }
            if (empty($express_sn)) {
                $this->err_array[] = $order_sn;
                continue;
            }
            if ($order_sn == $express_sn) {
                $this->err_array[] = '发货失败,订单号为' . $order_sn . '快递单号不能与订单编号一致';
//                $this->err_array[] = $order_sn;
                continue;
            }
            $order = Order::select('id', 'order_sn', 'status', 'refund_id','uid')->whereStatus(1)->whereOrderSn($order_sn)->first();

            if (!$order) {
                $this->err_array[] = $order_sn;
                continue;
            }
            $express_model = Express::where('order_id', $order->id)->first();

            !$express_model && $express_model = new Express();

            $express_model->order_id = $order->id;
            $express_model->express_company_name = $send_data['express_company_name'];
            $express_model->express_code = $send_data['express_code'];
            $express_model->express_sn = $express_sn;
            $express_model->save();
            $order->send_time = time();
            $order->status = 2;
            $this->uid[] = $order->uid;
            $order->save();
            $this->success_num += 1;
        }
        $this->setErrorMsg();
    }

    /**
     * @name 设置错误信息
     * @author
     */
    private function setErrorMsg()
    {
        if (count($this->err_array) > 0) {
            $num = 1;
            $this->error_msg = '<br>' . count($this->err_array) . '个订单发货失败,失败的订单编号: <br>';
            foreach ($this->err_array as $k => $v) {
                $this->error_msg .= $v . ' ';
                if (($num % 2) == 0) {
                    $this->error_msg .= '<br>';
                }
                ++$num;
            }
        }
    }

    /**
     * @name 获取示例excel
     * @author
     */
    public function getExample()
    {
        $export_data[0] = ["订单编号", "快递单号"];
        \Excel::create('批量发货数据模板', function ($excel) use ($export_data) {
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
        })->export('xls');
    }

    private function sendMessage($uid)
    {
        try {
            \Log::debug('批量发货短信');

            //sms_send 是否开启
            $smsSet = \Setting::get('shop.sms');
            //是否设置
            if ($smsSet['type'] != 3 || empty($smsSet['aly_templateBalanceCode'])) {
                return false;
            }
            //查询余额,获取余额超过该值的用户，并把没有手机号的筛选掉
            $mobile = Member::uniacid()
                ->WhereIn('uid',$uid)
                ->select('uid', 'mobile')
                ->whereNotNull('mobile')
                ->get();

            if (empty($mobile)) {
                \Log::debug('未找到满足条件会员');
                return false;
            } else {
                $mobile = $mobile->toArray();
            }

            $name = \Setting::get('shop.shop')['name'];
            $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($smsSet['aly_appkey']), trim($smsSet['aly_secret']));

            foreach ($mobile as $key => $value) {
                if (!$value['mobile']) {
                    continue;
                }
                //todo 发送短信
                $response = $aly_sms->sendSms(
                    $smsSet['aly_signname'], // 短信签名
                    $smsSet['aly_templateSendMessageCode'], // 发货提醒短信
                    $value['mobile'], // 短信接收者
                    Array(  // 短信模板中字段的值
                        "shop" => $name,
                    )
                );
                if ($response->Code == 'OK' && $response->Message == 'OK') {
                    \Log::debug($value['mobile'] . '阿里云短信发送成功');
                } else {
                    \Log::debug($value['mobile'] . '阿里云短信发送失败' . $response->Message);
                }
            }

        } catch (\Exception $e) {
            return false;
        }
    }
}