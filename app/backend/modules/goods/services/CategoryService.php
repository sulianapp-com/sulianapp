<?php

namespace app\backend\modules\goods\services;
use app\backend\modules\goods\models\Category;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午2:53
 */

class CategoryService
{

    public static function getCategoryMenu($params)
    {
        $catetorys = Category::getAllCategoryGroup();

        //获取分类2/3级联动
        if ($params['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0]) ? $params['ids'][0] : 0,
                isset($params['ids'][1]) ? $params['ids'][1] : 0,
                isset($params['ids'][2]) ? $params['ids'][2] : 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0]) ? $params['ids'][0] : 0,
                isset($params['ids'][1]) ? $params['ids'][1] : 0,
                isset($params['ids'][2]) ? $params['ids'][2] : 0
            );
        }

        return $catetory_menus;
    }

    public static function getCategoryMultiMenu($params)
    {
        $catetorys = Category::getAllCategoryGroup();

        //获取分类2/3级联动
        if ($params['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3_multi(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0]) ? $params['ids'][0] : 0,
                isset($params['ids'][1]) ? $params['ids'][1] : 0,
                isset($params['ids'][2]) ? $params['ids'][2] : 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2_multi(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0]) ? $params['ids'][0] : 0,
                isset($params['ids'][1]) ? $params['ids'][1] : 0,
                isset($params['ids'][2]) ? $params['ids'][2] : 0
            );
        }

        return $catetory_menus;
    }

    public static function getCategoryMultiMenuSearch($params)
    {
        $catetorys = Category::getAllCategoryGroup();

        //获取分类2/3级联动
        if ($params['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3_multi_search(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0][0]) ? $params['ids'][0][0] : 0,
                isset($params['ids'][1][0]) ? $params['ids'][1][0] : 0,
                isset($params['ids'][2][0]) ? $params['ids'][2][0] : 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2_multi_search(
                'category', $catetorys['parent'], $catetorys['children'],
                isset($params['ids'][0][0]) ? $params['ids'][0][0] : 0,
                isset($params['ids'][1][0]) ? $params['ids'][1][0] : 0,
                isset($params['ids'][2][0]) ? $params['ids'][2][0] : 0
            );
        }

        return $catetory_menus;
    }

    public static function tpl_form_field_category_level3($name, $parents, $children, $parentid, $childid, $thirdid)
    {
        $html = '
<script type="text/javascript">
	window._' . $name . ' = ' . json_encode($children) . ';
</script>';
if (!defined('TPL_INIT_CATEGORY_THIRD')) {
    $html .= '
<script type="text/javascript">
    function renderCategoryThird(obj, name){
        var index = obj.options[obj.selectedIndex].value;
        require([\'jquery\', \'util\'], function($, u){
            $selectChild = $(\'#\'+name+\'_child\');
            $selectThird = $(\'#\'+name+\'_third\');
            var html = \'<option value="0">请选择二级分类</option>\';
            var html1 = \'<option value="0">请选择三级分类</option>\';
            if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                $selectChild.html(html);
                $selectThird.html(html1);
                return false;
            }
            for(var i=0; i< window[\'_\'+name][index].length; i++){
                html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
            }
            $selectChild.html(html);
            $selectThird.html(html1);
        });
    }
    function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
    ';
    define('TPL_INIT_CATEGORY_THIRD', true);
}
        $html .= '<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategoryThird(this,\'' . $name . '\')">
			<option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
			<option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]" onchange="renderCategoryThird1(this,\'' . $name . '\')">
			<option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
		</select>
	</div>
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_third" name="' . $name . '[thirdid]">
			<option value="0">请选择三级分类</option>';
        if (!empty($childid) && !empty($children[$childid])) {
            foreach ($children[$childid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $thirdid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '</select>
	</div>
</div>';
        return $html;
    }

    public static function tpl_form_field_category_level2($name, $parents, $children, $parentid, $childid)
    {
        $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
        if (!defined('TPL_INIT_CATEGORY')) {
            $html .= '
        <script type="text/javascript">
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
            define('TPL_INIT_CATEGORY', true);
        }

        $html .=
            '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]">
                    <option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
                </select>
            </div>
        </div>
    ';
        return $html;
    }

    public static function tpl_form_field_category_level3_multi($name, $parents, $children, $parentid, $childid, $thirdid)
    {
        $html = '
<script type="text/javascript">
	window._' . $name . ' = ' . json_encode($children) . ';
</script>';
        if (!defined('TPL_INIT_CATEGORY_THIRD')) {
            $html .= '
<script type="text/javascript">
    function renderCategoryThird(obj, name){
        var index = obj.options[obj.selectedIndex].value;
        require([\'jquery\', \'util\'], function($, u){
            $selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_child\');
            $selectThird = $(obj).parent().siblings().find(\'#\'+name+\'_third\');
            var html = \'<option value="0">请选择二级分类</option>\';
            var html1 = \'<option value="0">请选择三级分类</option>\';
            if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                $selectChild.html(html);
                $selectThird.html(html1);
                return false;
            }
            for(var i=0; i< window[\'_\'+name][index].length; i++){
                html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
            }
            $selectChild.html(html);
            $selectThird.html(html1);
        });
    }
    function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
    ';
            define('TPL_INIT_CATEGORY_THIRD', true);
        }
        $html .= '<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid][]" onchange="renderCategoryThird(this,\'' . $name . '\')">
			<option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
			<option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid][]" onchange="renderCategoryThird1(this,\'' . $name . '\')">
			<option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
		</select>
	</div>
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_third" name="' . $name . '[thirdid][]">
			<option value="0">请选择三级分类</option>';
        if (!empty($childid) && !empty($children[$childid])) {
            foreach ($children[$childid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $thirdid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '</select></div>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3"><input type="button" value="删除" class="btn btn-danger delCategory"></div>
</div>';
        return $html;
    }

    public static function tpl_form_field_category_level2_multi($name, $parents, $children, $parentid, $childid)
    {
        $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
        if (!defined('TPL_INIT_CATEGORY')) {
            $html .= '
        <script type="text/javascript">
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
            define('TPL_INIT_CATEGORY', true);
        }

        $html .=
            '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid][]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid][]">
                    <option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
                </select></div>';
        $html .= '<div class="col-sm-4 col-md-4 col-lg-4"><input type="button" value="删除" class="btn btn-danger delCategory"></div>
        </div>
    ';
        return $html;
    }

    public static function tpl_form_field_category_level3_multi_search($name, $parents, $children, $parentid, $childid, $thirdid)
    {
        $html = '
<script type="text/javascript">
	window._' . $name . ' = ' . json_encode($children) . ';
</script>';
        if (!defined('TPL_INIT_CATEGORY_THIRD')) {
            $html .= '
<script type="text/javascript">
    function renderCategoryThird(obj, name){
        var index = obj.options[obj.selectedIndex].value;
        require([\'jquery\', \'util\'], function($, u){
            $selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_child\');
            $selectThird = $(obj).parent().siblings().find(\'#\'+name+\'_third\');
            var html = \'<option value="0">请选择二级分类</option>\';
            var html1 = \'<option value="0">请选择三级分类</option>\';
            if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                $selectChild.html(html);
                $selectThird.html(html1);
                return false;
            }
            for(var i=0; i< window[\'_\'+name][index].length; i++){
                html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
            }
            $selectChild.html(html);
            $selectThird.html(html1);
        });
    }
    function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
    ';
            define('TPL_INIT_CATEGORY_THIRD', true);
        }
        $html .= '<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
		<select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid][]" onchange="renderCategoryThird(this,\'' . $name . '\')">
			<option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
			<option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
		<select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid][]" onchange="renderCategoryThird1(this,\'' . $name . '\')">
			<option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
		</select>
	</div>
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
		<select class="form-control tpl-category-child" id="' . $name . '_third" name="' . $name . '[thirdid][]">
			<option value="0">请选择三级分类</option>';
        if (!empty($childid) && !empty($children[$childid])) {
            foreach ($children[$childid] as $row) {
                $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $thirdid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '</select></div></div>';
        return $html;
    }

    public static function tpl_form_field_category_level2_multi_search($name, $parents, $children, $parentid, $childid)
    {
        $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
        if (!defined('TPL_INIT_CATEGORY')) {
            $html .= '
        <script type="text/javascript">
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
            define('TPL_INIT_CATEGORY', true);
        }

        $html .=
            '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid][]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid][]">
                    <option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
                </select></div></div>
    ';
        return $html;
    }
}