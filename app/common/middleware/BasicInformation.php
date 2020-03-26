<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午11:53
 */

namespace app\common\middleware;


use app\common\traits\JsonTrait;
use app\frontend\controllers\HomePageController;
use app\frontend\modules\finance\controllers\PopularizePageShowController;
use app\frontend\modules\member\controllers\MemberController;


class BasicInformation
{
    use JsonTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        if(!$response)
        {
            return $response;
        }
        $content = $response->getContent();
        $response->setContent($this->handleContent($request,$content));
        return $response;
    }

    private function handleContent($request,$content)
    {
        $content = json_decode($content,true);
        if(request()->basic_info == 1)
        {
            $content = array_merge($content,['basic_info'=>$this->getBasicInfo($request)]);
        }
        if(request()->validate_page == 1)
        {
            $content = array_merge($content,$this->getValidatePage($request));
        }
        return json_encode($content);
    }

    private function getBasicInfo($request)
    {
        return [
            'popularize_page' =>  (new PopularizePageShowController())->index($request,true)['json'],
            'home' => (new HomePageController())->index($request,true)['json'],
            'balance' => (new HomePageController())->getBalance()['json'],
            'lang' => (new HomePageController())->getLangSetting()['json'],
            'globalParameter' => (new HomePageController())->globalParameter(true)['json'],
        ];
    }

    private function getValidatePage($request)
    {
        return ['validate_page'=>(new MemberController())->isValidatePage($request,true)['json']];
    }
}