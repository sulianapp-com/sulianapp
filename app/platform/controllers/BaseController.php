<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/20
 * Time: 上午10:53
 */

namespace app\platform\controllers;

use app\common\components\BaseController as Controller;

class BaseController extends Controller
{
    /*
     * 基础跳转公共方法
     * @param 1 $path 跳转路径
     * @param2 $message 响应提示
     * @param3  $isSuccess 是否是成功， 默认成功
     */
    protected function commonRedirect($path, $message = '', $isSuccess = 'success')
    {
        switch ($isSuccess){
            case 'success' :
                return redirect($path)->withSuccess($message .'成功！');
            case 'failed' :
                return redirect($path) ->withErrors($message.'失败！');
            case 'error' :
                return redirect($path)->withErrors("找不到该记录!");
            default :
                break;
        }
    }

    /**
     * 接口返回成功 JSON格式
     * @param string $message 提示信息
     * @param array $data 返回数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function successJson($message = '成功', $data = [])
    {
        return response()->json([
            'result' => 1,
            'msg' => $message,
            'data' => $data
        ], 200, ['charset' => 'utf-8']);
    }

    /**
     * 接口返回错误JSON 格式
     * @param string $message 提示信息
     * @param array $data 返回数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorJson($message = '失败', $data = [])
    {
        return response()->json([
            'result' => 0,
            'msg' => $message,
            'data' => $data
        ], 200, ['charset' => 'utf-8']);
    }
}