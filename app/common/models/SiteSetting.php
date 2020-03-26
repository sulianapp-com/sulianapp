<?php


namespace app\common\models;


class SiteSetting extends BaseModel
{
    protected $table = 'yz_site_setting';
    protected $guarded = [''];
    protected $casts = [
        'value' => 'json'
    ];
    /**
     * @var self
     */


}