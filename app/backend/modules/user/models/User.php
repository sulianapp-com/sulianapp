<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/11/1
 * Time: 9:53 AM
 */
namespace app\backend\modules\user\models;

class User extends \app\common\models\user\User
{
    static private $current;

    static function current(){
        if(!isset(self::$current)){
            self::$current = self::find(\YunShop::app()->uid);
        }
        return self::$current;
    }
}
