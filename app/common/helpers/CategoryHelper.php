<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/1/25
 * Time: 下午1:35
 */

namespace app\common\helpers;


class CategoryHelper
{
    public static function tplLinkCategoryShow()
    {
        return Cache::remember('tpl:link_category', 7200, function () {
            $html = '';
            $first_category = \app\backend\modules\goods\models\Category::getCategoryFirstLevel();
            $second_category = \app\backend\modules\goods\models\Category::getCategorySecondLevel();
            $third_category = \app\backend\modules\goods\models\Category::getCategoryThirdLevel();

            $html .= <<<EOF
<div class="mylink-con">
EOF;

            if (!is_null($first_category)) {
                foreach ($first_category as $goodcate_parent) {
                    $href = yzAppFullUrl('catelist/' . $goodcate_parent['id']);

                    $html .= <<<EOF
                          <div class="mylink-line">
                             {$goodcate_parent['name']}
                             <div class="mylink-sub">
                                <a href="javascript:;" id="category-{$goodcate_parent['id']}" class="mylink-nav"
                                  ng-click="chooseLink(1, 'category-{$goodcate_parent['id']}')"
                                  nhref="{$href}">选择</a>
                             </div>
                          </div>
EOF;

                    $sub_level = null;
                    $parent_id = $goodcate_parent['id'];

                    if (!is_null($second_category)) {
                        $sub_level = collect($second_category)->filter(function ($val, $key) use ($parent_id) {
                            if ($val['parent_id'] == $parent_id) {
                                return $val;
                            }
                        });
                    }

                    if (!is_null($sub_level)) {
                        foreach ($sub_level as $goodcate_chlid) {
                            $href = yzAppFullUrl('catelist/' . $goodcate_chlid['id']);
                            $html .= <<<EOF
                           <div class="mylink-line">
                              <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                   {$goodcate_chlid['name']}
                              <div class="mylink-sub">
                                  <a href="javascript:;" id="category-{$goodcate_chlid['id']}" class="mylink-nav"
                                       ng-click="chooseLink(1, 'category-{$goodcate_chlid['id']}')"
                                       nhref="{$href}">选择</a>
                              </div>
                           </div>
EOF;
                        }

                        $third_level = null;
                        $secod_parent_id = $goodcate_chlid['id'];
                        if (!is_null($third_category)) {
                            $third_level = collect($third_category)->filter(function ($val, $key) use ($secod_parent_id) {
                                if ($val['parent_id'] == $secod_parent_id) {
                                    return $val;
                                }
                            });
                        }

                        if (!is_null($third_level)) {
                            foreach ($third_level as $goodcate_third) {
                                if ($goodcate_third['parent_id'] == $goodcate_chlid['id']) {
                                    $href = yzAppFullUrl('catelist/' . $goodcate_third['id']);
                                    $html .= <<<EOF
                                   <div class="mylink-line">
                                      <span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                          {$goodcate_third['name']}
                                      <div class="mylink-sub">
                                          <a href="javascript:;" id="category-{$goodcate_third['id']}" class="mylink-nav"
                                                ng-click="chooseLink(1, 'category-{$goodcate_third['id']}')"
                                                nhref="{$href}">选择</a>
                                      </div>
                                   </div>
EOF;

                                }
                            }
                        }
                    }
                }
            }

            $html .= <<<EOF
</div>
EOF;

            return $html;
        });
    }

    public static function tplGoodsCategoryShow()
    {
        return Cache::remember('tpl:goods_category', 7200, function () {
            $html = '';
            $first_category = \app\backend\modules\goods\models\Category::getCategoryFirstLevel();
            $second_category = \app\backend\modules\goods\models\Category::getCategorySecondLevel();
            $third_category = \app\backend\modules\goods\models\Category::getCategoryThirdLevel();

            $html .= <<<EOF
<div class="mylink-con">
EOF;

            if (!is_null($first_category)) {
                foreach ($first_category as $goodcate_parent) {
                    $html .= <<<EOF
                          <div class="mylink-line">
                              {$goodcate_parent['name']}
                              <div class="mylink-sub">
                                  <a href="javascript:;" id="" class="" ng-click="selectCategoryGoods(focus,'{$goodcate_parent['id']}')">选择</a>
                              </div>
                          </div>
EOF;

                    $sub_level = null;
                    $parent_id = $goodcate_parent['id'];

                    if (!is_null($second_category)) {
                        $sub_level = collect($second_category)->filter(function ($val, $key) use ($parent_id) {
                            if ($val['parent_id'] == $parent_id) {
                                return $val;
                            }
                        });
                    }

                    if (!is_null($sub_level)) {
                        foreach ($sub_level as $goodcate_chlid) {
                            $html .= <<<EOF
                           <div class="mylink-line">
                                  <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                  {$goodcate_chlid['name']}
                                  <div class="mylink-sub">
                                     <a href="javascript:;" class="mylink-nav" ng-click="selectCategoryGoods(focus,'{$goodcate_chlid['id']}')">选择</a>
                                  </div>
                           </div>
EOF;
                        }

                        $third_level = null;
                        $secod_parent_id = $goodcate_chlid['id'];
                        if (!is_null($third_category)) {
                            $third_level = collect($third_category)->filter(function ($val, $key) use ($secod_parent_id) {
                                if ($val['parent_id'] == $secod_parent_id) {
                                    return $val;
                                }
                            });
                        }

                        if (!is_null($third_level)) {
                            foreach ($third_level as $goodcate_third) {
                                if ($goodcate_third['parent_id'] == $goodcate_chlid['id']) {
                                    $html .= <<<EOF
                                   <div class="mylink-line">
                                        <span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                        {$goodcate_third['name']}
                                         <div class="mylink-sub">
                                             <a href="javascript:;" class="mylink-nav"  ng-click="selectCategoryGoods(focus,'{$goodcate_third['id']}')">选择</a>
                                         </div>
                                   </div>
EOF;

                                }
                            }
                        }
                    }
                }
            }

            $html .= <<<EOF
</div>
EOF;

            return $html;
        });
    }
}