<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 上午11:13
 */

namespace app\common\helpers;


class ImageHelper
{
    /**
     * 【表单控件】: 图片上传与选择控件
     * @param string $name 表单input名称
     * @param string $value 表单input值
     * @param string $default 默认显示的缩略图
     * @param array $options 图片上传配置信息
     * <pre>
     * 		$options['width'] = '';
     * 		$options['height'] = '';
     * 		$options['global'] = '';// 是否显示 global 目录（公共目录）
     * 		$options['extras'] = array(
     * 			&nbsp;'image'=> 缩略图img标签的自定义属性及属性值 ,
     * 			&nbsp;'text'=> input 标签的自定义属性及属性值
     * 		)
     * </pre>
     * @return string
     */
    public static function tplFormFieldImage($name, $value = '', $default = '', $options = array())
    {
        if (empty($default)) {
            $default = static_url('resource/images/nopic.jpg');
        }
        $val = $default;
        if (!empty($value)) {
            $val = tomedia($value);
        }
        if (!empty($options['global'])) {
            $options['global'] = true;
        } else {
            $options['global'] = false;
        }
        if (empty($options['class_extra'])) {
            $options['class_extra'] = '';
        }
        if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
            if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
                exit('图片上传目录错误,只能指定最多两级目录,如: "yz_store","yz_store/d1"');
            }
        }
        $options['direct'] = true;
        $options['multiple'] = false;
        if (isset($options['thumb'])) {
            $options['thumb'] = !empty($options['thumb']);
        }

        $param = uploadParam();
        $options['fileSizeLimit'] = $param['fileSizeLimitImage'];

        $s = '';
        if (!defined('TPL_INIT_IMAGE')) {

            $s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
			    require.config({
                    paths:{
                        "'.$param['util'].'":"'.$param['util_url'].'"
                    }
                });
				require(["'.$param['util'].'"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().next().children();
					options = '.str_replace('"', '\'', json_encode($options)).';
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.attachment);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", \'{{static_url("resource/images/nopic.jpg")}}\');
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
            define('TPL_INIT_IMAGE', true);
        }

        $s .= '
		<div class="input-group ' . $options['class_extra'] . '">
			<input type="text" name="' . $name . '" value="' . $value . '"' . (isset($options['extras']['text']) ? $options['extras']['text'] : '') . ' class="form-control" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>
			</span>
		</div>
		<div class="input-group ' . $options['class_extra'] . '" style="margin-top:.5em;">
			<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" ' . (isset($options['extras']['image']) ? $options['extras']['image'] : '') . ' width="150" />
			<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
		</div>';
        return $s;
    }

    /**
     * 批量上传图片
     * @param string $name 表单input名称
     * @param array $value 附件路径信息
     * @param array $options  自定义图片上传路径
     * @return string
     */
    public static function tplFormFieldMultiImage($name, $value = array(), $options = array())
    {
        $options['multiple'] = true;
        $options['direct'] = false;
        $param = uploadParam();
        $options['fileSizeLimit'] = $param['fileSizeLimitImage'];

        if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
            if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
                exit('图片上传目录错误,只能指定最多两级目录,如: "yz_store","yz_store/d1"');
            }
        }
        $s = '';
        if (!defined('TPL_INIT_MULTI_IMAGE')) {

            $s = '
		<script type="text/javascript">
			function uploadMultiImage(elm) {
                var name = $(elm).next().val();
                require.config({
                    paths:{
                        "'.$param['util'].'":"'.$param['util_url'].'"
                    }
                });
				require(["'.$param['util'].'"], function(util){
					util.image("", function(urls){
						$.each(urls, function(idx, url){
                            $(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\''.static_url('./resource/images/nopic.jpg').'\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.attachment+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
                        });
					}, ' . json_encode($options) . ');
				});
			}
			function deleteMultiImage(elm){
                require(["jquery"], function($){
                    $(elm).parent().remove();
                });
	        }
		</script>';
            define('TPL_INIT_MULTI_IMAGE', true);
        }



        $s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $row) {
                $s .= '
<div class="multi-item">
	<img src="' . tomedia($row) . '" onerror="this.src=\''.static_url('resource/images/nopic.jpg').'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="' . $name . '[]" value="' . $row . '" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
            }
        }
        $s .= '</div>';

        return $s;
    }

    /**
     * 【表单控件】: 视频上传与选择控件
     * @param string $name 表单input名称
     * @param string $value 表单input值
     * @param string $default 默认显示的缩略图
     * @param array $options
     * @return string
     */
    public function tplFormFieldVideo($name, $value = '', $options = array())
    {
        if(!is_array($options)){
            $options = array();
        }
        if (!is_array($options)) {
            $options = array();
        }
        $options['direct'] = true;
        $options['multi'] = false;
        $options['type'] = 'video';
        $param = uploadParam();
        $options['fileSizeLimit'] = $param['fileSizeLimitAudio'];

        $s = '';
        if (!defined('TPL_INIT_VIDEO')) {
            $s = '
<script type="text/javascript">
	function showVideoDialog(elm, options) {
	    require.config({
            paths:{
                "'.$param['util'].'":"'.$param['util_url'].'"
            }
        });
		require(["'.$param['util'].'"], function(util){
			var btn = $(elm);
			var ipt = btn.parent().prev();
			var val = ipt.val();
			util.audio(val, function(url){
				if(url && url.attachment && url.url){
					btn.prev().show();
					ipt.val(url.attachment);
					ipt.attr("filename",url.filename);
					ipt.attr("url",url.url);
				}
				if(url && url.media_id){
					ipt.val(url.media_id);
				}
			}, '.json_encode($options).');
		});
	}

</script>';
            echo $s;
            define('TPL_INIT_VIDEO', true);
        }

        $s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control" autocomplete="off" '.($options['extras']['text'] ? $options['extras']['text'] : '').'>
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" onclick="showVideoDialog(this,'.str_replace('"','\'', json_encode($options)).');">选择媒体文件</button>
		</span>
	</div>';
        return $s;
    }

    /**
     * 前端组件上传图片
     *
     * @param $fileinput
     * @return null|string
     */
    public static function upload($fileinput, $dir = 'upload')
    {
        if (\Request::isMethod('post')) {
            $file = \Request::file($fileinput);

            if ($file->isValid()) {
                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $realPath = $file->getRealPath();   //临时文件的绝对路径

                $bool = \Storage::disk($dir)->put($originalName, file_get_contents($realPath));

                return $bool ? $originalName : '';
            }
        }
    }

    public static function fix_wechatAvatar($avatar)
    {
        return preg_replace('/132132/', '132', $avatar);
    }

    public static function iosWechatAvatar($avatar)
    {
        $osType = Client::osType();
        if ($osType == Client::OS_TYPE_IOS) {
            return preg_replace("/^http:/i","https:", $avatar);
        }
        return $avatar;
    }

    public static function getImageUrl($file)
    {
        if (config('app.framework') == 'platform') {
            return request()->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . substr($file, strpos($file, 'storage'));
        } else {
            return request()->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . substr($file, strpos($file, 'addons'));
        }
    }
}