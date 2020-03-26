<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/22
 * Time: 10:46
 */

namespace app\common\models;


class TemplateMessageDefault extends BaseModel
{
    public $table = 'yz_template_message_default';

    public function getData($template_id_short)
    {
        return self::uniacid()->where('template_id_short',$template_id_short)->first();
    }

    public static function delData($template_id)
    {
        return self::uniacid()->where('template_id',$template_id)->delete();
    }
}