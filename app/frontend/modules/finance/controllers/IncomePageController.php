<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午2:11
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\helpers\ImageHelper;
use app\common\models\Income;
use app\common\services\popularize\PortType;
use app\frontend\models\Member;
use app\frontend\models\MemberRelation;
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\finance\factories\IncomePageFactory;
use app\frontend\modules\finance\services\PluginSettleService;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\services\MemberService;
use Yunshop\Designer\home\IndexController;

class IncomePageController extends ApiController
{
    private $relationSet;
    private $is_agent;
    private $grand_total;
    private $usable_total;


    public function preAction()
    {
        parent::preAction();
        $this->relationSet = $this->getRelationSet();
    }

    /**
     * 收入页面接口
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function index($request)
    {
        $this->dataIntegrated(['status' => 1, 'json' => ''],'template_set');
        $this->dataIntegrated($this->getIncomePage($request, true),'income_page');
        if(app('plugins')->isEnabled('designer'))
        {
            $this->dataIntegrated((new IndexController())->templateSet($request, true),'template_set');
        }
        return $this->successJson('', $this->apiData);
    }

    public function getIncomePage($request, $integrated = null)
    {
        $member_id = \YunShop::app()->getMemberId();

        list($available, $unavailable) = $this->getIncomeInfo();

        //检测是否推广员
        $this->is_agent = $this->isAgent();

        //不是推广员且有设置跳转链接时
        $relation_set = \Setting::get('member.relation');
        $extension_set = \Setting::get('popularize.mini');
        $jump_link = '';
        $small_jump_link = '';
        $small_extension_link = '';
        if ($relation_set['is_jump'] && !empty($relation_set['jump_link'])) {
            if (!$this->is_agent) {
                $jump_link = $relation_set['jump_link'];
                $small_jump_link = $relation_set['small_jump_link'];
                $small_extension_link = $extension_set['small_extension_link'];
                if(is_null($integrated)){
                    return $this->successJson('ok', ['jump_link' => $jump_link,'small_jump_link'=>$small_jump_link,'small_extension_link'=>$small_extension_link]);
                }else{
                    return show_json(1,['jump_link' => $jump_link,'small_jump_link'=>$small_jump_link,'small_extension_link'=>$small_extension_link]);
                }
            }
        }

        list($available, $unavailable) = $this->getIncomeInfo();

        //添加商城营业额
        $is_show_performance = OrderAllController::isShow();

        $data = [
            'info' => $this->getPageInfo(),
            'parameter' => $this->getParameter(),
            'available' => $available,
            'unavailable' => $unavailable,
            'is_show_performance' => $is_show_performance,
            'jump_link' => $jump_link,
            'small_jump_link' => $small_jump_link,
            'small_extension_link' => $small_extension_link
        ];
        if(is_null($integrated)){
            return $this->successJson('ok', $data);
        }else{
            return show_json(1,$data);
        }
    }
    
    /**
     * 页面信息
     *
     * @return array
     */
    private function getPageInfo()
    {
        $autoWithdraw = 0;
        if (app('plugins')->isEnabled('mryt')) {
            $uid = \YunShop::app()->getMemberId();
            $autoWithdraw = (new \Yunshop\Mryt\services\AutoWithdrawService())->isWithdraw($uid);
        }

        if (app('plugins')->isEnabled('team-dividend')) {
            $uid = \YunShop::app()->getMemberId();
            $autoWithdraw = (new \Yunshop\TeamDividend\services\AutoWithdrawService())->isWithdraw($uid);
        }

        $member_id = \YunShop::app()->getMemberId();

        $memberModel = Member::select('nickname', 'avatar', 'uid')->whereUid($member_id)->first();

        //IOS时，把微信头像url改为https前缀
        $avatar = ImageHelper::iosWechatAvatar($memberModel->avatar);
        return [
            'avatar' => $avatar,
            'nickname' => $memberModel->nickname,
            'member_id' => $memberModel->uid,
            'grand_total' => $this->grand_total,
            'usable_total' => $this->usable_total,
            'auto_withdraw' => $autoWithdraw,
        ];
    }


    private function getParameter()
    {
        return [
            'share_page' => $this->getSharePageStatus(),
            'plugin_settle_show' => PluginSettleService::doesIsShow(),  //领取收益 开关是否显示
        ];
    }


    /**
     * 收入信息
     * @return array
     * @throws \app\common\exceptions\AppException
     */
    private function getIncomeInfo()
    {
        $lang_set = $this->getLangSet();

        $is_relation = $this->isOpenRelation();

        $config = $this->getIncomePageConfig();

        $total_income = $this->getTotalIncome();

        //是否显示推广插件入口
        $popularize_set = PortType::popularizeSet(\YunShop::request()->type);

        $available = [];
        $unavailable = [];
        foreach ($config as $key => $item) {

            $incomeFactory = new IncomePageFactory(new $item['class'], $lang_set, $is_relation, $this->is_agent, $total_income);

            if (!$incomeFactory->isShow()) {
                continue;
            }

            //不显示
            if (in_array($incomeFactory->getAppUrl(), $popularize_set)) {
                continue;
            }

            $income_data = $incomeFactory->getIncomeData();

            if ($incomeFactory->isAvailable()) {
                $available[] = $income_data;
            } else {
                $unavailable[] = $income_data;
            }

            //unset($incomeFactory);
            //unset($income_data);
        }

        return [$available, $unavailable];
    }


    /**
     * 获取商城中的插件名称自定义设置
     *
     * @return mixed
     */
    private function getLangSet()
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);

        return $lang[$lang['lang']];
    }


    /**
     * 是否开启关系链 todo 应该提出一个公用的服务
     *
     * @return bool
     */
    private function isOpenRelation()
    {
        if (!is_null($this->relationSet) && 1 == $this->relationSet->status) {
            return true;
        }
        return false;
    }


    private function getSharePageStatus()
    {
        if (!is_null($this->relationSet) && 1 == $this->relationSet->share_page) {
            return true;
        }
        return false;
    }


    private function getRelationSet()
    {
        return MemberRelation::uniacid()->first();
    }

    private function getTotalIncome()
    {
        $total_income =Income::selectRaw('member_id, incometable_type, sum(amount) as total_amount, sum(if(status = 0, amount, 0)) as usable_total')
            ->whereMember_id(\YunShop::app()->getMemberId())
            ->groupBy('incometable_type', 'member_id')
            ->get();

        //计算累计收入
        $this->grand_total = sprintf("%.2f",$total_income->sum('total_amount'));
        $this->usable_total = sprintf("%.2f",$total_income->sum('usable_total'));
        return $total_income;
    }


    /**
     * 登陆会员是否是推客
     *
     * @return bool
     */
    private function isAgent()
    {
        return MemberModel::isAgent();
    }


    /**
     * 收入页面配置 config
     *
     * @return mixed
     */
    private function getIncomePageConfig()
    {
        return \app\backend\modules\income\Income::current()->getPageItems();
    }


    //累计收入
    private function getGrandTotal()
    {
        return $this->getIncomeModel()->sum('amount');
    }

    //可提现收入
    private function getUsableTotal()
    {
        return $this->getIncomeModel()->where('status', 0)->sum('amount');
    }


    private function getIncomeModel()
    {
        $member_id = \YunShop::app()->getMemberId();

        return Income::uniacid()->where('member_id',$member_id);
    }

}
