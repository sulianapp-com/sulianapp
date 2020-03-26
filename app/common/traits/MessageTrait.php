<?php
namespace app\common\traits;

use Illuminate\Contracts\Support\MessageBag;

/**
 * 消息提醒类
 *
 * ```php
 * $this->error('Message');
 * $this->success('Message');
 * $this->info('Message');
 * $this->warning('Message');
 * $this->overlay('Modal Message', 'Modal Title');
 * $this->error('Message')->important();
 * ```
 * Author: 芸众商城 www.yunzshop.com
 * Date: 26/02/2017
 * Time: 08:03
 */
trait MessageTrait
{
    /**
     * 显示错误信息
     * @param string $message
     * @return \Laracasts\Flash\FlashNotifier
     */
    public function error($message = '')
    {
        return flash($this->_messageShow($message), 'danger');

    }

    /**
     * 显示成功信息
     * @param string $message
     * @return \Laracasts\Flash\FlashNotifier
     */
    public function success($message = '')
    {
        return flash($this->_messageShow($message), 'success');
    }

    /**
     * 显示info
     * @param string $message
     * @return \Laracasts\Flash\FlashNotifier
     */
    public function info($message = '')
    {
        return flash($this->_messageShow($message), 'info');
    }

    /**
     * 显示警告信息
     * @param string $message
     * @return \Laracasts\Flash\FlashNotifier
     */
    public function warning($message = '')
    {
        return flash($this->_messageShow($message), 'warning');
    }

    /**
     * 显示弹窗modal
     * @param string $message
     * @param string $title
     * @return $this
     */
    public function overlay($message = '', $title = '')
    {
        return flash()->overlay($this->_messageShow($message), $title);
    }

    private function _messageShow($message = '')
    {
        $messageStr = '';
        if ($message instanceof MessageBag) {
            $msgs = $message->toArray();
            $messageStr = '<ul style="list-style-type:disc;margin-left:10px">';
            foreach ($msgs as $fields) {
                foreach ($fields as $field){
                    is_array($field) && $messageStr .= '<li>' . implode('</li><li>', $field) . '</li>';
                    is_string($field) && $messageStr .= '<li>' . $field . '</li>';
                }
            }
            $messageStr .= '</ul>';
        } else {
            $messageStr = $message;
        }
        return $messageStr;
    }

    /**
     * 显示信息并跳转
     *
     * @param $message
     * @param string $redirect
     * @param string $status success  error danger warning  info
     * @return mixed
     */
    public function message($message, $redirect = '', $status = 'success')
    {
        if (config('app.framework') == 'platform') {
            return response()->view('message', [
                'redirect' => $redirect,
                'message' => $message,
                'status' => $status
            ])->send();
        }

        return view('message', [
            'redirect' => $redirect,
            'message' => $message,
            'status' => $status
        ]);
    }
}