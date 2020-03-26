<?php
namespace app\common\helpers;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午1:11
 */
class PaginationHelper
{
    /**
     * 获取分页导航HTML
     * @param int $total 总记录数
     * @param int $pageIndex 当前页码
     * @param int $pageSize 每页显示条数
     * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
     * @param array $context
     * @return string
     */
    public static function show($total, $pageIndex, $pageSize = 15, $url = '', $context = []) {

        !$context && $context = ['before' => 5, 'after' => 4, 'ajaxcallback' => '', 'callbackfuncname' => ''];
        $pdata = [
            'tcount' => 0,
            'tpage' => 0,
            'cindex' => 0,
            'findex' => 0,
            'pindex' => 0,
            'nindex' => 0,
            'lindex' => 0,
            'options' => ''
        ];
        $context['isajax'] = false;
        if (isset($context['ajaxcallback']) && $context['ajaxcallback']) {
            $context['isajax'] = true;
        }

        $callbackfunc = '';
        if (isset($context['callbackfuncname']) && $context['callbackfuncname']) {
            $callbackfunc = $context['callbackfuncname'];
        }

        $pdata['tcount'] = $total;
        $pdata['tpage'] = (empty($pageSize) || $pageSize < 0) ? 1 : ceil($total / $pageSize);
        if ($pdata['tpage'] <= 1) {
            return '';
        }
        $cindex = $pageIndex;
        $cindex = min($cindex, $pdata['tpage']);
        $cindex = max($cindex, 1);
        $pdata['cindex'] = $cindex;
        $pdata['findex'] = 1;
        $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
        $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
        $pdata['lindex'] = $pdata['tpage'];

        if ($context['isajax'] === true) {
            if (empty($url)) {
                $url = \YunShop::app()->script_name . '?' . http_build_query($_REQUEST);
            }
            $pdata['faa'] = 'href="javascript:;" page="' . $pdata['findex'] . '" '. ($callbackfunc ? 'onclick="'.$callbackfunc.'(\'' . $url . '\', \'' . $pdata['findex'] . '\', this);return false;"' : '');
            $pdata['paa'] = 'href="javascript:;" page="' . $pdata['pindex'] . '" '. ($callbackfunc ? 'onclick="'.$callbackfunc.'(\'' . $url . '\', \'' . $pdata['pindex'] . '\', this);return false;"' : '');
            $pdata['naa'] = 'href="javascript:;" page="' . $pdata['nindex'] . '" '. ($callbackfunc ? 'onclick="'.$callbackfunc.'(\'' . $url . '\', \'' . $pdata['nindex'] . '\', this);return false;"' : '');
            $pdata['laa'] = 'href="javascript:;" page="' . $pdata['lindex'] . '" '. ($callbackfunc ? 'onclick="'.$callbackfunc.'(\'' . $url . '\', \'' . $pdata['lindex'] . '\', this);return false;"' : '');
        } else {
            if ($url) {
                $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
                $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
                $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
                $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
            } else {
                $_REQUEST['page'] = $pdata['findex'];
                $pdata['faa'] = 'href="' . \YunShop::app()->script_name . '?' . http_build_query($_REQUEST) . '"';
                $_REQUEST['page'] = $pdata['pindex'];
                $pdata['paa'] = 'href="' . \YunShop::app()->script_name . '?' . http_build_query($_REQUEST) . '"';
                $_REQUEST['page'] = $pdata['nindex'];
                $pdata['naa'] = 'href="' . \YunShop::app()->script_name . '?' . http_build_query($_REQUEST) . '"';
                $_REQUEST['page'] = $pdata['lindex'];
                $pdata['laa'] = 'href="' . \YunShop::app()->script_name . '?' . http_build_query($_REQUEST) . '"';

            }
        }

        $html = '<div><ul class="pagination pagination-centered">';
        if ($pdata['cindex'] > 1) {
            $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
            $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
        }
        //页码算法：前5后4，不足10位补齐
        if (!$context['before'] && $context['before'] != 0) {
            $context['before'] = 5;
        }
        if (!$context['after'] && $context['after'] != 0) {
            $context['after'] = 4;
        }

        if ($context['after'] != 0 && $context['before'] != 0) {
            $range = array();
            $range['start'] = max(1, $pdata['cindex'] - $context['before']);
            $range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
            if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
                $range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
                $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
            }
            for ($i = $range['start']; $i <= $range['end']; $i++) {
                if (true === $context['isajax']) {
                    $aa = 'href="javascript:;" page="' . $i . '" '. ($callbackfunc ? 'onclick="'.$callbackfunc.'(\'' . $url . '\', \'' . $i . '\', this);return false;"' : '');
                } else {
                    if ($url) {
                        $aa = 'href="?' . str_replace('*', $i, $url) . '"';
                    } else {
                        $_REQUEST['page'] = $i;
                        $aa = 'href="?' . http_build_query($_REQUEST) . '"';
                    }
                }
                $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
            }
        }

        if ($pdata['cindex'] < $pdata['tpage']) {
            $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
            $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
        }
        $html .= "<li><input type='text' id='jump' style='width: 25px; height: 25px;'></li>";
        $html .= "<li><a onclick =getkey(this,{$pdata['tpage']})  class=\"pager-nav\" style='float: right;'  >跳转</a></li>";
        $html .= '</ul></div>';
        return $html;
    }
}