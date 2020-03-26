<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/18
 * Time: 下午6:48
 */

namespace app\common\helpers;


use Illuminate\Support\Facades\DB;

define('TIMESTAMP', time());

class YunSession implements \SessionHandlerInterface
{
    public static $uniacid;

    public static $openid;

    public static $expire;

    public static function start($uniacid, $openid, $expire = 7200) {
        self::$uniacid = $uniacid;
        self::$openid = $openid;
        self::$expire = $expire;

        $cache_setting = $GLOBALS['_W']['config']['setting'];
        if (extension_loaded('memcache') && !empty($cache_setting['memcache']['server']) && !empty($cache_setting['memcache']['session'])) {
            self::setHandler('memcache');
        } elseif (extension_loaded('redis') && !empty($cache_setting['redis']['server']) && !empty($cache_setting['redis']['session'])) {
            self::setHandler('redis');
        } else {
            self::setHandler('mysql');
        }
        register_shutdown_function('session_write_close');
        session_start();
    }

    public static function setHandler($type = 'mysql') {
        $classname = "app\common\helpers\YunSession{$type}";
        if (class_exists($classname)) {
            $sess = new $classname;
        }
        if (version_compare(PHP_VERSION, '5.5') >= 0) {
            session_set_save_handler($sess, true);
        } else {
            session_set_save_handler(
                array(&$sess, 'open'),
                array(&$sess, 'close'),
                array(&$sess, 'read'),
                array(&$sess, 'write'),
                array(&$sess, 'destroy'),
                array(&$sess, 'gc')
            );
        }
        return true;
    }

    public function open($save_path, $session_name) {
        return true;
    }

    public function close() {
        return true;
    }


    public function read($sessionid) {
        return '';
    }


    public function write($sessionid, $data) {
        return true;
    }


    public function destroy($sessionid) {
        return true;
    }


    public function gc($expire) {
        return true;
    }

}

class YunSessionMemcache extends YunSession {
    protected $session_name;

    protected function key($sessionid) {
        return $this->session_name . ':' . $sessionid;
    }

    public function open($save_path, $session_name) {
        $this->session_name = $session_name;

        if (cache_type() != 'memcache') {
            trigger_error('Memcache 扩展不可用或是服务未开启，请将 \$config[\'setting\'][\'memcache\'][\'session\'] 设置为0 ');
            return false;
        }
        return true;
    }

    public function read($sessionid) {
        $row = cache_read($this->key($sessionid));
        if ($row['expiretime'] < TIMESTAMP) {
            return '';
        }
        if(is_array($row) && !empty($row['data'])) {
            return $row['data'];
        }
        return '';
    }

    public function write($sessionid, $data) {
        if (empty($data) || (!empty($data) && empty($this->chk_member_id_session($data)))) {
            $read_data = $this->read($sessionid);

            if (!empty($member_data = $this->chk_member_id_session($read_data))) {
                $data .= $member_data;
            }
        }

        $row = array();
        $row['data'] = $data;
        $row['expiretime'] = TIMESTAMP + YunSession::$expire;

        return cache_write($this->key($sessionid), $row);
    }

    public function destroy($sessionid) {
        return cache_write($this->key($sessionid), '');
    }

    public function chk_member_id_session($read_data)
    {
        $member_data = '';

        if (!empty($read_data)) {
            preg_match_all('/yunzshop_([\w]+[^|]*|)/', $read_data, $name_matches);
            preg_match_all('/(a:[\w]+[^}]*})/', $read_data, $value_matches);

            if (!empty($name_matches)) {
                foreach ($name_matches[0] as $key => $val) {
                    if ($val == 'yunzshop_member_id') {
                        $member_data = $val . '|' . $value_matches[0][$key];
                    }
                }
            }
        }

        return $member_data;
    }
}

class YunSessionRedis extends YunSessionMemcache {
    public function __construct()
    {
    }

    public function open($save_path, $session_name) {
        $this->session_name = $session_name;

        if (cache_type() != 'redis') {
            trigger_error('Redis 扩展不可用或是服务未开启，请将 \$config[\'setting\'][\'redis\'][\'session\'] 设置为0 ');
            return false;
        }
        return true;
    }
}

class YunSessionMysql extends YunSession {
    public function open($save_path, $session_name) {
        return true;
    }

    public function read($sessionid) {
        $sql = 'SELECT * FROM ' . DB::getTablePrefix() . 'core_sessions WHERE `sid`=:sessid AND `expiretime`>:time';
        $params = array();
        $params[':sessid'] = $sessionid;
        $params[':time'] = TIMESTAMP;
        $row = DB::selectOne($sql, $params);


        if(is_array($row) && !empty($row['data'])) {
            return $row['data'];
        }

        return '';
    }


    public function write($sessionid, $data) {
        if (empty($data) || (!empty($data) && empty($this->chk_member_id_session($data)))) {
            $read_data = $this->read($sessionid);

            if (!empty($member_data = $this->chk_member_id_session($read_data))) {
                $data .= $member_data;
            }
        }

        $row = array();
        $row['sid'] = $sessionid;
        $row['uniacid'] = YunSession::$uniacid;
        $row['openid'] = YunSession::$openid;
        $row['data'] = $data;
        $row['expiretime'] = TIMESTAMP + YunSession::$expire;

        $sql = 'REPLACE INTO ' . DB::getTablePrefix() . "core_sessions (`sid`, `uniacid`, `openid`, `data`, `expiretime`) 
                   VALUES ('{$row['sid']}', {$row['uniacid']}, '{$row['openid']}', '{$row['data']}', {$row['expiretime']})";

        return DB::insert($sql) >= 1;
    }


    public function destroy($sessionid) {
        $row = array();
        $row[':sid'] = $sessionid;

        $sql = 'DELETE FROM ' . DB::getTablePrefix() . 'core_sessions WHERE `sid` = :sid';
        return DB::delete($sql, $row) == 1;
    }


    public function gc($expire) {
        $sql = 'DELETE FROM ' . DB::getTablePrefix() . 'core_sessions WHERE `expiretime`<:expire';

        return DB::delete($sql, [':expire' => TIMESTAMP]) == 1;
    }

    private function chk_member_id_session($read_data)
    {
        $member_data = '';

        if (!empty($read_data)) {
            preg_match_all('/yunzshop_([\w]+[^|]*|)/', $read_data, $name_matches);
            preg_match_all('/(a:[\w]+[^}]*})/', $read_data, $value_matches);

            if (!empty($name_matches)) {
                foreach ($name_matches[0] as $key => $val) {
                    if ($val == 'yunzshop_member_id') {
                        $member_data = $val . '|' . $value_matches[0][$key];
                    }
                }
            }
        }

        return $member_data;
    }
}