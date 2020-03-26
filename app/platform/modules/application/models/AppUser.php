<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use app\platform\modules\user\models\AdminUser;
use Illuminate\Database\Eloquent\Model;


class AppUser extends BaseModel
{
	protected $table = 'yz_app_user';
	protected $search_fields = [''];
  	protected $guarded = [''];
    protected $appends = ['role_name'];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    public function scopeSearch($query, $keyword)
    {
        if ($keyword['name']) {
            
            $query = $query->whereHas('hasOneUser', function($query) use($keyword) {
            
                $query = $query->where('username', 'like', '%'.$keyword['name'].'%');
           
            });
        }
        
        if ($keyword['uid']) {
            $query = $query->whereHas('hasOneUser', function($query) use ($keyword) {
                
                $query = $query->where('uid', $keyword['uid']);
            });
        }

        return $query;
    }

    public function atributeNames() 
    {
        return [
            'uniacid' => '平台id',
            'uid' => '用户id',
            'role' => '角色'
        ];
    }
    
    public function rules()
    {
    	return [
            'uniacid' => 'required | integer',
            'uid' => 'required | integer',
            'role' => 'required | string | max:20',
        ];
    }

    public function hasOneApp()
    {
        return $this->hasOne(\app\platform\modules\application\models\UniacidApp::class, 'id', 'uniacid');
    }

    public function hasOneUser()
    {
        return $this->hasOne(\app\platform\modules\user\models\AdminUser::class, 'uid', 'uid');
    }
    public function getRoleNameAttribute()
    {
        if ($this->role == 'manager') {
            return $this->role_name = '管理员';
        } elseif ($this->role == 'clerk') {
            return $this->role_name = '店员';
        } elseif ($this->role == 'operator') {
            return  $this->role_name = '操作员';
        } elseif ($this->role == 'creator') {
            return  $this->role_name = '创始人';
        }
    }

    public static function getAccount($uid, $uniacid = null)
    {
        if (is_null($uniacid)) {
            return self::where('uid', $uid)->first();
        }

        return self::where(['uid' => $uid, 'uniacid' => $uniacid])->first();
    }
}