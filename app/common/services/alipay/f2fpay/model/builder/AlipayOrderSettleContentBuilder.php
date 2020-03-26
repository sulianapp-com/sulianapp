<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/18
 * Time: 下午2:09
 */
namespace app\common\services\alipay\f2fpay\model\builder;

class AlipayOrderSettleContentBuilder extends ContentBuilder
{
    private $bizContent = NULL;
    private $royaltyParameters;
    private $outRequestNo;
    private $tradeNo;
    private $grantType;

    private $bizParas = array();


    public function __construct()
    {
    }

    public function AlipayTradePayContentBuilder()
    {
        $this->__construct();
    }

    public function getBizContent()
    {
        if(!empty($this->bizParas)){
            $this->bizContent = json_encode($this->bizParas,JSON_UNESCAPED_UNICODE);
        }

        return $this->bizContent;
    }


    public function getGrantType()
    {
        return $this->grantType;
    }

    public function setGrantType($grantType)
    {
        $this->grantType = $grantType;
        $this->bizParas['grant_type'] = $grantType;
    }

    public function setOutRequestNo($outRequestNo)
    {
        $this->outRequestNo = $outRequestNo;
        $this->bizParas['out_request_no'] = $outRequestNo;
    }

    public function getOutRequestNo()
    {
        return $this->outRequestNo;
    }
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
        $this->bizParas['trade_no'] = $tradeNo;
    }

    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    public function setRoyaltyParameters($royaltyParameters)
    {
        $this->royaltyParameters = $royaltyParameters;
        $this->bizParas['royalty_parameters'] = $royaltyParameters;
    }

    public function getRoyaltyParameters()
    {
        return $this->royaltyParameters;
    }
}

