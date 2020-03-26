<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午12:36
 */

namespace app\platform\modules\user\controllers;


use app\common\events\UserActionEvent;
use app\platform\controllers\BaseController;
use app\platform\modules\user\models\Permission;
use app\platform\modules\user\requests\PermissionCreateRequest;
use app\platform\modules\user\requests\PermissionUpdateRequest;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    protected $fields = [
        'name'        => '',
        'label'       => '',
        'description' => '',
        'parent_id'   => 0,
        'icon'        => '',
    ];


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $parentId = 0)
    {
        $parentId = (int)$parentId;
        $datas['parentId'] = $parentId;

        $datas['data'] = Permission::where('parent_id', $parentId)->get();

        return view('admin.permission.index', $datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($parentId)
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['parent_id'] = $parentId;

        return view('admin.permission.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PremissionCreateRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionCreateRequest $request)
    {
        $permission = new Permission();
        foreach (array_keys($this->fields) as $field) {
            $permission->$field = $request->get($field, $this->fields[$field]);
        }
        $permission->save();

        event(new UserActionEvent(Permission::class, $permission->id, 1,
            '添加了权限:' . $permission->name . '(' . $permission->label . ')'));

        return $this->successJson('添加成功', []);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find((int)$id);
        if (!$permission) {
            return redirect('/admin/permission')->withErrors("找不到该权限!");
        }
        $data = ['id' => (int)$id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $permission->$field);
        }

        //dd($data);
        return view('admin.permission.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionUpdateRequest|Request $request
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = Permission::find((int)$id);
        foreach (array_keys($this->fields) as $field) {
            $permission->$field = $request->get($field, $this->fields[$field]);
        }
        $permission->save();

        event(new UserActionEvent(Permission::class, $permission->id, 3,
            '修改了权限:' . $permission->name . '(' . $permission->label . ')'));

        return $this->successJson('添加成功', []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $child = Permission::where('parent_id', $id)->first();

        if ($child) {
            return redirect()->back()
                ->withErrors("请先将该权限的子权限删除后再做删除操作!");
        }
        $tag = Permission::find((int)$id);
        foreach ($tag->roles as $v) {
            $tag->roles()->detach($v->id);
        }
        if ($tag) {
            $tag->delete();
        } else {
            return redirect()->back()
                ->withErrors("删除失败");
        }

        event(new UserActionEvent(Permission::class, $tag->id, 2, '删除了权限:' . $tag->name . '(' . $tag->label . ')'));

        return $this->successJson('成功', []);
    }
}