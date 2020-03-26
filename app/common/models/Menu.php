<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 09/03/2017
 * Time: 10:52
 */

namespace app\common\models;


use app\common\traits\TreeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Menu extends BaseModel
{
    use TreeTrait,SoftDeletes;

    public $table = 'yz_menu';

    //设置字段默认值
    public $attributes = [
        'parent_id'=>0,
        'name'=>'',
        'item'=>'',
        'url'=>'',
        'url_params'=>'',
        'permit'=>1,
        'menu'=>1,
        'icon'=>'',
        'sort'=>0,
        'status'=>1
    ];
    //不可填充
    public $guarded = [''];

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    public function getDateFormat() {
         return 'U';
     }

    /**
     * 父菜单与子菜单栏目1:n关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 子菜单与父菜单1:1关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 获取待处理的原始节点数据
     *
     * 必须实现
     *
     * return \Illuminate\Support\Collection
     */
    public function getTreeAllNodes()
    {
        return self::orderBy('sort', 'asc')->get();
    }

    /**
     * 获取菜单栏目
     *
     * @param $parent_id
     * @param int $child_switch
     * @return mixed
     */
    public static function getMenuAllInfo($parent_id = 0, $child_switch = 1)
    {
        $result = self::where('parent_id', $parent_id)
                   ->where('status', 1)
                   ->orderBy('sort', 'asc');

        if ($child_switch) {
            $result = $result->with(['childs'=>function ($query) {
                return $query->where('status', self::STATUS_ENABLED)->orderBy('sort', 'asc');
            }]);
        }

        return $result;
    }

    /**
     * 生成 config 菜单数据结构
     *
     * @param int $parentId
     * @return array
     */
    public static function getMenuList($parentId = 0, $parent = [])
    {
        $list = [];
            $menuList = static::select('id','name','url','url_params','permit','menu','icon','parent_id','sort','item')
                ->where(['parent_id' => $parentId,'status'=>self::STATUS_ENABLED])
                ->with('childs')
                ->orderby('sort')
                ->get();

            if($menuList){
                foreach ($menuList as $key=>$value){
                    $list[$value->item] = $value->toArray();
                    $list[$value->item]['parents'] = $parent;
                    array_forget($list[$value->item],'childs');
                    if($value->childs->count() > 0){
                        $list[$value->item]['child'] = self::getMenuList($value->id, array_merge($parent,(array) $value->item));
                    }
                }
            }

        return $list;
    }

    /**
     * 获取当前菜单父级item
     * @param $item
     * @param array $menuList
     * @return mixed
     */
    public static function getCurrentMenuParents($item, array $menuList)
    {
        static $current = [];
        //dump($menuList);
        foreach($menuList as $key=>$value){
            //dump($key);
            if($key == $item){
                $current = $value['parents'];
                //dd(11);
                break;
            }
            if(isset($value['child']) && $value['child']){
                $current = self::getCurrentMenuParents($item,$value['child']);
            }
        }

        //dd($menuList);
        //dd($current);

        //exit;
        return $current;
    }

    /**
     * 获取 item from route
     * @param $route
     * @param array $menuList
     * @return array|int|mixed|string
     */
    public static function getCurrentItemByRoute($route, array $menuList)
    {
        static $current = null;
        foreach($menuList as $key=>$value){
            if(isset($value['url']) && $value['url'] == $route){
                $current = $key;
                break;
            }
            if(isset($value['child']) && $value['child']){
                $current = self::getCurrentItemByRoute($route,$value['child']);
            }
        }

        return $current;
    }


    public static function getItemByRoute($route)
    {
        $data = static::select('item')->where(['url'=>$route])->first();
        return $data ? $data->item : '';
    }

    /**
     * 通过ID获取菜单栏目
     *
     * @param $id
     * @return mixed
     */
    public static function getMenuInfoById($id)
    {
        return self::where('id', $id)->with(['childs'])->first();
    }

    /**
     * 重写检测提示文字
     * @return array
     */
    public function validationMessages()
    {
        return array_merge(parent::validationMessages(),[
            "different" => " 不能选择自己为上级。"
        ]);
    }

    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'item'=> '标识',
            'name'=> '菜单',
            'url'=> 'URL',
            'url_params'=> 'URL参数',
            'icon'=> '图标',
            'sort'=> '排序',
            'permit'=> '权限控制',
            'menu'=> '菜单显示',
            'status'=> '状态',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        $rule =  [
            //具体unique可看文档 https://laravel.com/docs/5.4/validation#rule-unique
            'item' => ['required',Rule::unique($this->table)->ignore($this->id)],
            'name' => 'required|max:45',
            'url' => 'max:255',
            'url_params' => 'max:255',
            'icon' => 'max:45',
            'sort' => 'required|integer',
            'permit' => 'required|digits_between:0,1',
            'menu' => 'required|digits_between:0,1',
            'status' => 'required|digits_between:0,1'
        ];
        //修改时不能选择自己做为上级
        if((int)$this->getAttributeValue('id') > 0){
            $rule = array_merge(['parent_id' => 'different:id'], $rule);
        }

        return $rule;
    }
}