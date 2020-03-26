<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/7
 * Time: 下午2:01
 */

namespace app\common\models\order;


use app\common\models\BaseModel;
use Ixudra\Curl\Facades\Curl;

/**
 * Class Express
 * @package app\common\models\order
 * @property string express_code
 * @property string express_sn
 * @property string tel
 * @property string status_name
 * @property string express_company_name
 * @property array data
 */
class Express extends BaseModel
{
    public $table = 'yz_order_express';

    protected $search_fields = ['express_sn', 'express_company_name'];

    protected $guarded = ['data'];
    protected $appends = ['tel'];

    public function getExpress($express = null, $express_sn = null)
    {

        if (!isset($express_sn)) {
            $express_sn = $this->express_sn;
        }
//        $result = $this->kD100($express,$express_sn);
        $result = app('express')->getTraces($express, $express_sn);

        if (empty($result)) {
            return array();
        }
        $result['status_name'] = $this->expressStatusName($result['state']);

        return $result;
    }

    private function kD100($express, $express_sn)
    {
        $url = sprintf('https://m.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=%s', $express, $express_sn, time());

        $result = Curl::to($url)
            ->asJsonResponse(true)->get();

        return $result;
    }

    public function getTelAttribute()
    {
        $mapping = '{"\u51e1\u5ba2\u914d\u9001": "400-010-6660G", "\u90d1\u5dde\u5efa\u534e": "0371-65995266", "\u5b89\u80fd\u7269\u6d41": "400-104-0088", "\u95fd\u76db\u5feb\u9012": "0592-3725988", "USPS\u5feb\u9012": "800-275-8777W", "\u8fd0\u901a\u4e2d\u6e2f": "0769-81156999Z", "\u4ebf\u987a\u822a": "4006-018-268", "\u94f6\u6377\u901f\u9012": "0755-88250666", "COE\u5feb\u9012": "0755-83575000", "\u504c\u4e9a\u5965\u5feb\u9012": "400-887-1871O", "EMS\u5feb\u9012": "11183F", "GLS\u5feb\u9012": "877-914-5465", "\u6613\u901a\u8fbe": "0898-65339299", "\u54c1\u901f\u5fc3\u8fbe\u5feb\u9012": "400-800-3693 Q", "\u539f\u98de\u822a\u5feb\u9012": "0755-29778899", "\u8de8\u8d8a\u901f\u8fd0": "400-809-8098", "\u4f20\u559c\u7269\u6d41": "400-777-5656 D", "\u4e00\u90a6\u901f\u9012": "400-800-0666", "\u7533\u901a\u5feb\u9012": "95543", "\u534e\u4f01\u5feb\u8fd0": "400-626-2356", "\u4e50\u6377\u9012": "400-618-1400", "\u6052\u8def\u7269\u6d41": "400-182-6666J", "\u5fe0\u4fe1\u8fbe": "400-646-6665", "\u6e90\u5b89\u8fbe": "0769-85021875", "\u7965\u9f99\u8fd0\u901a": "0755-88888908", "\u76db\u4e30\u7269\u6d41": "0591-83621111", "\u9012\u56db\u65b9": "0755-33933895", "\u987a\u4e30": "95338", "\u6797\u9053\u56fd\u9645\u5feb\u9012": "400-820-0112", "\u9f99\u90a6\u901f\u9012": "021-39283333", "\u5982\u98ce\u8fbe": "400-010-6660S", "\u4e2d\u901a\u901f\u9012": "95311", "KCS\u5feb\u9012": "800-858-5590", "\u8d8a\u4e30\u7269\u6d41": "852-23909969", "\u5fae\u7279\u6d3e": "400-6363-000", "\u98ce\u884c\u5929\u4e0b\u5feb\u9012": "400-040-4909", "\u4e2d\u901f\u5feb\u9012": "11183", "\u95e8\u5bf9\u95e8": "400-700-7676N", "OCS\u5feb\u9012": "400-118-8588", "\u6377\u7279\u5feb\u9012": "400-820-8585", "\u5b89\u8fc5\u7269\u6d41": "010-59288730B", "\u901a\u548c\u5929\u4e0b\u5feb\u9012": "400-0056-516 U", "\u4e2d\u94c1\u5feb\u8fd0": "95572", "FedEx\u56fd\u9645": "400-886-1888", "\u6e90\u4f1f\u4e30": "400-601-2228", "\u52a0\u8fd0\u7f8e": "0769-85515555", "\u90ae\u5fc5\u4f73": "400-687-8123", "\u4f73\u6021\u7269\u6d41": "400-631-9999", "\u5e73\u5b89\u8fbe\u817e\u98de": "4006-230-009", "\u65b0\u86cb\u7269\u6d41": "400-820-4400", "\u5168\u5cf0\u5feb\u9012": "400-100-0001", "\u5168\u65e5\u901a": "020-86298999", "BHT\u5feb\u9012": "010-58633508", "\u90a6\u9001\u7269\u6d41": "021-20965696", "\u660a\u76db\u7269\u6d41": "400-186-5566", "\u4e2d\u94c1\u7269\u6d41": "400-000-5566", "\u4e2d\u777f\u901f\u9012": "400-0375-888", "\u7a57\u4f73\u7269\u6d41": "400-880-9771T", "D\u901f\u5feb\u9012": "0531-88636363", "\u8d5b\u6fb3\u9012\u5feb\u9012": "400-034-5888", "DHL\u4e2d\u56fd\u5feb\u9012": "95380", "\u5168\u6668\u5feb\u9012": "0769-82026703", "\u8054\u660a\u901a": "0769-88620000M", "\u5feb\u6377\u901f\u9012": "4008333666", "\u9012\u8fbe\u901f\u8fd0": "400-687-8123", "\u5723\u5b89\u7269\u6d41": "400-661-8169", "\u5609\u91cc\u5927\u901a": "400-610-3188", "\u80fd\u8fbe\u901f\u9012": "400-6886-765", "\u4e07\u5bb6\u7269\u6d41": "021-5193 7018X", "\u767e\u798f\u4e1c\u65b9\u5feb\u9012": "010-57169000C", "\u534e\u590f\u9f99": "0755-61211999", "\u6025\u5148\u8fbe": "400-694-1256", "\u91d1\u5927\u7269\u6d41": "0755-82262209K", "\u4e2d\u90ae\u7269\u6d41": "11183", "UPS\u5feb\u9012": "400-820-8388", "\u7acb\u5373\u9001": "400-028-5666", "\u4e9a\u98ce\u901f\u9012": "400-628-0018", "\u5929\u5730\u534e\u5b87\u5feb\u9012": "400-808-6666", "OnTrac\u5feb\u9012": "800-334-5000P", "\u6d77\u76df\u901f\u9012": "400-080-6369", "\u6c47\u5f3a\u5feb\u9012": "400-000-0177", "GSM\u5feb\u9012": "021-64656011", "\u5927\u7530\u7269\u6d41": "400-626-1166", "\u51e1\u5b87\u5feb\u9012": "400-658-0358", "\u8054\u90a6\u5feb\u9012": "400-889-1888", "\u5143\u667a\u6377\u8bda": "400-081-2345", "\u5706\u901a\u901f\u9012": "95554", "\u4e09\u6001\u901f\u9012": "400-881-8106", "\u65b0\u90a6\u7269\u6d41": "400-800-0222", "\u5b89\u4fe1\u8fbe": "021-54224681", "\u97f5\u8fbe\u5feb\u9012": "95546", "\u6c11\u90a6\u901f\u9012": "0769-81515303", "\u6c11\u822a\u5feb\u9012": "400-817-4008", "AAE\u5feb\u9012": "400-610-0400", "\u4fe1\u4e30\u7269\u6d41": "400-830-6333", "\u4eac\u5e7f\u901f\u9012": "0769-83660666", "\u4e2d\u5916\u8fd0\u901f\u9012": "010-80418611", "\u76db\u8f89\u7269\u6d41": "400-822-2222", "\u5b87\u946b\u7269\u6d41": "0371-66368798", "City-Link": "603-55658399", "\u6cb3\u5317\u5efa\u534e": "0311-86123186", "\u4e07\u8c61\u7269\u6d41": "400-820-8088", "\u664b\u8d8a\u5feb\u9012": "0769-85158039", "\u98de\u8c79\u5feb\u9012": "400-000-5566", "\u5e0c\u4f18\u7279": "400-840-0365Y", "FedEx\u7f8e\u56fd\u4ef6": "800-463-3339", "\u56fd\u901a\u5feb\u9012": "95327", "\u901f\u5c14\u5feb\u9012": "400-882-2168", "\u4f18\u901f\u7269\u6d41": "400-1111-119", "\u5df4\u4f26\u652f": "400-636-1516", "\u4e03\u5929\u8fde\u9501\u5feb\u9012": "400-882-1202R", "\u5168\u4e00\u5feb\u9012": "400-663-1111", "\u8fdc\u6210\u7269\u6d41": "400-820-1646", "\u660e\u4eae\u7269\u6d41": "400-035-6568", "TNT\u5feb\u9012": "800-820-9868", "\u767e\u4e16\u5feb\u9012\u5feb\u9012": "400-956-5656", "DPEX\u5feb\u9012": "021-64659883", "\u4f73\u5409\u5feb\u8fd0": "400-820-5566", "\u5927\u6d0b\u7269\u6d41": "400-820-0088E", "\u5eb7\u529b\u7269\u6d41": "400-156-5156 L", "\u5168\u9645\u901a": "400-0179-888", "\u4e0a\u5927\u7269\u6d41": "021-54477891", "\u5171\u901f\u8fbe": "400-111-0005H", "\u4ebf\u9886\u901f\u8fd0": "400-611-1892"}';
        $mapping = json_decode($mapping, true);

        return array_get($mapping, $this->express_company_name, '');
    }


    private function expressStatusName($key)
    {
        $state_name_map = [
            2 => '在途中',
            3 => '签收',
            4 => '问题件',
        ];
        return $state_name_map[$key];
    }
}