<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午2:27
 */

namespace app\common\traits;

trait JsonTrait
{

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
        response()->json([
            'result' => 0,
            'msg' => $message,
            'data' => $data
        ], 200, ['charset' => 'utf-8'])->send();
        exit();
    }
}