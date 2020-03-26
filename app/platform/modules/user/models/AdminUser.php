<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午4:51
 */

namespace app\platform\modules\user\models;


use app\common\events\UserActionEvent;
use app\common\services\Utils;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Authenticatable
{
    use Notifiable;

    public $primaryKey = 'uid';
    protected $table = 'yz_admin_users';
    public $timestamps = true;
    protected $guarded = [''];
    protected $dateFormat = 'U';
    public static $base = '';

    const ORIGINAL          = '原密码错误';
    const NEW_AND_ORIGINAL  = '新密码与原密码一致';
    const STORAGE           = '存储相关信息表失败';
    const PARAM             = '参数错误';
    const NO_DATA           = '未获取到数据';
    const FAIL              = '失败';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    //用户角色
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'yz_admin_role_user', 'user_id', 'role_id');
    }

    // 判断用户是否具有某个角色
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role); // ?
        }

        return !!$role->intersect($this->roles)->count();
    }

    // 判断用户是否具有某权限
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
            if (!$permission) {
                return false;
            }
        }

        return $this->hasRole($permission->roles);
    }

    // 给用户分配角色
    public function assignRole($role)
    {
        return $this->roles()->save($role);
    }

    //角色整体添加与修改
    public function giveRoleTo(array $RoleId)
    {
        $this->roles()->detach();
        $roles = Role::whereIn('id', $RoleId)->get();
        foreach ($roles as $v) {
            $this->assignRole($v);
        }
        return true;
    }

    /**
     * 保存数据
     *
     * @param $data
     * @param string $user_model
     * @return mixed
     */
    public static function saveData($data, $user_model = [])
    {
        $verify_res = self::verifyData($data, $user_model);
        if ($verify_res['sign'] == '0') {
            return $verify_res;
        }
        $verify_res['re_password'] ? $verify_res['password'] = bcrypt($verify_res['password']) : null;
        unset($verify_res['re_password']);
        \Log::info("----------管理员用户----------", "管理员:(uid:{$verify_res['uid']})-----用户信息-----".$verify_res.'-----参数-----'.json_encode($data));
        if ($verify_res->save()) {
            if (request()->path() != "admin/user/modify_user" && request()->path() != "admin/user/change") {
                if (self::saveProfile($data, $verify_res)) {
                    return self::returnData(0, self::STORAGE);
                }
            }
            return self::returnData(1);
        } else {
            return self::returnData(0, self::FAIL);
        }
    }

    /**
     * 整合数据
     *
     * @param $data
     * @param array $user_model
     * @return AdminUser|array
     */
    public static function verifyData($data, $user_model)
    {
        $data['username'] ? $data['username'] = trim($data['username']) : null;
        $data['password'] ? $data['password'] = trim($data['password']) : null;
        $data['application_number'] == 0 && !$user_model['application_number'] ? $data['application_number'] = '' : $user_model['application_number'];
        $data['endtime'] == 0 && !$user_model['application_number'] ? $data['endtime'] = '' : $user_model['endtime'];

        if (request()->path() == "admin/user/change" || (request()->path() == "admin/user/modify_user" && $data['password'])) {
            $data['old_password'] = trim($data['old_password']);
            if (request()->path() != "admin/user/change" && (!Hash::check($data['old_password'], $user_model['password']))) {
                return self::returnData(0, self::ORIGINAL);
            } elseif (Hash::check($data['password'], $user_model['password'])) {
                return self::returnData(0, self::NEW_AND_ORIGINAL);
            }
            unset($data['old_password']);
        }

        $data['lastvisit'] =time();
        $data['lastip'] = Utils::getClientIp();
        unset($data['avatar']);

        !$user_model ? $user_model = new self() : null;
        !$user_model['joinip'] ? $user_model['joinip'] = Utils::getClientIp() : null;
        !$user_model['salt'] ? $user_model['salt'] = Utils::getClientIp() : null;
        $user_model->fill($data);
        unset($user_model['mobile']);

        return $user_model;
    }

    /**
     * 读取所有数据
     * @param $parames
     * @return mixed
     */
    public static function getList($parames)
    {
        $users = self::searchUsers($parames)->where('type', 1)->orderBy('uid', 'desc')->paginate();
        foreach ($users as $item) {
            $item['create_at'] = $item['created_at']->format('Y年m月d日');
            $item['status'] == 2 ? $item['state'] = '有效' : null;
            $item['status'] == 3 ? $item['state'] = '已禁用' : null;
            if ($item['endtime'] == 0) {
                $item['endtime'] = '永久有效';
            }else {
                if (time() > $item['endtime']) {
                    $item['state'] = '已过期';
                }
                $item['endtime'] = date('Y年m月d日', $item['endtime']);
            }
        }

        return $users;
    }

    /**
     * 读取单条数据
     *
     * @param $uid
     * @return mixed
     */
    public static function getData($uid)
    {
        return self::find($uid);
    }

    /**
     * 检索用户信息
     *
     * @param $parame
     * @return mixed
     */
    public static function scopeSearchUsers($result, $parame)
    {
        $result = $result->select(['uid', 'username', 'status', 'type', 'remark', 'application_number', 'endtime', 'created_at', 'updated_at']);

        if ($parame['search']['status']) {
            if ($parame['search']['status'] == 4) {
                $time = [['endtime', '<', time()], ['endtime', '>', '0']];
                $result = $result->where($time);
            } else {
                $result = $result->where('status', $parame['search']['status'])->where(function ($query) {
                    $query->where('endtime', '==', '0')
                        ->orWhere('endtime', '>', time());
                });
            }
        }

        if ($parame['search']['searchtime']) {
            $range = [$parame['search']['times']['start'], $parame['search']['times']['end']];
            if ($parame['search']['searchtime'] == 1 && $parame['search']['times']['start']) {
                $result = $result->whereBetween('created_at', $range);
            } elseif ($parame['search']['searchtime'] == 2 && $parame['search']['times']['start']) {
                $result = $result->whereBetween('endtime', $range);
            }
        }

        if ($parame['search']['keyword']) {
            $result = $result->where(function ($query) use ($parame) {
                $query->where('username', 'like', '%' . $parame['search']['keyword'] . '%')
                    ->orWhereHas('hasOneProfile', function ($query) use ($parame)  {
                        $query->where('mobile', 'like', '%' . $parame['search']['keyword'] . '%');
                    });
            });
        }

        return $result;
    }

    /**
     * 获取随机字符串
     *
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    protected static function randNum($length, $numeric = FALSE)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     * 保存用户信息表
     *
     * @param $data
     * @param $user
     * @return int
     */
    public static function saveProfile($data, $user)
    {
        $data = [
            'mobile' => $data['mobile'],
            'avatar' => $data['avatar']
        ];

        $type = 1;
        $content = '添加用户';
        $profile_model = new YzUserProfile;

        if (request()->path() == "admin/user/create") {
            $data['uid'] = $user->uid;
        } elseif (request()->path() == "admin/user/edit" || request()->path() == "admin/user/modify_mobile") {
            $type = 3;
            $content = '编辑用户';
            $profile_model = YzUserProfile::where('uid', $user->uid)->first();
        }

        $profile_model->fill($data);
        if (!$profile_model->save()) {
            return 1;
        }

        event(new UserActionEvent(self::class, $user['uid'], $type, $content . $user['username']));
    }

    /**
     * 获得多个平台的使用者.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyAppUser()
    {
        return $this->hasMany(\app\platform\modules\application\models\AppUser::class, 'uid', 'uid');
    }

    /**
     * 获取与用户表相关的用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneProfile()
    {
        return $this->hasOne(\app\platform\modules\user\models\YzUserProfile::class, 'uid', 'uid');
    }

    /**
     * 获得单个平台的使用者.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneAppUser()
    {
        return $this->hasOne(\app\platform\modules\application\models\AppUser::class, 'uid', 'uid');
    }

    public static function returnData($sign = '', $message = '')
    {
        return [
            'sign' => $sign,
            'message' => $message
        ];
    }
}