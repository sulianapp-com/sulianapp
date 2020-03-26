<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/20 下午3:23
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\income\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\helpers\QrCodeHelper;
use app\frontend\models\Income;
use app\frontend\models\Member;
use app\frontend\models\MemberRelation;
use Carbon\Carbon;
use Yunshop\Poster\models\Qrcode;

class SharePageController extends ApiController
{
    private $memberModel;


    private $relationSet;


    public function preAction()
    {
        parent::preAction();
        $this->relationSet = $this->getRelationSet();
    }


    public function index()
    {

        if ($this->getSharePageStatus()) {

            $this->memberModel = $this->getMemberModel();

            return $this->successJson('ok', $this->getResultData());
        }
        return $this->errorJson('我的收入页面未开启');
    }


    private function getResultData()
    {
        return [
            'avatar' => $this->memberModel->avatar,
            'share_qr' => $this->getShareQrUrl(),
            'nickname' => $this->getNickname(),
            'member_id' => $this->getMemberId(),
            'member_level' => $this->getLevelName(),
            'today_income' => $this->getTodayIncome(),
            'month_income' => $this->getMonthIncome(),
            'total_income' => $this->getTotalIncome(),
        ];
    }


    /**
     * 今日收入
     *
     * @return double
     */
    private function getTodayIncome()
    {
        $start_time = Carbon::today()->startOfDay()->timestamp;
        $end_time = Carbon::today()->endOfDay()->timestamp;

        return Income::whereBetween('created_at', [$start_time, $end_time])->sum('amount');
    }


    /**
     * 本月收入
     *
     * @return double
     */
    private function getMonthIncome()
    {
        $start_time = Carbon::now()->startOfMonth()->timestamp;
        $end_time = Carbon::now()->endOfMonth()->timestamp;

        return Income::whereBetween('created_at', [$start_time, $end_time])->sum('amount');
    }


    /**
     * 累计收入
     *
     * @return double
     */
    private function getTotalIncome()
    {
        return Income::sum('amount');
    }


    private function getShareQrUrl()
    {
        if ($this->memberModel->yzMember->is_agent == 1 && $this->memberModel->yzMember->status == 2) {

            $url = yzAppFullUrl('member', ['mid' => $this->memberModel->uid]);

            return \QrCode::size(240)->get($url,'app/public/qr/share')['url'];
        }
        return "";

        /*if (app('plugins')->isEnabled('poster')) {

            $member_id = $this->getMemberId();

            $qrCodeModel = Qrcode::uniacid()->where('scene_str', 'like', '%' . $member_id)->orderBy('createtime', 'desc')->first();
            if ($qrCodeModel) {
                $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode";

                return $url . "?ticket={$qrCodeModel->ticket}";
            }
        }
        return '';*/

    }


    /**
     * 会员昵称
     *
     * @return string
     */
    private function getNickname()
    {
        return $this->memberModel->realname ?: $this->memberModel->nickname;
    }


    /**
     * 会员等级名称
     *
     * @return string
     */
    private function getLevelName()
    {
        $level_name = $this->memberModel->yzMember->level->level_name;

        return $level_name ?: $this->getDefaultMemberLevel();
    }


    /**
     * 会员默认等级名称
     *
     * @return string
     */
    private function getDefaultMemberLevel()
    {
        $member_set = \Setting::get('shop.member');

        return $member_set['level_name'] ?: '普通会员';
    }


    /**
     *
     *
     * @return Member
     * @throws AppException
     */
    private function getMemberModel()
    {
        $member_id = $this->getMemberId();

        $memberModel = Member::getUserInfos($member_id)->first();
        if (!$memberModel) {
            throw new AppException('会员信息错误，请重试！');
        }
        return $memberModel;
    }


    private function getMemberId()
    {
        $member_id = \YunShop::app()->getMemberId();
        if (!$member_id) {
            throw new AppException('Please login in.');
        }
        return $member_id;
    }


    private function getSharePageStatus()
    {
        if (is_null($this->relationSet) || 1 == $this->relationSet->share_page) {
            return true;
        }
        return false;
    }


    private function getRelationSet()
    {
        return MemberRelation::uniacid()->first();
    }

}
